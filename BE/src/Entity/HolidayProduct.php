<?php

namespace App\Entity;

use App\Repository\HolidayProductRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HolidayProductRepository::class)]
#[ORM\Table(name: 'holiday_product')]
#[ORM\UniqueConstraint(name: 'unique_holiday_product', columns: ['holiday_name', 'shop_id', 'product_id'])]
#[ORM\Index(name: 'idx_shop_holiday', columns: ['shop_id', 'holiday_name'])]
#[ORM\HasLifecycleCallbacks]
class HolidayProduct
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $holidayName;

    #[ORM\ManyToOne(targetEntity: Shop::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Shop $shop;

    #[ORM\Column(type: 'bigint')]
    private int $productId;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHolidayName(): string
    {
        return $this->holidayName;
    }

    public function setHolidayName(string $holidayName): self
    {
        $this->holidayName = $holidayName;

        return $this;
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

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function setProductId(int $productId): self
    {
        $this->productId = $productId;

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): self
    {
        $this->createdAt = new DateTime('now');

        return $this;
    }
}
