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

    /**
     * Поиск праздников с пагинацией и возможностью фильтрации по названию.
     *
     * @param int $page Номер страницы для пагинации
     * @param int $limit Количество записей на странице (по умолчанию 12)
     * @param string|null $search Строка поиска для фильтрации по названию праздника (опционально)
     *
     * @return array Возвращает массив с пагинированными результатами, включая стандартные и пользовательские теги для каждого праздника
     *
     * @throws \Exception Если произошла ошибка при выполнении запроса
     */
    public function searchHolidays(int $page, int $limit = 12, ?string $search = null): array
    {
        $builder = $this->createQueryBuilder('h')
            ->leftJoin('h.defaultTag', 'dt')
            ->leftJoin('h.tags', 't')
            ->addSelect('dt')
            ->addSelect('t');

        if ($search) {
            $builder->where($builder->expr()->like('LOWER(h.name)', ':search'))
                ->setParameter('search', '%' . strtolower($search) . '%');
        }

        return $this->paginate($builder, $page, $limit);
    }
}
