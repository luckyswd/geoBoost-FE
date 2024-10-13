<?php

namespace App\Entity;

use App\Repository\SettingRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SettingRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Setting
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'id', type: 'integer')]
    protected int $id;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: true)]
    protected ?DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: true)]
    protected ?DateTime $updatedAt;

    #[ORM\Column(name: '`key`', type: 'string', length: 255)]
    protected string $key;

    #[ORM\Column(name: 'value', type: 'text')]
    protected mixed $value = null;

    #[ORM\ManyToOne(targetEntity: Shop::class, inversedBy: 'settings')]
    #[ORM\JoinColumn(nullable: false)]
    private Shop $shop;

    public function getId(): int
    {
        return $this->id;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): self
    {
        $this->createdAt = new DateTime('now');

        return $this;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAt(): self
    {
        $this->updatedAt = new DateTime('now');

        return $this;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function setValue(mixed $value): self
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }

        if (is_bool($value)) {
            $value = $value ? 1 : 0;
        }

        $this->value = $value;

        return $this;
    }

    public function getValue(): mixed
    {
        $decodedValue = json_decode($this->value, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decodedValue)) {
            return $decodedValue;
        }

        if ($this->value === '1') {
            $this->value = 1;
        }

        if ($this->value === '0') {
            $this->value = 0;
        }

        return $this->value;
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
}
