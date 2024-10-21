<?php

namespace App\Services\Product;

use App\Entity\Shop;
use App\Services\Shopify\ShopifyApiService;
use Symfony\Component\HttpFoundation\Request;

class ProductService
{
    public function getProductResponse(
        Request $request,
        Shop $shop,
    ): array {
        $after = $request->query->get('after');
        $before = $request->query->get('before');
        $search = $request->query->get('s');
        $collectionId = $request->query->get('collectionId');
        $productsPerPage = 12;

        $graphQLQuery = $this->buildGraphQLQuery($productsPerPage, $after, $before, $search, $collectionId);

        $shopifyResponse = ShopifyApiService::client($shop, true)->query($graphQLQuery)->getDecodedBody();

        $products = $shopifyResponse['data']['products']['edges'];
        $pageInfo = $shopifyResponse['data']['products']['pageInfo'];

        $formattedProducts = array_map(function ($productNode) {
            $product = $productNode['node'];
            $parts = explode('/', $product['id']);

            return [
                'id' => end($parts),
                'handle' => $product['handle'],
                'title' => $product['title'],
                'status' => $product['status'],
                'holidayTags' => $this->getHolidayTags($product['metafields']['edges']),
                'collection' => $this->getCollectionsForProduct($product['collections'])
            ];
        }, $products);

        return [
            'products' => $formattedProducts,
            'pageInfo' => [
                'hasNextPage' => $pageInfo['hasNextPage'],
                'hasPreviousPage' => $pageInfo['hasPreviousPage'],
                'endCursor' => $pageInfo['endCursor'],
                'startCursor' => $pageInfo['startCursor']
            ],
        ];
    }

    private function buildGraphQLQuery(
        $productsPerPage,
        $after = null,
        $before = null,
        $search = null,
        $collectionId = null
    ): string {
        $filters = [];

        if ($search) {
            $filters[] = "title:*$search*";
        }
        if ($collectionId) {
            $filters[] = "collection_id:$collectionId";
        }

        $filterString = !empty($filters) ? 'query: "' . implode(' AND ', $filters) . '"' : '';

        $params = [];

        if ($after) {
            $params[] = "first: $productsPerPage";
            $params[] = "after: \"$after\"";
        } elseif ($before) {
            $params[] = "last: $productsPerPage";
            $params[] = "before: \"$before\"";
        } else {
            $params[] = "first: $productsPerPage";
        }

        if ($filterString) {
            $params[] = $filterString;
        }

        $paramsString = implode(', ', $params);

        return "
            {
                products($paramsString) {
                    edges {
                        node {
                            id
                            handle
                            title
                            status
                            collections(first: 250) {
                                edges {
                                    node {
                                        id
                                        title
                                    }
                                }
                            }
                            metafields(first: 250, namespace: \"holiday_tags\") {
                                edges {
                                    node {
                                        key
                                        value
                                    }
                                }
                            }
                        }
                        cursor
                    }
                    pageInfo {
                        hasNextPage
                        hasPreviousPage
                        endCursor
                        startCursor
                    }
                }
            }
        ";
    }


    private function getHolidayTags(array $metaFields): array {
        foreach ($metaFields as $metaFieldEdge) {
            if ($metaFieldEdge['node']['key'] === 'holiday_tags') {
                return explode(',', $metaFieldEdge['node']['value']);
            }
        }

        return [];
    }

    private function getCollectionsForProduct(array $collections): array {
        return array_map(function ($collectionEdge) {
            $collection = $collectionEdge['node'];
            $parts = explode('/', $collection['id']);

            return [
                'id' => end($parts),
                'title' => $collection['title'],
            ];
        }, $collections['edges']);
    }
}