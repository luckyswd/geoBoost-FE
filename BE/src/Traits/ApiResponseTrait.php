<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

trait ApiResponseTrait
{
    protected array $errors = [];
    protected array $meta = [];

    protected function success(
        array $data = [],
        int $statusCode = Response::HTTP_OK
    ): JsonResponse {
        return new JsonResponse(
            [
                'data' => empty($data) ? ['success'] : $data,
                'meta' => $this->meta
            ],
            $statusCode,
            [],
            false
        );
    }

    protected function error(
        string $message,
        int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR,
    ): JsonResponse
    {
        return new JsonResponse(
            ['errors' => $message],
            $statusCode,
            [],
            false
        );
    }

    protected function addMeta(
        string $key,
        mixed $value
    ): void {
        $this->meta[$key] = $value;
    }

    protected function getDomain(
        Request $request
    ): string {
        $domain = $request->get('domain') ?? $request->getPayload()->get('domain') ?? null;

        if (!$domain) {
            throw new BadRequestHttpException("The 'domain' parameter is missing.");
        }

        return $domain;
    }
}
