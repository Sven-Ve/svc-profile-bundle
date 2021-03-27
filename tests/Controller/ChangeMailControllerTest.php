<?php

declare(strict_types=1);

namespace Svc\ProfileBundle\Tests\Controller;

//require_once(__dir__ . "/../Service/UserRepositoryDummy.php");

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ChangeMailControllerTest extends KernelTestCase
{

  public function testStartPage()
  {
    $kernel = new SvcProfileControllerKernel();
    $client = new KernelBrowser($kernel);
    $client->request('GET', '/api/cm');
    $this->assertSame(200, $client->getResponse()->getStatusCode());
  }

  public function testMailSent()
  {
    $kernel = new SvcProfileControllerKernel();
    $client = new KernelBrowser($kernel);
    $client->request('GET', '/api/cm/mail1_sent');
    $this->assertSame(200, $client->getResponse()->getStatusCode());
  }
}
