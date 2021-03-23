<?php

namespace Svc\ProfileBundle\Tests\Service;

require "UserRepositoryDummy.php";

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
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

  /**
   * check token and token hash creation
   *
   * @return void
   */
  public function testTokenHandling()
  {

    $em = $this->createMock(EntityManagerInterface::class);
    $userChangeRep = $this->createMock(UserChangesRepository::class);
    $mailerHelper = $this->createMock(MailerHelper::class);
    $translator = $this->createMock(TranslatorInterface::class);
    $twig = $this->createMock(Environment::class);
    $router = $this->createMock(RouterInterface::class);
    $user = $this->createMock(UserRepository::class);


    $changeMailHelper = new ChangeMailHelper(
      $userChangeRep,
      $em,
      $mailerHelper,
      $user,
      $twig,
      $router,
      $translator,
    );

    $token1 = $changeMailHelper->getToken();
    $token2 = $changeMailHelper->getToken();

    $this->assertEquals($token1, $token2, 'Token should be stored.');

    $tokenHash1 = $changeMailHelper->getTokenHash($token1);
    $tokenHash2 = $changeMailHelper->getTokenHash($token1);

    $this->assertEquals($tokenHash1, $tokenHash2, 'Token should be equal.');
  }
}
