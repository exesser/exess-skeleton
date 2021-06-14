<?php declare(strict_types=1);

namespace Test\Functional\CustomInclude\Validators;

use ExEss\Cms\Test\Testcase\FunctionalTestCase;
use ExEss\Cms\Validators\FixedPhoneNumber;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FixedPhoneNumberValidatorTest extends FunctionalTestCase
{
    private ValidatorInterface $validator;

    public function _before(): void
    {
        $this->validator = $this->tester->grabService(ValidatorInterface::class);
    }

    /**
     * @dataProvider provider
     */
    public function testCanValidatePhoneNumbers(string $input, bool $isValid): void
    {
        $response = $this->validator->validate($input, new FixedPhoneNumber());
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
            'Should validate fixed line phone number' => ['03 612 20 20', true],
            'Should validate UAN phone number' => ['078 15 79 79', true],
            'Should invaidate a mobile phone number' => ['0475 31 99 99', false]
        ];
    }
}
