<?php declare(strict_types=1);

namespace Test\Functional\CustomInclude\Validators;

use Helper\Testcase\FunctionalTestCase;
use ExEss\Cms\Validators\MobilePhoneNumber;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MobilePhoneNumberValidatorTest extends FunctionalTestCase
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
        $response = $this->validator->validate($input, new MobilePhoneNumber());
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
            'Should invalidate fixed line phone number' => ['03 612 20 20', false],
            'Should invalidate UAN phone number' => ['078 15 79 79', false],
            'Should validate a mobile phone number' => ['0475 31 99 99', true]
        ];
    }
}
