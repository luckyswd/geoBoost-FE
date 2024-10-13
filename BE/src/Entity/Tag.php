<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Shop::class, inversedBy: 'tags')]
    #[ORM\JoinColumn(nullable: false)]
    private Shop $shop;

    #[ORM\ManyToOne(targetEntity: Holiday::class, inversedBy: 'holidays')]
    #[ORM\JoinColumn(nullable: false)]
    private Holiday $holiday;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $tags;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShop(): Shop
    {
        return $this->shop;
    }

    public function setShop(Shop $shop): self
    {
        $this->shop = $shop;

        return $this;
    }

    public function getHoliday(): Holiday
    {
        return $this->holiday;
    }

    public function setHoliday(Holiday $holiday): self
    {
        $this->holiday = $holiday;

        return $this;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function setTags(?array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }
}
