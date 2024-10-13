<?php

namespace App\Services\AleshaService\Train;

class Winter implements BaseTrain
{
    public function getLabel(): string
    {
        return 'winter';
    }

    public function getSamples(): array
    {
        return [
            ['winter', 'boots'],
            ['down', 'jacket'],
            ['thermal', 'gloves'],
            ['wool', 'hat'],
            ['scarves'],
            ['sweaters'],
            ['heavy', 'coat'],
            ['thermal', 'leggings'],
            ['snow', 'pants'],
            ['fleece-lined', 'hoodie'],
            ['insulated', 'parka'],
            ['winter', 'gloves'],
            ['beanie'],
            ['ear', 'muffs'],
            ['puffer', 'jacket'],
            ['long-sleeve', 'shirt'],
            ['turtleneck', 'sweater'],
            ['wool', 'socks'],
            ['flannel', 'shirt'],
            ['winter', 'scarf'],
            ['waterproof', 'gloves'],
            ['thermal', 'socks'],
            ['sherpa-lined', 'jacket'],
            ['snow', 'boots'],
            ['hooded', 'parka'],
            ['base', 'layers'],
            ['winter', 'running', 'shoes'],
            ['fleece', 'vest'],
            ['windbreaker'],
            ['insulated', 'snow', 'pants'],
            ['cable-knit', 'sweater'],
            ['heavy', 'fleece', 'jacket'],
            ['fleece-lined', 'leggings'],
            ['winter', 'coat'],
            ['wool', 'sweater'],
            ['snowshoes'],
            ['ski', 'gloves'],
            ['warm', 'hat'],
            ['teddy', 'coat'],
            ['thermal', 'underwear'],
            ['wool-lined', 'boots'],
            ['down', 'vest'],
            ['sweatpants'],
            ['thermal', 'top'],
            ['insulated', 'jacket'],
            ['puffer', 'vest'],
            ['ski', 'jacket'],
            ['winter', 'poncho'],
            ['faux', 'fur', 'coat'],
            ['cold', 'weather'],
            ['winter', 'warm', 'boots'],
            ['overcoat'],
        ];
    }
}
