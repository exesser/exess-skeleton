<?php declare(strict_types=1);

namespace Test\Functional\CustomInclude\Validators;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Helper\Testcase\FunctionalTestCase;
use ExEss\Cms\Validators\Vat;

class VatValidatorTest extends FunctionalTestCase
{
    private ValidatorInterface $validator;

    private Vat $constraint;

    public function _before(): void
    {
        $this->validator = $this->tester->grabService(ValidatorInterface::class);
        $this->constraint = new Vat();
    }

    /**
     * @dataProvider provider
     */
    public function testValidVatNumber(string $input, bool $isValid): void
    {
        $response = $this->validator->validate($input, $this->constraint);
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
            'Should validate normal BE VAT number' => ['BE0670861601', true],
            'Should validate normal NL VAT number' => ['NL999999999B99', true],
            'Should invalidate BE VAT number' => ['BE123456789', false],
            'Should invalidate BE VAT number because BE is required' => ['0123456789', false],
        ];
    }
}
