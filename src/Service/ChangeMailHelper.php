<?php

namespace Svc\ProfileBundle\Service;

use App\Entity\User;
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
  private $userRep;
  private $token;


  public function __construct(
    private UserChangesRepository $userChangeRep,
    private EntityManagerInterface $entityManager,
    private MailerHelper $mailerHelper,
    private Environment $twig,
    private RouterInterface $router,
    private TranslatorInterface $translator
  ) {
    /** @phpstan-ignore-next-line */
    $this->userRep = $this->entityManager->getRepository(User::class);
  }

  /**
   * check if a request exists and if it expired
   *
   * @param User $user
   *
   * @phpstan-ignore-next-line 
   */
  public function checkExpiredRequest(User $user): bool
  {
    $entry = $this->userChangeRep->findOneBy(["user" => $user, "changeType" => self::TYPCHANGEMAIL]);
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
   *
   * @param string $email email address to be checked
   * @return User|null
   * 
   * @phpstan-ignore-next-line 
   */
  public function checkMailExists(string $email): ?User
  {
    return $this->userRep->findOneBy(['email' => $email]);
  }

  /**
   * write the change record in table userChanges
   *
   * @param User $user
   *
   * @phpstan-ignore-next-line 
   */
  public function writeUserChangeRecord(User $user, string $newMail): void
  {
    $change = new UserChanges();
    $change->setUser($user);
    $change->setChangeType(self::TYPCHANGEMAIL);
    $change->setExpiresAt(new \DateTimeImmutable(\sprintf('+%d seconds', self::TOKENLIFETIME)));
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
  public function sendActivationMail(string $newMail): bool
  {
    $token = $this->getToken();
    $url = $this->router->generate('svc_profile_change_mail_activate', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

    $html = $this->twig->render("@SvcProfile/profile/changeMail/MT_activateMail.html.twig", ["url" => $url]);
    $text = $this->twig->render("@SvcProfile/profile/changeMail/MT_activateMail.text.twig", ["url" => $url]);
    $subject = $this->translator->trans("Activate new email address", [], 'ProfileBundle');
    return $this->mailerHelper->send($newMail, $subject, $html, $text);
  }

  /**
   * send a mail to the old address to inform about the mail change.
   *
   * @return boolean if mail sent
   */
  public function sendActivationDoneMail(string $oldMail, string $newMail): bool
  {
    $url = EnvInfoHelper::getURLtoIndexPhp();

    $html = $this->twig->render("@SvcProfile/profile/changeMail/MT_mailChanged.html.twig", [
      "startPage" => $url, "newMail" => $newMail, "oldMail" => $oldMail
    ]);
    $text = $this->twig->render("@SvcProfile/profile/changeMail/MT_mailChanged.text.twig", [
      "startPage" => $url, "newMail" => $newMail, "oldMail" => $oldMail
    ]);

    $subject = $this->translator->trans("Email address changed", [], 'ProfileBundle');
    return $this->mailerHelper->send($oldMail, $subject, $html, $text);
  }

  /**
   * activate new mail adress (write in Users table and delete from UserChanges table)
   */
  public function activateNewMail(string $token): bool
  {
    $tokenHash = $this->getTokenHash($token);
    $entry = $this->userChangeRep->findOneBy(["hashedToken" => $tokenHash]);

    if (!$entry) {
      return false;
    }
    if ($entry->getExpiresAt() < new \DateTimeImmutable()) {
      return false;
    }

    $user = $entry->getUser();
    $oldMail = $user->getEmail();  /** @phpstan-ignore-line */
    $newMail = $entry->getNewMail();

    $this->sendActivationDoneMail($oldMail, $newMail);

    $user->setEmail($newMail);  /** @phpstan-ignore-line */

    $this->entityManager->persist($user);
    $this->entityManager->flush();
    $this->entityManager->remove($entry);
    $this->entityManager->flush();

    return true;
  }

  /**
   * create a token
   */
  public function getToken(): string
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
  public function getTokenHash($token): string
  {
    $secretKey = $_ENV['SVC_PROFILE_HASH_SECRET'] ?? self::SECRETKEY;
    return hash_hmac('sha256', $token, $secretKey);  // sha256 = 64 chars
  }
}
