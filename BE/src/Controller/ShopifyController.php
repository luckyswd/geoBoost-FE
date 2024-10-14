<?php

namespace App\Controller;

use App\Entity\Shop;
use App\Enum\Scopes;
use App\Repository\ShopRepository;
use App\Services\Cache\Redis;
use App\Services\Setting\SettingService;
use App\Services\Shopify\ShopifyApiService;
use App\Services\Shopify\ShopifyService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ShopifyController extends AbstractController
{
    #[Route('/', name: 'shopify_authorize', methods: ['GET'])]
    public function authorize(Request $request): RedirectResponse
    {
        $shop = $request->get('shop');

        if (!$shop) {
            throw new Exception('Shop parameter is missing.');
        }

        $apiKey = getenv('SHOPIFY_API_KEY');
        $redirectUri = getenv('SHOPIFY_REDIRECTION_URI');
        $scopes = Scopes::getAsCommaSeparated();

        $installUrl = "https://$shop/admin/oauth/authorize?client_id=$apiKey&scope=$scopes&redirect_uri=$redirectUri";

        return new RedirectResponse($installUrl);
    }

    #[Route('/shopify/callback', name: 'shopify_callback')]
    public function callback(
        Request $request,
        HttpClientInterface $httpClient,
        EntityManagerInterface $entityManager,
        ShopRepository $shopRepository,
        ShopifyService $shopifyService,
        SettingService $settingService,
    ): Response {
        $domain = $request->get('shop') ?? null;
        $code = $request->get('code') ?? null;
        $hmac = $request->get('hmac') ?? null;
        $query = $request->query->all();

        ShopifyService::shopifyInstallValidation($domain, $code, $hmac, $query);

        $shopifyApiKey = getenv('SHOPIFY_API_KEY');
        $redisKeyAccessToken = "$domain:access_token";

        if (!Redis::get($redisKeyAccessToken)) {
            $accessTokenResponse = $httpClient->request('POST', "https://$domain/admin/oauth/access_token", [
                'json' => [
                    'client_id' => $shopifyApiKey,
                    'client_secret' => getenv('SHOPIFY_SECRET_KEY'),
                    'code' => $code,
                ]
            ]);

            $responseData = $accessTokenResponse->toArray();
            $accessToken = $responseData['access_token'] ?? null;
            Redis::set($redisKeyAccessToken, $accessToken);
        } else {
            $accessToken = Redis::get($redisKeyAccessToken);
        }

        $shop = $shopRepository->findOneBy(['domain' => $domain]);

        if ($shop && $shop->getActive()) {
            return new RedirectResponse(getenv('SHOPIFY_FRONT_URL'));
        }

        if (!$shop) {
            $shop = new Shop();
            $settingService->setDefaultSetting($shop);
        }

        $shop->setDomain($domain);
        $shop->setAccessToken($accessToken);

        $response = ShopifyApiService::client($shop)->get('shop.json')->getDecodedBody();

        if (isset($response['errors'])) {
            #TODO add logs
            throw new Exception($response['errors']);
        }

        $responseShop = $response['shop'];
        $shop
            ->setName($responseShop['name'])
            ->setEmail($responseShop['email'])
            ->setCountryCode($responseShop['country_code'])
            ->setCountryName($responseShop['country_name'])
            ->setLanguage($responseShop['primary_locale'])
            ->setActive(true);

        $entityManager->persist($shop);
        $entityManager->flush();
        $shopifyService->registerAppUninstalledWebhook($shop);

        $redirectUrl = "https://$domain/admin/apps/$shopifyApiKey";

        return new RedirectResponse($redirectUrl);
    }

    #[Route('/shopify/webhook/uninstalled', name: 'shopify_uninstalled', methods: ['POST'])]
    public function uninstalled(
        Request $request,
        ShopRepository $shopRepository,
        EntityManagerInterface $entityManager,
    ): Response {
        $data = json_decode($request->getContent(), true);
        $domain = $data['domain'] ?? null;

        if (!$domain) {
            return new Response('Invalid webhook', Response::HTTP_BAD_REQUEST);
        }

        $shop = $shopRepository->findOneBy(['domain' => $domain]);
        $shop->setActive(false);
        $entityManager->flush();

        //Инвалидация access_token для shop, при удалени приложения
        Redis::delete("$domain:access_token");

        return new Response('Webhook received', Response::HTTP_OK);
    }
}
