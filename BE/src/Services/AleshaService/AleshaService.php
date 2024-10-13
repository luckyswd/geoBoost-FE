<?php

namespace App\Services\AleshaService;

use Phpml\Classification\NaiveBayes;
use Phpml\Exception\FileException;
use Phpml\Exception\SerializeException;
use Phpml\ModelManager;

class AleshaService
{
    private string $modelPath;

    public function __construct(string $projectDir)
    {
        $this->modelPath = sprintf('%s%s', $projectDir, '/files/ML/models/season_model');
    }

    /**
     * Обучает модель на основе образцов и меток.
     *
     * @throws SerializeException Если возникла ошибка сериализации
     * @throws FileException Если возникла ошибка с файлом
     * @return void
     */
    public function trainModel(): void
    {
        $trains = $this->getTrains();
        $samples = [];
        $labels = [];

        foreach ($trains as $train) {
            $modelInstance = new $train();
            $modelSamples = $modelInstance->getSamples();
            $modelLabel = $modelInstance->getLabel();

            foreach ($modelSamples as $sample) {
                $samples[] = explode(' ', $sample[0]);
                $labels[] = $modelLabel;
            }
        }

        $classifier = new NaiveBayes();
        $classifier->train($samples, $labels);

        $modelManager = new ModelManager();
        $modelManager->saveToFile($classifier, $this->modelPath);
    }

    /**
     * Предсказывает тег на основе введенного текста.
     *
     * @param string $text Входной текст для предсказания
     * @throws FileException Если возникла ошибка с файлом
     * @throws SerializeException Если возникла ошибка сериализации
     * @return mixed Результат предсказания
     */
    public function predict(string $text): mixed
    {
        $modelManager = new ModelManager();
        $classifier = $modelManager->restoreFromFile($this->modelPath);
        $tokens = explode(' ', $text);

        return $classifier->predict([$tokens]);
    }

    /**
     * Получает список классов тренировок из директории.
     *
     * @return array Массив с классами тренировок
     */
    private function getTrains(): array
    {
        $models = [];
        $directory = new \DirectoryIterator(__DIR__ . '/Train');

        foreach ($directory as $fileinfo) {
            if ($fileinfo->isFile() && $fileinfo->getExtension() === 'php') {
                $className = 'App\Services\AleshaService\Train\\' . pathinfo($fileinfo->getFilename(), PATHINFO_FILENAME);
                if (class_exists($className)) {
                    $models[] = $className;
                }
            }
        }

        return $models;
    }
}
