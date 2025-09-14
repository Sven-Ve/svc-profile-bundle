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
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ValidEmailDomainValidator extends ConstraintValidator
{
    private const DISPOSABLE_DOMAINS = [
        '10minutemail.com',
        'guerrillamail.com',
        'mailinator.com',
        'tempmail.org',
        'yopmail.com',
        'throwaway.email',
        'temp-mail.org',
    ];

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidEmailDomain) {
            throw new UnexpectedTypeException($constraint, ValidEmailDomain::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        // Extract domain from email
        $parts = explode('@', $value);
        if (count($parts) !== 2) {
            return; // Let other validators handle this
        }

        $domain = strtolower(trim($parts[1]));

        // Check against known disposable email domains
        if (in_array($domain, self::DISPOSABLE_DOMAINS, true)) {
            $this->context->buildViolation($constraint->disposableMessage)
                ->addViolation();

            return;
        }

        // Additional domain validation - check if domain has MX record
        if (!$this->validateDomainMX($domain)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }

    private function validateDomainMX(string $domain): bool
    {
        // Basic domain format check
        if (!filter_var($domain, FILTER_VALIDATE_DOMAIN)) {
            return false;
        }

        // Check for MX record (only in production-like environments)
        if (function_exists('checkdnsrr')) {
            return checkdnsrr($domain, 'MX') || checkdnsrr($domain, 'A');
        }

        return true; // Skip MX check if DNS functions not available
    }
}
