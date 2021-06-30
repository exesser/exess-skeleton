<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\Component\Flow;

use Doctrine\Common\Collections\ArrayCollection;
use ExEss\Bundle\CmsBundle\Component\Flow\Response\Model;
use ExEss\Bundle\CmsBundle\Component\Flow\Validator as ValidatorService;
use ExEss\Bundle\CmsBundle\Doctrine\Type\Validator as ValidatorType;
use ExEss\Bundle\CmsBundle\Entity\User;
use ExEss\Bundle\CmsBundle\Entity\Validator;
use Helper\Testcase\FunctionalTestCase;
use stdClass;

class ValidatorTest extends FunctionalTestCase
{
    private const EMAIL = 'Email';
    private const DAY = 'day';
    private const MONTH = 'month';
    private const YEAR = 'year';

    private ValidatorService $validator;

    public function _before(): void
    {
        $this->validator = $this->tester->grabService(ValidatorService::class);
    }

    public function emailProvider(): array
    {
        return [
            'valid' => ['test@test.com', true],
            'valid+1' => ['test+1@test.com', true],
            'invalid string' => ['test', false],
            'invalid special chars' => ['a1&é@"#\'(§^è!çà{})°-_><?,.;/:+= ~@f.com', false],
            'invalid 2 emails' => ['est@test.com or test2@test.com', false],
        ];
    }

    /**
     * @dataProvider emailProvider
     */
    public function testEmailValidation(string $email, bool $valid): void
    {
        $validator = $this->tester->generateFieldValidator([
            'name' => 'is email',
            'validator' => 'Email',
            'validator_field' => 'email',
        ]);
        $validatorCollection = new ArrayCollection([
            $this->tester->grabEntityFromRepository(Validator::class, ['id' => $validator])
        ]);

        // valid email
        $model = new Model(['email' => $email]);
        $response = $this->validator->runValidationRules($validatorCollection, $model);
        $this->tester->assertCount((int)!$valid, $response);
    }

    public function testDateValidationDataProvider(): array
    {
        return [
            'valid Y-m-d' => ['1931-03-14', true],
            'valid d-m-Y' => ['14-03-1931', true],
            'valid d/m/Y' => ['14/03/1931', true],
            'invalid month' => ['1931-14-14', false],
            'invalid day' => ['1931-03-32', false],
            'random string' => ['invalid-date', false],
        ];
    }

    /**
     * @dataProvider testDateValidationDataProvider
     */
    public function testDateValidation(string $date, bool $valid): void
    {
        // setup
        $validator = $this->tester->generateFieldValidator([
            'name' => 'is date',
            'validator' => 'Date',
            'validator_field' => 'date',
        ]);

        $validatorCollection = new ArrayCollection([
            $this->tester->grabEntityFromRepository(Validator::class, ['id' => $validator])
        ]);

        $model = new Model(['date' => $date]);

        // act
        $response = $this->validator->runValidationRules($validatorCollection, $model);

        // assert
        $this->tester->assertCount((int)!$valid, $response);
    }

    public function validatorProvider(): array
    {
        $notBlankValidator = $this->selfFieldValidator(ValidatorType::NOT_BLANK);
        $notBlankMessage = 'This value should not be blank.';

        return [
            'no_rule' => [
                'fieldName' => 'companyName',
                'validators' => [],
                'fieldValuesAndExpectations' => [
                    ['model' => ['companyName' => 'The Snorlax Co'], 'result' => []],
                    ['model' => ['companyName' => ''], 'result' => []],
                ],
            ],
            'test_one_rule' => [
                'fieldName' => 'companyName',
                'validators' => [$notBlankValidator],
                'fieldValuesAndExpectations' => [
                    [
                        'model' => ['companyName' => ''],
                        'result' => [$notBlankMessage]
                    ],
                    ['model' => ['companyName' => null], 'result' => [$notBlankMessage]],
                    ['model' => ['companyName' => 'The Snorlax Co'], 'result' => []],
                ],
            ],
            'email_combined_rules' => [
                'fieldName' => 'email',
                'validators' => [
                    $notBlankValidator,
                    $this->selfFieldValidator(ValidatorType::EMAIL),
                ],
                'fieldValuesAndExpectations' => [
                    ['model' => ['email' => ''], 'result' => [$notBlankMessage]],
                    ['model' => [], 'result' => [$notEmailMessage = 'This value is not a valid email address.']],
                    ['model' => ['email' => null], 'result' => [$notBlankMessage]],
                    ['model' => ['email' => 'Snorlax Co'], 'result' => [$notEmailMessage]],
                    ['model' => ['email' => 'test@test.com'], 'result' => []],
                ],
            ],
            'combined_rules' => [
                'fieldName' => 'firstName',
                'validators' => [
                    $this->notBlankValidator('firstName'),
                    $this->notBlankValidator('lastName'),
                    $this->notBlankValidator('phone'),
                ],
                'fieldValuesAndExpectations' => [
                    [
                        'model' => [
                            'firstName' => '',
                            'lastName' => '',
                            'phone' => '',
                        ],
                        'result' => [$notBlankMessage],
                    ],
                    [
                        'model' => [
                            'firstName' => 'something',
                            'lastName' => '',
                            'phone' => '',
                        ],
                        'result' => [],
                    ],
                    [
                        'model' => [
                            'firstName' => '',
                            'lastName' => 'test',
                            'phone' => '',
                        ],
                        'result' => [],
                    ],
                ],
            ],
            'day_mutator_valid' => [
                'fieldName' => 'fictional_date',
                'validators' => [$this->dateMutator(self::DAY, 5)],
                'fieldValuesAndExpectations' => [
                    [
                        'model' => ['fictional_date' => '2016-03-05',],
                        'result' => [],
                    ],
                ],
            ],
            'day_mutator_invalid' => [
                'fieldName' => 'fictional_date',
                'validators' => [$this->dateMutator(self::DAY, 5)],
                'fieldValuesAndExpectations' => [
                    [
                        'model' => ['fictional_date' => '2016-03-06',],
                        'result' => ['The day of this value should be equal to "5".'],
                    ],
                ],
            ],
            'month_mutator_valid' => [
                'fieldName' => 'fictional_date',
                'validators' => [$this->dateMutator(self::MONTH, 3)],
                'fieldValuesAndExpectations' => [
                    [
                        'model' => ['fictional_date' => '2016-03-06',],
                        'result' => [],
                    ],
                ],
            ],
            'month_mutator_invalid' => [
                'fieldName' => 'fictional_date',
                'validators' => [$this->dateMutator(self::MONTH, 5)],
                'fieldValuesAndExpectations' => [
                    [
                        'model' => ['fictional_date' => '2016-03-06',],
                        'result' => [$this->expectedMsg(self::MONTH, 5)],
                    ],
                ],
            ],
            'year_mutator_valid' => [
                'fieldName' => 'fictional_date',
                'validators' => [$this->dateMutator(self::YEAR, 2016)],
                'fieldValuesAndExpectations' => [
                    [
                        'model' => ['fictional_date' => '2016-03-06',],
                        'result' => [],
                    ],
                ],
            ],
            'year_mutator_invalid' => [
                'fieldName' => 'fictional_date',
                'validators' => [$this->dateMutator(self::YEAR, 5)],
                'fieldValuesAndExpectations' => [
                    [
                        'model' => ['fictional_date' => '2016-03-06',],
                        'result' => [$this->expectedMsg(self::YEAR, 5)],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider validatorProvider
     */
    public function testRunValidationRules(
        string $fieldName,
        array $validators,
        array $fieldValuesAndExpectations
    ): void {
        // setup
        $user = $this->tester->grabEntityFromRepository(User::class, ['id' => '1']);
        $rules = [];
        foreach ($validators as $validatorData) {
            $rules[] = $this->tester->grabEntityFromRepository(Validator::class, [
                'id' => $this->tester->haveInRepository(
                    Validator::class,
                    [
                        'createdBy' => $user,
                        'dateEntered' => new \DateTime(),
                        'showOnTop' => false,
                        'andNotNull' => false,
                    ] + $validatorData
                )
            ]);
        }

        foreach ($fieldValuesAndExpectations as $case => ['result' => $expected, 'model' => $model]) {
            $expectation = new stdClass();
            if (!empty($expected)) {
                $expectation->$fieldName = $expected;
            }

            // act, assert
            $this->tester->assertEquals(
                $expectation,
                $this->validate(new Model($model), $rules, $fieldName),
                "Case #$case: Result should match expected errors."
            );
        }
    }

    private function validate(Model $model, array $rules, string $fieldName): stdClass
    {
        $validationErrors = $this->validator->runValidationRules(
            new ArrayCollection($rules),
            $model,
            $fieldName
        );

        return $this->validator->formatViolations($validationErrors);
    }

    private function dateMutator(string $type, int $value): array
    {
        return [
            'validator' => 'EqualTo',
            'mode' => 0,
            'mutator' => $type,
            'value' => $value,
        ];
    }

    private function notBlankValidator(string $field): array
    {
        return [
            'validator' => ValidatorType::NOT_BLANK,
            'field' => $field,
            'mode' => 1,
        ];
    }

    private function selfFieldValidator(string $validator): array
    {
        return [
            'validator' => $validator,
            'field' => '__self__',
            'mode' => 0,
        ];
    }

    private function expectedMsg(string $type, int $number): string
    {
        return \sprintf('The %s of this value should be equal to "%d".', $type, $number);
    }
}
