<?php

namespace App\Services\Shop;

use App\Entity\Shop;
use App\Services\ShopLogger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ShopService
{
    public static function getShop(
        Request $request,
        EntityManagerInterface $em
    ): Shop {
        $domain = $request->get('domain') ?? $request->getPayload()->get('domain') ?? null;

        if (!$domain) {
            throw new BadRequestHttpException("The 'domain' parameter is missing.");
        }

        $shopRepository = $em->getRepository(Shop::class);
        $shop = $shopRepository->findOneBy(['domain' => $domain]);

        if (!$shop) {
            ShopLogger::error($domain, "В БД не был найден shop");

            throw new NotFoundHttpException("Shop not found");
        }

        return $shop;
    }
}