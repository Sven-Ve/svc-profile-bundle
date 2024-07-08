<?php

declare(strict_types=1);

namespace Svc\ProfileBundle\Tests\Service;

require_once __DIR__ . '/../Dummy/UserDummy.php';

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Svc\ProfileBundle\Entity\UserChanges;
use Svc\ProfileBundle\Repository\UserChangesRepository;
use Svc\ProfileBundle\Service\ChangeMailHelper;
use Svc\UtilBundle\Service\MailerHelper;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * testing the ChangeMailHelper class.
 */
class ChangeMailHelperTest extends TestCase
{
  private EntityManagerInterface $em;

  private UserChangesRepository $userChangeRep;

  private MailerHelper $mailerHelper;

  private TranslatorInterface $translator;

  private Environment $twig;

  private RouterInterface $router;

  private ChangeMailHelper $changeMailHelper;

  /**
   * prepare the mockups, load the class.
   */
  protected function setUp(): void
  {
    $this->em = $this->createMock(EntityManagerInterface::class);
    $this->userChangeRep = $this->createMock(UserChangesRepository::class);
    $this->mailerHelper = $this->createMock(MailerHelper::class);
    $this->translator = $this->createMock(TranslatorInterface::class);
    $this->twig = $this->createMock(Environment::class);
    $this->router = $this->createMock(RouterInterface::class);
    //    $this->userRep = $this->createMock(UserRepository::class);

    $this->changeMailHelper = new ChangeMailHelper(
      $this->userChangeRep,
      $this->em,
      $this->mailerHelper,
      $this->twig,
      $this->router,
      $this->translator,
    );
  }

  /**
   * check, if we load the correct class.
   */
  public function testClassLoad(): void
  {
    $this->assertInstanceOf(ChangeMailHelper::class, $this->changeMailHelper);
  }

  /**
   * Check, if Change record expired (Case 1 = not expired).
   */
  public function testCheckExpiredRequest1(): void
  {
    $user = new User();

    $userChange = new UserChanges();
    $userChange->setExpiresAt(new \DateTimeImmutable(\sprintf('+%d seconds', 100)));

    $this->userChangeRep
      ->method('findOneBy')
      ->willReturn($userChange);

    $result = $this->changeMailHelper->checkExpiredRequest($user);
    $this->assertFalse($result);
  }

  /**
   * Check, if Change record expired (Case 2 = expired).
   */
  public function testCheckExpiredRequest2(): void
  {
    $user = new User();

    $userChange = new UserChanges();
    $userChange->setExpiresAt(new \DateTimeImmutable(\sprintf('-%d seconds', 1000)));

    $this->userChangeRep
      ->method('findOneBy')
      ->willReturn($userChange);

    $result = $this->changeMailHelper->checkExpiredRequest($user);
    $this->assertTrue($result);
  }

  /**
   * check, if email address exists (Case 1 = email exists).
   *
   * @return void
   */
  /*   public function testCheckMailExists1()
    {
      $email = "test@test.com";

      $user = new User();
      $user->setEmail($email);

      $this->userRep
        ->method('findOneBy')
        ->willReturn($user);

      $result = $this->changeMailHelper->checkMailExists($email);
      $this->assertEquals($email, $result->getEmail());
    } */

  /**
   * check, if email address exists (Case 2 = email not exists).
   *
   * @return void
   */
  /*   public function testCheckMailExists2()
    {
      $email = "test@test.com";

      $this->userRep
        ->method('findOneBy')
        ->willReturn(null);

      $result = $this->changeMailHelper->checkMailExists($email);
      $this->assertNull($result);
    } */

  /**
   * check token creation.
   *
   * @return void
   */
  public function testTokenHandling()
  {
    $token1 = $this->changeMailHelper->getToken();
    $token2 = $this->changeMailHelper->getToken();

    $this->assertEquals($token1, $token2, 'Token should be stored.');
  }

  /**
   * check token hash creation.
   *
   * @return void
   */
  public function testTokenHashHandling()
  {
    $token = $this->changeMailHelper->getToken();
    $tokenHash1 = $this->changeMailHelper->getTokenHash($token);
    $tokenHash2 = $this->changeMailHelper->getTokenHash($token);

    $this->assertEquals($tokenHash1, $tokenHash2, 'Token should be equal.');
  }
}
