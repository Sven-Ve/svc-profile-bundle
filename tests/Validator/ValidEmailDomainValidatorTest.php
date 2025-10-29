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

namespace Svc\ProfileBundle\Tests\Validator;

use PHPUnit\Framework\TestCase;
use Svc\ProfileBundle\Validator\Constraints\ValidEmailDomain;
use Svc\ProfileBundle\Validator\Constraints\ValidEmailDomainValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

/**
 * Tests for ValidEmailDomainValidator.
 *
 * This comprehensive test suite (15 tests) verifies email domain validation including:
 *
 * **Input Validation:**
 * - Null and empty string handling (allowed, handled by other constraints)
 * - Type checking (non-string values throw exceptions)
 * - Constraint type validation
 *
 * **Disposable Email Detection:**
 * Tests blocking of 7 known disposable email domains:
 * - 10minutemail.com
 * - guerrillamail.com
 * - mailinator.com
 * - tempmail.org
 * - yopmail.com
 * - throwaway.email
 * - temp-mail.org
 *
 * **Security & Edge Cases:**
 * - Case-insensitive domain matching
 * - Invalid email format handling (deferred to Email constraint)
 * - Multiple @ symbol handling
 * - MX record validation (via checkdnsrr when available)
 *
 * **Test Approach:**
 * Uses mocked ExecutionContext and ConstraintViolationBuilder to verify
 * validation logic in isolation without actual DNS lookups during testing.
 * PHPStan ignore comments are used for mock expectations.
 */
class ValidEmailDomainValidatorTest extends TestCase
{
    private ValidEmailDomainValidator $validator;

    private ExecutionContextInterface $context;

    private ValidEmailDomain $constraint;

    protected function setUp(): void
    {
        $this->validator = new ValidEmailDomainValidator();
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->constraint = new ValidEmailDomain();
        $this->validator->initialize($this->context);
    }

    /**
     * Test that null values are allowed (handled by NotNull constraint).
     */
    public function testNullValueIsAllowed(): void
    {
        /* @phpstan-ignore-next-line */
        $this->context->expects($this->never())->method('buildViolation');
        $this->validator->validate(null, $this->constraint);
        // No assertion needed - test passes if no violation is built
    }

    /**
     * Test that empty string values are allowed.
     */
    public function testEmptyStringIsAllowed(): void
    {
        /* @phpstan-ignore-next-line */
        $this->context->expects($this->never())->method('buildViolation');
        $this->validator->validate('', $this->constraint);
        // No assertion needed - test passes if no violation is built
    }

    /**
     * Test that non-string values throw exception.
     */
    public function testNonStringThrowsException(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->validator->validate(123, $this->constraint);
    }

    /**
     * Test that wrong constraint type throws exception.
     */
    public function testWrongConstraintTypeThrowsException(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $wrongConstraint = $this->createMock(\Symfony\Component\Validator\Constraint::class);
        $this->validator->validate('test@example.com', $wrongConstraint);
    }

    /**
     * Test that disposable email domains are rejected.
     */
    public function testDisposableEmailIsRejected(): void
    {
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $violationBuilder->expects($this->once())
            ->method('addViolation');

        /* @phpstan-ignore-next-line */
        $this->context->expects($this->once())
            ->method('buildViolation')
            ->with($this->constraint->disposableMessage)
            ->willReturn($violationBuilder);

        $this->validator->validate('test@mailinator.com', $this->constraint);
    }

    /**
     * Test that 10minutemail disposable domain is rejected.
     */
    public function testDisposableDomain10MinuteMail(): void
    {
        $this->assertDisposableEmailIsRejected('user@10minutemail.com');
    }

    /**
     * Test that guerrillamail disposable domain is rejected.
     */
    public function testDisposableDomainGuerrillaMail(): void
    {
        $this->assertDisposableEmailIsRejected('test@guerrillamail.com');
    }

    /**
     * Test that tempmail disposable domain is rejected.
     */
    public function testDisposableDomainTempMail(): void
    {
        $this->assertDisposableEmailIsRejected('foo@tempmail.org');
    }

    /**
     * Test that yopmail disposable domain is rejected.
     */
    public function testDisposableDomainYopMail(): void
    {
        $this->assertDisposableEmailIsRejected('bar@yopmail.com');
    }

    /**
     * Test that throwaway.email disposable domain is rejected.
     */
    public function testDisposableDomainThrowaway(): void
    {
        $this->assertDisposableEmailIsRejected('test@throwaway.email');
    }

    /**
     * Test that temp-mail.org disposable domain is rejected.
     */
    public function testDisposableDomainTempMailOrg(): void
    {
        $this->assertDisposableEmailIsRejected('user@temp-mail.org');
    }

    /**
     * Helper method to assert disposable email is rejected.
     */
    private function assertDisposableEmailIsRejected(string $email): void
    {
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $violationBuilder->expects($this->once())
            ->method('addViolation');

        /* @phpstan-ignore-next-line */
        $this->context->expects($this->once())
            ->method('buildViolation')
            ->with($this->constraint->disposableMessage)
            ->willReturn($violationBuilder);

        $this->validator->validate($email, $this->constraint);
    }

    /**
     * Test that valid email with good domain passes validation.
     */
    public function testValidEmailPasses(): void
    {
        // For known good domains, we expect no violation
        /* @phpstan-ignore-next-line */
        $this->context->expects($this->never())->method('buildViolation');
        $this->validator->validate('user@gmail.com', $this->constraint);
    }

    /**
     * Test that email without @ symbol doesn't cause validator errors.
     * (This should be handled by Email constraint).
     */
    public function testInvalidEmailFormatIsIgnored(): void
    {
        /* @phpstan-ignore-next-line */
        $this->context->expects($this->never())->method('buildViolation');
        $this->validator->validate('notanemail', $this->constraint);
    }

    /**
     * Test that email with multiple @ symbols is ignored by this validator.
     */
    public function testEmailWithMultipleAtSymbolsIsIgnored(): void
    {
        /* @phpstan-ignore-next-line */
        $this->context->expects($this->never())->method('buildViolation');
        $this->validator->validate('test@@example.com', $this->constraint);
    }

    /**
     * Test domain validation is case-insensitive.
     */
    public function testDomainValidationIsCaseInsensitive(): void
    {
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $violationBuilder->expects($this->once())
            ->method('addViolation');

        /* @phpstan-ignore-next-line */
        $this->context->expects($this->once())
            ->method('buildViolation')
            ->with($this->constraint->disposableMessage)
            ->willReturn($violationBuilder);

        // Test uppercase version of disposable domain
        $this->validator->validate('test@MAILINATOR.COM', $this->constraint);
    }
}
