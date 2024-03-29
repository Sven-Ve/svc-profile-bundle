<?php


declare(strict_types=1);

namespace Svc\ProfileBundle\Tests\Controller;

use Svc\ProfileBundle\Tests\SvcProfileKernel;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ChangeMailControllerTest extends KernelTestCase
{
  public function testMailSent()
  {
    $kernel = new SvcProfileKernel();
    $client = new KernelBrowser($kernel);
    $client->request('GET', '/profile/cm/mail1_sent/');
    $this->assertSame(500, $client->getResponse()->getStatusCode());
  }
}
