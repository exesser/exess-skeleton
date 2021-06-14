<?php declare(strict_types=1);

namespace Test\Functional\CustomInclude\Validators;

use ExEss\Cms\Test\Testcase\FunctionalTestCase;
use ExEss\Cms\Validators\IsEndOfMonth;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class IsEndOfMonthValidatorTest extends FunctionalTestCase
{
    private ValidatorInterface $validator;

    public function _before(): void
    {
        $this->validator = $this->tester->grabService(ValidatorInterface::class);
    }

    /**
     * @dataProvider provider
     */
    public function testCanValidateDate(string $input, bool $isValid): void
    {
        $response = $this->validator->validate(new \DateTime($input), new IsEndOfMonth());
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
            'Should invalidate 31 days month' => ['2019-03-30', false],
            'Should invalidate 30 days month' => ['2019-04-29', false],
            'Should invalidate 28 days month' => ['2019-02-27', false],
            'Should validate 31 days month' => ['2019-03-31', true],
            'Should validate 30 days month' => ['2019-04-30', true],
            'Should validate 28 days month' => ['2019-02-28', true],
        ];
    }
}
