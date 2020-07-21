<?php

namespace App\Service;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;

class PaginationService
{
    /**
     * @param $query
     * @param $currentPage
     * @param int $limit
     * @return Paginator
     */
    public function paginate($query, $currentPage, int $limit): Paginator
    {
        $paginator = new Paginator($query);
        $paginator
            ->getQuery()
            ->setFirstResult($limit * ($currentPage - 1))
            ->setMaxResults($limit);

        return $paginator;
    }

    /**
     * @param Paginator $paginator
     * @return int
     */
    public function lastPage(Paginator $paginator): int
    {
        return ceil($paginator->count() / $paginator->getQuery()->getMaxResults());
    }

    /**
     * @param Paginator $paginator
     * @return int
     */
    public function total(Paginator $paginator): int
    {
        return $paginator->count();
    }

    /**
     * @param Paginator $paginator
     * @return bool
     * @throws \Exception
     */
    public function currentPageHasNoResult(Paginator $paginator): bool
    {
        return !$paginator->getIterator()->count();
    }

    /**
     * @param Paginator $paginator
     * @return int
     */
    public function getMaxPages(Paginator $paginator) :int
    {
        return $paginator->getQuery()->getMaxResults();
    }


}