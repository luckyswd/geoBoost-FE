<?php

namespace App\Services\AleshaService\Train;

interface BaseTrain
{
    function getLabel(): string;
    function getSamples(): array;
}