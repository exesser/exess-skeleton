<?php declare(strict_types=1);

namespace Test\Functional\CustomInclude\Validators;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use ExEss\Cms\Validators\NotInList;
use ExEss\Cms\Test\Testcase\FunctionalTestCase;

class NotInListValidatorTest extends FunctionalTestCase
{
    private ValidatorInterface $validator;

    private NotInList $constraint;

    public function _before(): void
    {
        $this->validator = $this->tester->grabService(ValidatorInterface::class);
        $this->constraint = new NotInList(['value' => 'blub|blab|blop']);
    }

    /**
     * @dataProvider provider
     */
    public function testValidVatNumber(string $input, bool $isValid): void
    {
        $response = $this->validator->validate($input, $this->constraint);
        $this->tester->assertEquals($isValid, \count($response) === 0);
    }

    /**
     * Provide some scenario's
     *
     * @return array
     */
    public function provider(): array
    {
        return [
            'Should be valid' => ['plop', true],
            'Should be invalid' => ['blub', false]
        ];
    }
}
