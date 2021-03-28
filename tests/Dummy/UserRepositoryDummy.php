<?php

namespace App\Repository;
require_once(__dir__ . "/UserDummy.php");

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping as ORM;

/**
 * @method Params|null find($id, $lockMode = null, $lockVersion = null)
 * @method Params|null findOneBy(array $criteria, array $orderBy = null)
 * @method Params[]    findAll()
 * @method Params[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{

  public function __construct(ManagerRegistry $registry)
  {
//    parent::__construct($registry, User::class); // ignore parent constructor because user entity doesn't exits in Test
  }

  private $id;
}
