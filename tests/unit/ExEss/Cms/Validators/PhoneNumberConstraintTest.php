<?php declare(strict_types=1);

namespace Test\Unit\ExEss\Cms\Validators;

use Helper\Testcase\UnitTestCase;
use ExEss\Bundle\CmsBundle\Validators\PhoneNumber;
use ExEss\Bundle\CmsBundle\Validators\PhoneNumberValidator;
use Symfony\Component\Validator\Constraint;

class PhoneNumberConstraintTest extends UnitTestCase
{
    public function testCanConstruct(): void
    {
        $constraint = new PhoneNumber();
        $this->tester->assertInstanceOf(Constraint::class, $constraint);
        $this->tester->assertInstanceOf(PhoneNumber::class, $constraint);
    }

    public function testHasDefaultMessage(): void
    {
        $constraint = new PhoneNumber();
        $this->tester->assertTrue(\property_exists($constraint, 'message'));
        $this->tester->assertStringContainsString('{{ value }}', $constraint->message);
    }

    public function testIsValidatedByPhoneNumberValidator(): void
    {
        $constraint = new PhoneNumber();
        $this->tester->assertEquals(PhoneNumberValidator::class, $constraint->validatedBy());
    }
}
