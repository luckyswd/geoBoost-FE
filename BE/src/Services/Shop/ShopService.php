<?php

namespace App\Services\Shop;

use App\Entity\Shop;
use App\Kernel;
use App\Services\ShopLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ShopService
{
    public static function getShop(Request $request): Shop
    {
        $domain = $request->get('domain') ?? $request->getPayload()->get('domain') ?? null;

        if (!$domain) {
            throw new BadRequestHttpException("The 'domain' parameter is missing.");
        }

        $kernel = new Kernel(getenv('APP_ENV'), true);
        $kernel->boot();

        $container = $kernel->getContainer();
        $doctrine = $container->get('doctrine');
        $em = $doctrine->getManager();

        $shopRepository = $em->getRepository(Shop::class);

        $shop = $shopRepository->findOneBy(['domain' => $domain]);

        if (!$shop) {
            ShopLogger::error($domain, "В БД не был найден shop");

            throw new NotFoundHttpException("shop not found");
        }

        return $shop;
    }
}