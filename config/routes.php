<?php

/*
 * This file is part of the svc/profile-bundle.
 *
 * (c) 2025 Sven Vetter <dev@sv-systems.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Svc\ProfileBundle\Controller\ChangeMailController;
use Svc\ProfileBundle\Controller\ChangePWController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    $routes->add('svc_profile_change_mail_start', '/cm/')
        ->controller([ChangeMailController::class, 'startForm']);

    $routes->add('svc_profile_change_mail_sent1', '/cm/mail1_sent/')
        ->controller([ChangeMailController::class, 'mail1Sent']);

    $routes->add('svc_profile_change_mail_activate', '/cm/activate')
        ->controller([ChangeMailController::class, 'activateNewMail']);

    $routes->add('svc_profile_change_pw_start', '/cpw/')
        ->controller([ChangePWController::class, 'startForm']);
};