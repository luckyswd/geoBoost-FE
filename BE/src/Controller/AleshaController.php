<?php

namespace App\Controller;

use App\Services\AleshaService\AleshaService;
use Phpml\Exception\FileException;
use Phpml\Exception\SerializeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AleshaController extends AbstractController
{
    /**
     * Метод для обучения модели.
     * @param AleshaService $aleshaService Сервис для работы с обучением модели.
     *
     * @return JsonResponse
     */
    #[Route('/alesha/train', name: 'alesha_train')]
    public function trainModel(
        AleshaService $aleshaService,
    ): JsonResponse
    {
        try {
            $aleshaService->trainModel();

            return $this->json([
                'status' => 'Success',
                'Message' => 'The model was successfully trained on the data from the files and saved.'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'Error',
                'Message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Метод для предсказания сезона на основе текста.
     * @param AleshaService $aleshaService Сервис для работы с обучением модели.
     *
     * @return JsonResponse
     */
    #[Route('/alesha/predict', name: 'alesha_predict')]
    public function predict(
        AleshaService $aleshaService,
    ): JsonResponse
    {
        $text = 'Leather Jacket A stylish and warm jacket for autumn days.';
        try {
            $prediction = $aleshaService->predict(mb_strtolower($text));

            return $this->json($prediction[0]);
        } catch (FileException|SerializeException $e) {
            return $this->json([
                'status' => 'Error',
                'Message' => $e->getMessage()
            ]);
        }
    }
}