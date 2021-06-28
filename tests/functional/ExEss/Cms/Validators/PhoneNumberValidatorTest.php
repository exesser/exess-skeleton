<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\Validators;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Helper\Testcase\FunctionalTestCase;
use ExEss\Cms\Validators\PhoneNumber;

class PhoneNumberValidatorTest extends FunctionalTestCase
{
    private ValidatorInterface $validator;

    private PhoneNumber $contraint;

    public function _before(): void
    {
        $this->validator = $this->tester->grabService(ValidatorInterface::class);
        $this->contraint = new PhoneNumber();
    }

    /**
     * @dataProvider provider
     */
    public function testCanValidatePhoneNumbers(string $input, bool $isValid): void
    {
        $response = $this->validator->validate($input, $this->contraint);
        $this->tester->assertEquals($isValid, \count($response) === 0, "Assert input '$input' to be valid or not.");
    }

    /**
     * Provide some scenario's
     *
     * @return array
     */
    public function provider(): array
    {
        return [
            'Should validate normal phone number' => ['0474 31 99 99', true],
            'Should validate normal phone number with country code' => ['0032 474 31 99 90', true],
            'Should validate a international phone number' => ['+34 664 24 22 67', true],
            'Should (in)validate an international phone number without code' => ['0664 24 22 67', false],
            'Should (in)validate an international phone number with wrong code' => ['+32 664 24 22 67', false],
            'Should (in)validate a random number' => ['474319', false],
            'Should (in)validate a random string' => ['abcdef', false],
            'Should (in)validate a good looking number that is not valid' => ['+0486549001', false],
        ];
    }
}
