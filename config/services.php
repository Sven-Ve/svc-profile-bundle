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
use Svc\ProfileBundle\Repository\UserChangesRepository;
use Svc\ProfileBundle\Service\ChangeMailHelper;
use Svc\ProfileBundle\Validator\Constraints\ValidEmailDomainValidator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->private();

    $services->set(ChangeMailController::class);
    $services->set(ChangeMailHelper::class);
    $services->set(ChangePWController::class);

    $services->set(UserChangesRepository::class)
        ->tag('doctrine.repository_service');

    $services->set(ValidEmailDomainValidator::class)
        ->tag('validator.constraint_validator');
};
