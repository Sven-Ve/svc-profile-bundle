<?php

namespace Svc\ProfileBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Svc\ProfileBundle\Repository\UserChangesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ChangeMailHelper extends AbstractController
{
  private $rep;
  private $entityManager;

  public function __construct(UserChangesRepository $rep, EntityManagerInterface $entityManager)
  {
    $this->rep = $rep;
    $this->entityManager = $entityManager;
  }

  public function checkExpiredRequest($user) {
    $res = $this->rep->findOneBy(["user"=>$user]);
    dump($res);
    $this->entityManager->remove($res);
    $this->entityManager->flush();

    die("Hoer");
  }

}