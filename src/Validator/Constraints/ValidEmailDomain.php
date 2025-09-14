<?php

/*
 * This file is part of the svc/profile-bundle.
 *
 * (c) 2025 Sven Vetter <dev@sv-systems.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Svc\ProfileBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ValidEmailDomain extends Constraint
{
    public string $message = 'This email domain is not allowed.';

    public string $disposableMessage = 'Disposable email addresses are not allowed.';
}
