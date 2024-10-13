<?php
namespace App\Repository;

use App\Entity\Holiday;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class HolidayRepository extends ServiceEntityRepository
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

}
