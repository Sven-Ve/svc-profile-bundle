<?php

declare(strict_types=1);

namespace Svc\ProfileBundle\Tests\Service;

require "UserRepositoryDummy.php";

use App\Entity\User;
use App\Repository\UserRepository;
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
 * testing the ChangeMailHelper class
 */
class ChangeMailHelperTest extends TestCase
{

  private $em;
  private $userChangeRep;
  private $mailerHelper;
  private $translator;
  private $twig;
  private $router;
  private $userRep;
  private $token;

  protected function setUp(): void
  {
    $this->em = $this->createMock(EntityManagerInterface::class);
    $this->userChangeRep = $this->createMock(UserChangesRepository::class);
    $this->mailerHelper = $this->createMock(MailerHelper::class);
    $this->translator = $this->createMock(TranslatorInterface::class);
    $this->twig = $this->createMock(Environment::class);
    $this->router = $this->createMock(RouterInterface::class);
    $this->userRep = $this->createMock(UserRepository::class);
  }


  /**
   * Check, if Change record expired (Case 1 = not expired)
   *
   * @return void
   */
  public function testCheckExpiredRequest1()
  {
    $user = new User();

    $userChange = new UserChanges();
    $userChange->setExpiresAt(new \DateTimeImmutable(\sprintf('+%d seconds', 100)));

    $this->userChangeRep
      ->method('findOneBy')
      ->willReturn($userChange);

    $changeMailHelper = new ChangeMailHelper($this->userChangeRep, $this->em, $this->mailerHelper, $this->userRep, $this->twig, $this->router, $this->translator);

    $result = $changeMailHelper->checkExpiredRequest($user);
    $this->assertFalse($result);
  }

    /**
   * Check, if Change record expired (Case 2 = expired)
   *
   * @return void
   */
  public function testCheckExpiredRequest2()
  {
    $user = new User();

    $userChange = new UserChanges();
    $userChange->setExpiresAt(new \DateTimeImmutable(\sprintf('+%d seconds', -100)));

    $this->userChangeRep
      ->method('findOneBy')
      ->willReturn($userChange);

    $changeMailHelper = new ChangeMailHelper($this->userChangeRep, $this->em, $this->mailerHelper, $this->userRep, $this->twig, $this->router, $this->translator);

    $result = $changeMailHelper->checkExpiredRequest($user);
    $this->assertTrue($result);
  }

  /**
   * check token creation
   *
   * @return void
   */
  public function testTokenHandling()
  {

    $changeMailHelper = new ChangeMailHelper(
      $this->userChangeRep,
      $this->em,
      $this->mailerHelper,
      $this->userRep,
      $this->twig,
      $this->router,
      $this->translator,
    );

    $token1 = $changeMailHelper->getToken();
    $token2 = $changeMailHelper->getToken();
    $this->token = $token1;

    $this->assertEquals($token1, $token2, 'Token should be stored.');
  }

  /**
   * check token hash creation
   *
   * @return void
   */
  public function testTokenHashHandling()
  {

    $changeMailHelper = new ChangeMailHelper(
      $this->userChangeRep,
      $this->em,
      $this->mailerHelper,
      $this->userRep,
      $this->twig,
      $this->router,
      $this->translator,
    );

    $tokenHash1 = $changeMailHelper->getTokenHash($this->token);
    $tokenHash2 = $changeMailHelper->getTokenHash($this->token);

    $this->assertEquals($tokenHash1, $tokenHash2, 'Token should be equal.');
  }
}
