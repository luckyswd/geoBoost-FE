<?php
namespace App\Entity;

use App\Repository\DefaultTagRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DefaultTagRepository::class)]
class DefaultTag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $tags;

    #[ORM\OneToOne(targetEntity: Holiday::class, mappedBy: 'defaultTag')]
    private ?Holiday $holiday = null;

    public function setId(int $id): self {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getHoliday(): ?Holiday
    {
        return $this->holiday;
    }

    public function setHoliday(?Holiday $holiday): self
    {
        $this->holiday = $holiday;

        return $this;
    }
}
