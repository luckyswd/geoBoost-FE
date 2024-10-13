<?php

namespace App\Services\AleshaService\Train;

class Autumn implements BaseTrain
{
    public function getLabel(): string
    {
        return 'autumn';
    }

    public function getSamples(): array
    {
        return [
            ['leather', 'jacket'],
            ['wool', 'sweater'],
            ['autumn', 'coat'],
            ['knitted', 'scarf'],
            ['beanie', 'hat'],
            ['rain', 'boots'],
            ['corduroy', 'pants'],
            ['flannel', 'shirt'],
            ['heavy', 'cardigan'],
            ['fall', 'leggings'],
            ['fall', 'scarf'],
            ['hiking', 'boots'],
            ['long-sleeve', 'shirt'],
            ['windbreaker'],
            ['puffer', 'jacket'],
            ['fleece-lined', 'leggings'],
            ['sweatshirt'],
            ['trench', 'coat'],
            ['fall', 'dress'],
            ['thermal', 'top'],
            ['autumnal', 'sweater'],
            ['chemisier', 'dress'],
            ['cable', 'knit', 'sweater'],
            ['chukka', 'boots'],
            ['tweed', 'blazer'],
            ['plaid', 'scarf'],
            ['autumn', 'hat'],
            ['chunky', 'knit', 'cardigan'],
            ['shackets'],
            ['layered', 'outfits'],
            ['faux', 'fur', 'vest'],
            ['over-the-knee', 'boots'],
            ['bomber', 'jacket'],
            ['cardigan', 'sweater'],
            ['cashmere', 'sweater'],
            ['thermal', 'leggings'],
            ['fall', 'beanie'],
            ['jean', 'jacket'],
            ['v-neck', 'sweater'],
            ['fall', 'footwear'],
            ['ankle', 'boots'],
            ['turtleneck', 'sweater'],
            ['heavy', 'knit', 'hat'],
            ['padded', 'coat'],
            ['oversized', 'sweater'],
            ['printed', 'scarf'],
            ['fall', 'gloves'],
            ['cozy', 'socks'],
            ['suede', 'boots'],
            ['parka', 'jacket'],
            ['rain', 'jacket'],
            ['fall', 'poncho'],
            ['utility', 'jacket'],
            ['golf', 'jacket'],
            ['plaid', 'shirt'],
            ['sweater', 'dress'],
        ];
    }
}
