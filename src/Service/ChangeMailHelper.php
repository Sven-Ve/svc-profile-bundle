<?php

namespace Svc\ProfileBundle\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Svc\ProfileBundle\Entity\UserChanges;
use Svc\ProfileBundle\Repository\UserChangesRepository;
use Svc\UtilBundle\Service\EnvInfoHelper;
use Svc\UtilBundle\Service\MailerHelper;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Private class to support profile changes
 *
 * @author Sven Vetter <dev@sv-systems.com>
 */
class ChangeMailHelper
{
  
  private const TOKENLIFETIME = 3600;

  # generated with https://passwordsgenerator.net/sha256-hash-generator/
  private const SECRETKEY = "23573BE852F6D1C73B314809E940F19F3D00EF1CD99147462861BB714E68DCC1";
  private const TYPCHANGEMAIL = 1;
  
  private $userChangeRep;
  private $userRep;
  private $entityManager;
  private $mailerHelper;
  private $token;
  private $twig;
  private $router;
  private $translator;

  
  public function __construct(
    UserChangesRepository $userChangeRep, 
    EntityManagerInterface $entityManager, 
    MailerHelper $mailerHelper, 
    UserRepository $userRep,
    Environment $twig,
    RouterInterface $router,
    TranslatorInterface $translator)
  {
    $this->userChangeRep = $userChangeRep;
    $this->entityManager = $entityManager;
    $this->mailerHelper = $mailerHelper;
    $this->userRep = $userRep;
    $this->twig = $twig;
    $this->router = $router;
    $this->translator = $translator;
  }

  public function checkExpiredRequest($user) {
    $entry = $this->userChangeRep->findOneBy(["user"=>$user, "changeType" => static::TYPCHANGEMAIL]);
    if (!$entry) {
      return true;
    }

    if ($entry->getExpiresAt() > new \DateTimeImmutable()) {
      return false;
    }

    $this->entityManager->remove($entry);
    $this->entityManager->flush();
    return true;
  }

  /**
   * Check, if user record with this mail adress exists
   */
  public function checkMailExists($email) {
    return $this->userRep->findOneBy(['email' => $email]);
  }

  /**
   * write the change record in table userChanges
   */
  public function writeUserChangeRecord(User $user, $newMail) {
    // $expiresAt = new \DateTimeImmutable(\sprintf('+%d seconds', static::TOKENLIFETIME));

    $change = new UserChanges();
    $change->setUser($user);
    $change->setChangeType(static::TYPCHANGEMAIL);
    $change->setExpiresAt(new \DateTimeImmutable(\sprintf('+%d seconds', static::TOKENLIFETIME)));
    $change->setNewMail($newMail);

    $token = $this->getToken();
    $change->setHashedToken($this->getTokenHash($token));

    $this->entityManager->persist($change);
    $this->entityManager->flush();
  }

  /**
   * send a mail with the activation link
   * 
   * @return boolean true if mail sent
   */
  public function sendActivationMail($newMail) {
    $token = $this->getToken();
    $url=$this->router->generate('svc_profile_change_mail_activate', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

    $html=$this->twig->render("@SvcProfile/profile/changeMail/MT_activateMail.html.twig", ["url" => $url]);
    $text=$this->twig->render("@SvcProfile/profile/changeMail/MT_activateMail.text.twig", ["url" => $url]);
    $subject = $this->translator->trans("Activate new email address", [], 'ProfileBundle');
    return $this->mailerHelper->send($newMail, $subject, $html, $text);
  }

  /**
   * send a mail to the old address to inform about the mail change.
   * 
   * @return boolean true if mail sent
   */
  public function sendActivationDoneMail($oldMail, $newMail) {
    $url=EnvInfoHelper::getURLtoIndexPhp();

    $html=$this->twig->render("@SvcProfile/profile/changeMail/MT_mailChanged.html.twig", [
      "startPage" => $url, "newMail" => $newMail, "oldMail" => $oldMail]);
    $text=$this->twig->render("@SvcProfile/profile/changeMail/MT_mailChanged.text.twig", [
      "startPage" => $url, "newMail" => $newMail, "oldMail" => $oldMail]);

    $subject = $this->translator->trans("Email address changed", [], 'ProfileBundle');
    return $this->mailerHelper->send($oldMail, $subject, $html, $text);
  }

  /**
   * activate new mail adress (write in Users table and delete from UserChanges table)
   */
  public function activateNewMail($token) {
    $tokenHash=$this->getTokenHash($token);
    $entry = $this->userChangeRep->findOneBy(["hashedToken"=>$tokenHash]);

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
    $secretKey = $_ENV['SVC_PROFILE_HASH_SECRET'] ?? static::SECRETKEY;
    return hash_hmac('sha256', $token, $secretKey);  // sha256 = 64 chars
  }

}