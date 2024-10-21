<?php

namespace App\Controller;

use App\Repository\ShopRepository;
use App\Services\Product\ProductService;
use App\Services\ShopLogger;
use App\Traits\ApiResponseTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/product')]
class ProductController extends AbstractController
{
    use ApiResponseTrait;

    #[Route('/', name: 'get_products', methods: ["GET"])]
    public function getProducts(
        Request $request,
        ShopRepository $shopRepository,
        ProductService $productService,
    ): JsonResponse {
        $domain = $this->getDomain($request);
        $shop = $shopRepository->findOneBy(['domain' => $domain]);

        if (!$shop) {
            ShopLogger::error($domain, "В БД не был найден shop");

            return $this->error('domain not found', Response::HTTP_NOT_FOUND);
        }

        try {
            return $this->success($productService->getProductResponse($request, $shop));
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }
}
