<?php

declare(strict_types=1);

/*
 * This file is part of the svc/profile-bundle.
 *
 * (c) 2025 Sven Vetter <dev@sv-systems.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Svc\ProfileBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ChangeMailControllerTest extends KernelTestCase
{
    public function testMailSent(): void
    {
        $kernel = self::bootKernel();
        $client = new KernelBrowser($kernel);
        $client->request('GET', '/profile/cm/mail1_sent/');
        $this->assertSame(500, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString("Class 'App\Entity\User' does not exist", (string) $client->getResponse()->getContent());
    }
}
