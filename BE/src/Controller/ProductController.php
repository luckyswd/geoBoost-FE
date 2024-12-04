<?php

namespace App\Controller;

use App\Services\Product\ProductService;
use App\Traits\ApiResponseTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/product')]
class ProductController extends AbstractController
{
    use ApiResponseTrait;

    #[Route('/', name: 'get_products', methods: ["GET"])]
    public function getProducts(
        Request $request,
        ProductService $productService,
    ): JsonResponse {
        try {
            return $this->success($productService->getProductResponse($request));
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }
}
