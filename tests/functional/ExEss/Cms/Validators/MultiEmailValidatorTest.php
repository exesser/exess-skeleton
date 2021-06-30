<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\Validators;

use Helper\Testcase\FunctionalTestCase;
use ExEss\Bundle\CmsBundle\Validators\MultiEmail;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MultiEmailValidatorTest extends FunctionalTestCase
{
    private ValidatorInterface $validator;

    public function _before(): void
    {
        $this->validator = $this->tester->grabService(ValidatorInterface::class);
    }

    /**
     * @dataProvider provider
     */
    public function testCanValidate(string $input, bool $isValid): void
    {
        $response = $this->validator->validate($input, new MultiEmail());
        $this->tester->assertEquals($isValid, \count($response) === 0, "Assert input '$input' to be valid or not.");
    }

    public function provider(): array
    {
        return [
            'One valid' => ['test@test.com', true],
            'Multiple valid' => ['test@test.com; test2@test.com', true],
            'One invalid' => ['test@test.com; éççét est@tes t.com', false],
            'All invalid' => ['éàé&étes t@test.co m; éççétes t@te st.com', false],
        ];
    }
}
