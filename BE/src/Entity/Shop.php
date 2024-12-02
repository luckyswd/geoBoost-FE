<?php

namespace App\Entity;

use App\Repository\ShopRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

#[ORM\Entity(repositoryClass: ShopRepository::class)]
#[ORM\Table(name: 'shop', uniqueConstraints: [new ORM\UniqueConstraint(name: 'shop_unique', columns: ['shop'])])]
#[ORM\HasLifecycleCallbacks]
class Shop
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'id', type: 'integer')]
    protected int $id;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: true)]
    protected ?DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: true)]
    protected ?DateTime $updatedAt;

    #[ORM\Column(type: 'string', length: 255, unique: true, nullable: false)]
    private string $domain;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private string $accessToken;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string $name;

    #[ORM\ManyToOne(targetEntity: Shop::class)]
    #[ORM\JoinColumn(name: 'sub_shop_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Shop $subShop = null;

    #[ORM\Column(type: 'string', length: 3, nullable: true)]
    private ?string $countryCode = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $countryName = null;

    #[ORM\Column(type: 'string', length: 3, nullable: true)]
    private ?string $language = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'boolean', nullable: true, options: ['default' => 0])]
    private ?bool $active = false;

    #[ORM\OneToMany(targetEntity: Setting::class, mappedBy: 'shop', cascade: ['persist', 'remove'])]
    private Collection $settings;

    #[ORM\OneToMany(targetEntity: Tag::class, mappedBy: 'shop', cascade: ['persist', 'remove'], fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    private Collection $tags;

    public function __construct()
    {
        $this->settings = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    public function getId(): ?int
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

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): self
    {
        $this->domain = $domain;

        return $this;
    }

    public function getSubShop(): ?Shop
    {
        return $this->subShop;
    }

    public function setSubShop(?Shop $subShop): self
    {
        $this->subShop = $subShop;

        return $this;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): self
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountryCode(?string $countryCode): self
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    public function getCountryName(): ?string
    {
        return $this->countryName;
    }

    public function setCountryName(?string $countryName): self
    {
        $this->countryName = $countryName;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getSettings(): Collection
    {
        return $this->settings;
    }

    public function addSetting(Setting $setting): void
    {
        $this->settings[] = $setting;

        $setting->setShop($this);
    }

    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): void
    {
        $this->tags[] = $tag;

        $tag->setShop($this);
    }

    public function removeTag(Tag $tag): void
    {
        if($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }
    }
}
