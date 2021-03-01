<?php

namespace Svc\ProfileBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Svc\ProfileBundle\Repository\UserChangesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Private class to support profile changes
 *
 * @author Sven Vetter <dev@sv-systems.com>
 */
class ChangeMailHelper extends AbstractController
{
  
  private const TOKENLIFETIME = 3600;
  private $userChageRep;
  private $entityManager;

  
  public function __construct(UserChangesRepository $userChageRep, EntityManagerInterface $entityManager)
  {
    $this->userChageRep = $userChageRep;
    $this->entityManager = $entityManager;
  }

  public function checkExpiredRequest($user) {
    $entry = $this->userChageRep->findOneBy(["user"=>$user]);

    if (!$entry) {
      return true;
    }

    $expiresAt = new \DateTimeImmutable(\sprintf('+%d seconds', static::TOKENLIFETIME));

    if ($entry->getExpiresAt() > new \DateTimeImmutable()) {
      return false;
    }

    $this->entityManager->remove($entry);
    $this->entityManager->flush();
    return true;

  }

}