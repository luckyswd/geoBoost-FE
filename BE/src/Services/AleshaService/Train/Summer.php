<?php

namespace App\Services\AleshaService\Train;

class Summer implements BaseTrain
{
    public function getLabel(): string
    {
        return 'summer';
    }

    public function getSamples(): array
    {
        return [
            ['beach', 'sandals'],
            ['swim', 'trunks'],
            ['sun', 'hat'],
            ['flip-flops'],
            ['bikini'],
            ['summer', 'dress'],
            ['tank', 'top'],
            ['shorts'],
            ['sunglasses'],
            ['lightweight', 't-shirt'],
            ['water', 'shoes'],
            ['sun', 'protection', 'shirt'],
            ['board', 'shorts'],
            ['romper'],
            ['cover-up', 'dress'],
            ['casual', 'slip-ons'],
            ['light', 'jacket'],
            ['straw', 'tote', 'bag'],
            ['wedge', 'sandals'],
            ['broad-brimmed', 'hat'],
            ['flip-flop', 'sandals'],
            ['summer', 'hoodie'],
            ['cut-off', 'jeans'],
            ['polo', 'shirt'],
            ['athletic', 'shorts'],
            ['sleeveless', 'blouse'],
            ['linen', 'pants'],
            ['light', 'cardigan'],
            ['jumpsuit'],
            ['swim', 'cover-up'],
            ['canvas', 'sneakers'],
            ['summer', 'romper'],
            ['beach', 'umbrella'],
            ['pool', 'floaties'],
            ['lightweight', 'beach', 'towel'],
            ['travel', 'shorts'],
            ['sports', 'tank', 'top'],
            ['hiking', 'sandals'],
            ['mesh', 'shorts'],
            ['sun', 'shirt'],
            ['skorts'],
            ['tote', 'bag'],
            ['fishing', 'hat'],
            ['sundress'],
            ['canvas', 'backpack'],
            ['sweat-wicking', 'shirt'],
            ['cropped', 'tank', 'top'],
            ['light-weight', 'running', 'shoes'],
            ['summer', 'scarf'],
            ['colorful', 'flip-flops'],
        ];
    }
}
