<?php
namespace App\Repository;

use App\Entity\Holiday;
use Doctrine\Persistence\ManagerRegistry;

class HolidayRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Holiday::class);
    }

    /**
     * Получить предстоящие праздники для указанной страны до указанной даты, включая указанную дату.
     *
     * @param string $country
     * @param \DateTime $endDate
     * @return Holiday[]
     */
    public function findUpcomingHolidaysForCountry(string $country, \DateTime $endDate): array
    {
        $currentDate = new \DateTime();

        return $this->createQueryBuilder('h')
            ->where('h.holidayDate >= :currentDate')
            ->andWhere('h.holidayDate <= :endDate')
            ->andWhere('h.country = :country')
            ->setParameter('currentDate', $currentDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('country', $country)
            ->orderBy('h.holidayDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function searchHolidays(
        int $page,
        int $limit = 12,
        ?string $search = null,
        ?string $country = null,
        ?string $year = null
    ): array {
        $builder = $this->createQueryBuilder('h');

        if ($search) {
            $builder->where($builder->expr()->like('LOWER(h.name)', ':search'))
                ->setParameter('search', '%' . strtolower($search) . '%');
        }

        if ($country) {
            $builder->andWhere('h.country = :country')
                ->setParameter('country', $country);
        }

        if ($year) {
            $builder->andWhere('h.year = :year')
                ->setParameter('year', $year);
        }

        return $this->paginate($builder, $page, $limit);
    }


    public function findCountries(): array
    {
        $countries = $this->createQueryBuilder('h')
            ->select('DISTINCT h.country')
            ->getQuery()
            ->getResult();

        return array_map(fn($item) => $item['country'], $countries);
    }

    public function findYears(): array
    {
        $years = $this->createQueryBuilder('h')
            ->select('DISTINCT h.year')
            ->getQuery()
            ->getResult();

        return array_map(fn($item) => (string) $item['year'], $years);
    }
}
