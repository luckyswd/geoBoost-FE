<?php

namespace App\Services\AleshaService\Train;

class Spring implements BaseTrain
{
    public function getLabel(): string
    {
        return 'spring';
    }

    public function getSamples(): array
    {
        return [
            ['spring', 'shoes'],
            ['light', 'jacket'],
            ['denim', 'jacket'],
            ['cardigan'],
            ['sneakers'],
            ['floral', 'dress'],
            ['capri', 'pants'],
            ['t-shirt'],
            ['spring', 'scarf'],
            ['lightweight', 'sweater'],
            ['linen', 'trousers'],
            ['ballet', 'flats'],
            ['cargo', 'shorts'],
            ['windbreaker'],
            ['spring', 'sandals'],
            ['hiking', 'shoes'],
            ['cropped', 'pants'],
            ['short-sleeve', 'shirt'],
            ['maxi', 'dress'],
            ['activewear'],
            ['spring', 'blouse'],
            ['sundress'],
            ['light', 'cardigan'],
            ['colorful', 'sneakers'],
            ['spring', 'vest'],
            ['boat', 'shoes'],
            ['polo', 'shirt'],
            ['lightweight', 'hoodie'],
            ['pullover'],
            ['chinos'],
            ['ankle', 'pants'],
            ['tunic', 'top'],
            ['oversized', 'shirt'],
            ['spring', 'poncho'],
            ['canvas', 'shoes'],
            ['sweatshirt'],
            ['spring', 'hat'],
            ['active', 'shorts'],
            ['mesh', 'top'],
            ['trench', 'coat'],
            ['fitted', 'jeans'],
            ['sport', 'sandals'],
            ['layered', 'tops'],
            ['water-resistant', 'jacket'],
            ['comfortable', 'flats'],
            ['beach', 'cover-up'],
            ['lightweight', 'windbreaker'],
            ['floral', 'blouse'],
            ['joggers'],
            ['spring', 'kimono'],
            ['sunglasses'],
        ];
    }
}
