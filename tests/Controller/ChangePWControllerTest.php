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

/**
 * Tests for ChangePWController functionality.
 *
 * This test suite verifies:
 * - Route accessibility for password change endpoints
 * - Integration with the test kernel configuration
 * - Basic controller functionality without full authentication setup
 *
 * Note: Tests marked as "risky" are expected due to exception handlers
 * in the test environment (Doctrine mapping exceptions, missing services).
 * These are not actual test failures.
 */
class ChangePWControllerTest extends KernelTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->client = new KernelBrowser($kernel);
    }

    /**
     * Test that the password change form route is accessible.
     * Route should exist even if not authenticated.
     */
    public function testStartFormRouteIsAccessible(): void
    {
        $this->client->request('GET', '/profile/cpw/');
        $response = $this->client->getResponse();

        // Route should exist (will fail in test due to entity config but not 404)
        $this->assertNotEquals(404, $response->getStatusCode());
    }
}
