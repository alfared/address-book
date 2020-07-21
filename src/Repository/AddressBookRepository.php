<?php

namespace App\Repository;

use App\Entity\AddressBook;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AddressBook|null find($id, $lockMode = null, $lockVersion = null)
 * @method AddressBook|null findOneBy(array $criteria, array $orderBy = null)
 * @method AddressBook[]    findAll()
 * @method AddressBook[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AddressBookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AddressBook::class);
    }

    /**
     * @param $inputs
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function store($inputs)
    {

        $this->_em->persist($inputs);
        $this->_em->flush();
    }

    /**
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public function listAll()
    {
        $developers = $this->_em->getRepository(AddressBook::class);

        // build the query for the doctrine paginator
        $query = $developers->createQueryBuilder('u')
            ->orderBy('u.id', 'DESC')
            ->getQuery();

        return $query;
    }

    /**
     * @param $id
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove($id)
    {
        $addressBook = $this->find($id);
        $this->_em->remove($addressBook);
        $this->_em->flush();
    }

}
