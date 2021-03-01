<?php

namespace Svc\ProfileBundle\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Svc\ProfileBundle\Entity\UserChanges;
use Svc\ProfileBundle\Repository\UserChangesRepository;
use Svc\UtilBundle\Service\EnvInfoHelper;
use Svc\UtilBundle\Service\MailerHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Private class to support profile changes
 *
 * @author Sven Vetter <dev@sv-systems.com>
 */
class ChangeMailHelper extends AbstractController
{
  
  private const TOKENLIFETIME = 3600;
  private const SECRETKEY = "23573BE852F6D1C73B314809E940F19F3D00EF1CD99147462861BB714E68DCC1";
  private $userChageRep;
  private $entityManager;
  private $mailerHelper;
  private $token;

  
  public function __construct(UserChangesRepository $userChageRep, EntityManagerInterface $entityManager, MailerHelper $mailerHelper)
  {
    $this->userChageRep = $userChageRep;
    $this->entityManager = $entityManager;
    $this->mailerHelper = $mailerHelper;
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

  public function writeUserChangeRecord(User $user, $newMail) {
    $expiresAt = new \DateTimeImmutable(\sprintf('+%d seconds', static::TOKENLIFETIME));

    $change = new UserChanges();
    $change->setUser($user);
    $change->setChangeType(1);
    $change->setExpiresAt($expiresAt);
    $change->setNewMail($newMail);

    $token = $this->getToken();
    $change->setHashedToken($this->getTokenHash($token));

    // $entityManager = $this->getDoctrine()->getManager();
    $this->entityManager->persist($change);
    $this->entityManager->flush();
  }


  public function sendActivationMail($newMail) {
    $token = $this->getToken();
    $tokenHash = $this->getTokenHash($token);
    $url=$this->generateUrl('svc_profile_change_mail_activate', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

    $html=$this->renderView("@SvcProfile/profile/changeMail/MT_activateMail.html.twig", ["url" => $url]);
    return $this->mailerHelper->send($newMail,"Activate new mail address", $html);
  }

  public function sendActivationDoneMail($oldMail, $newMail) {
    $url=EnvInfoHelper::getURLtoIndexPhp();

    $html=$this->renderView("@SvcProfile/profile/changeMail/MT_mailChanged.html.twig", [
      "startPage" => $url,
      "newMail" => $newMail,
      "oldMail" => $oldMail
    ]);

    return $this->mailerHelper->send($oldMail,"Mail address changed", $html);
  }

  public function activateNewMail($token) {
    $tokenHash=$this->getTokenHash($token);
    $entry = $this->userChageRep->findOneBy(["hashedToken"=>$tokenHash]);

    if (!$entry) {
      return false;
    }
    if ($entry->getExpiresAt() < new \DateTimeImmutable()) {
      return false;
    }

    $user = $entry->getUser();
    $oldMail = $user->getEmail();
    $newMail = $entry->getNewMail();

    $this->sendActivationDoneMail($oldMail, $newMail);

    $user->setEmail($newMail);

    $this->entityManager->persist($user);
    $this->entityManager->flush();
    $this->entityManager->remove($entry);
    $this->entityManager->flush();

    return true;
  }


  public function getToken()
  {
    if (!$this->token) {
      $this->token = bin2hex(random_bytes(16));  // 16 bytes = 128 bits = 32 hex characters
    }
    return $this->token;
  }

  /**
   * Get the hashed token value
   *
   * @return string The hashed value
   */
  public function getTokenHash($token)
  {
    return hash_hmac('sha256', $token, static::SECRETKEY);  // sha256 = 64 chars
  }

}