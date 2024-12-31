<?php

namespace App\Services\Product;

use App\Entity\HolidayProduct;
use App\Repository\HolidayProductRepository;
use App\Repository\HolidayRepository;
use App\Services\Shop\ShopService;
use App\Services\Shopify\ShopifyApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ProductService
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private HolidayRepository $holidayRepository,
        private HolidayProductRepository $holidayProductRepository,
    ) {}

    public function getProductResponse(Request $request): array
    {
        $shop = ShopService::getShop($request, $this->entityManager);
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
                'holidayNames' => $this->getHolidayNames($product['metafields']['edges']),
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

    private function getHolidayNames(array $metaFields): array
    {
        foreach ($metaFields as $metaFieldEdge) {
            if ($metaFieldEdge['node']['key'] === 'holiday_names') {
                return explode(',', $metaFieldEdge['node']['value']);
            }
        }

        return [];
    }

    private function getCollectionsForProduct(array $collections): array
    {
        return array_map(function ($collectionEdge) {
            $collection = $collectionEdge['node'];
            $parts = explode('/', $collection['id']);

            return [
                'id' => end($parts),
                'title' => $collection['title'],
            ];
        }, $collections['edges']);
    }

    public function setHolidayProduct(Request $request): void
    {
        $shop = ShopService::getShop($request, $this->entityManager);
        $productId = $request->getPayload()->get('productId');
        $holidayNames = $request->getPayload()->get('holidayNames');

        if (!$productId) {
            throw new BadRequestHttpException("The 'productId' parameter is missing.");
        }

        if (!$holidayNames) {
            throw new BadRequestHttpException("The 'holidayNames' parameter is missing.");
        }

        $holidayProducts = $this->holidayProductRepository->findBy([
            'shop' => $shop,
            'productId' => $productId,
        ]);

        foreach ($holidayProducts as $product) {
            $this->entityManager->remove($product);
        }

        $this->entityManager->flush();

        $holidayNames = explode(',', $holidayNames);

        foreach ($holidayNames as $holidayName) {
            $isExistHoliday = $this->holidayRepository->findBy(['name' => $holidayName]);

            if (!$isExistHoliday) {
                continue;
            }

            $holidayProduct = (new HolidayProduct())
                ->setProductId($productId)
                ->setShop($shop)
                ->setHolidayName($holidayName);

            $this->entityManager->persist($holidayProduct);
        }

        $this->entityManager->flush();
    }
}