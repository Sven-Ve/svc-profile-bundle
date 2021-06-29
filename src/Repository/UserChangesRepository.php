<?php

namespace Svc\ProfileBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Svc\ProfileBundle\Entity\UserChanges;
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
}
