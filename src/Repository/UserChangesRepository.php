<?php

namespace Svc\ProfileBundle\Repository;

use Svc\ProfileBundle\Entity\UserChanges;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserChanges|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserChanges|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserChanges[]    findAll()
 * @method UserChanges[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserChangesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserChanges::class);
    }

    // /**
    //  * @return UserChanges[] Returns an array of UserChanges objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserChanges
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
