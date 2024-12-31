<?php
namespace App\Entity;

use App\Repository\HolidayRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: HolidayRepository::class)]
#[ORM\Table(name: 'holiday')]
#[ORM\UniqueConstraint(name: 'holiday_unique', columns: ['name', 'year', 'country', 'holiday_date'])]
#[ORM\HasLifecycleCallbacks]
class Holiday
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'id', type: 'integer')]
    #[Groups(['Holiday.all'])]
    protected int $id;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: true)]
    protected ?DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: true)]
    protected ?DateTime $updatedAt;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['Holiday.all'])]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['Holiday.all'])]
    private string $country;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['Holiday.all'])]
    private int $year;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string $timezone;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $type;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $translations;

    #[ORM\Column(type: 'datetime', nullable: false)]
    #[Groups(['Holiday.all'])]
    private DateTime $holidayDate;

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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getTimezone(): string
    {
        return $this->timezone;
    }

    public function setTimezone(string $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getTranslations(): ?array
    {
        return $this->translations;
    }

    public function setTranslations(?array $translations): self
    {
        $this->translations = $translations;

        return $this;
    }

    public function getHolidayDate(): DateTime
    {
        return $this->holidayDate;
    }

    public function setHolidayDate(DateTime $holidayDate): self
    {
        $this->holidayDate = $holidayDate;

        return $this;
    }
}
