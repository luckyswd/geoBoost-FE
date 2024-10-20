<?php

namespace App\Repository;

use App\Entity\Shop;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Shop>
 */
abstract class BaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, string $entityClass)
    {
        parent::__construct($registry, $entityClass);
    }

    /**
     * @throws \Exception
     */
    protected function paginate(QueryBuilder $queryBuilder, int $page = 1, int $limit = 10): array
    {
        $queryBuilder
            ->setFirstResult(($page - 1) * $limit) // offset
            ->setMaxResults($limit); // limit

        $paginator = new Paginator($queryBuilder);

        return [
            'data' => iterator_to_array($paginator->getIterator()),
            'totalCount' => $paginator->count(),
            'page' => $page,
            'totalPages' => ceil($paginator->count() / $limit),
        ];
    }
}
