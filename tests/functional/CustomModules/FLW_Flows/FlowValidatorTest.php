<?php declare(strict_types=1);

namespace Test\Functional\CustomModules\FLW_Flows;

use ExEss\Cms\Entity\FlowStep;
use ExEss\Cms\FLW_Flows\FlowValidator;
use ExEss\Cms\FLW_Flows\Response\Model;
use ExEss\Cms\FLW_Flows\Response\ValidationResult;
use Helper\Testcase\FunctionalTestCase;
use Test\Functional\CustomModules\Dashboard\GridRepositoryTest;

class FlowValidatorTest extends FunctionalTestCase
{
    private const MSG_NOT_BLANK = 'This value should not be blank.';
    private const MSG_LENGTH = 'This value should have exactly 5 characters.';

    private FlowValidator $validator;

    public function _before(): void
    {
        $this->validator = $this->tester->grabService(FlowValidator::class);
        $this->tester->loadJsonFixturesFrom(__DIR__  . '/../Dashboard/resources/repeatable.fixtures.json');
    }

    public function testValidateUnknownField(): void
    {
        $model = new Model();

        $flowStep = $this->tester->grabEntityFromRepository(FlowStep::class, ['id' => 'my-flowstep']);

        $field = 'my-unknown-field';

        // run test
        $result = $this->validator->validateField($flowStep, $model, $field);

        // assertions
        $this->tester->assertInstanceOf(\stdClass::class, $result);
        $this->tester->assertEmpty((array)$result);
    }

    public function testValidateFieldWithoutValidators(): void
    {
        $model = new Model();

        $flowStep = $this->tester->grabEntityFromRepository(FlowStep::class, ['id' => 'my-flowstep']);

        // run test
        $result = $this->validator->validateField($flowStep, $model, GridRepositoryTest::FIELD_NAME);

        // assertions
        $this->tester->assertInstanceOf(\stdClass::class, $result);
        $this->tester->assertEmpty((array)$result);
    }

    public function validationDataProvider(): array
    {
        return [
            [null, 'validator-notblank', self::MSG_NOT_BLANK],
            ['', 'validator-notblank', self::MSG_NOT_BLANK],
            ['boo', 'validator-notblank'],
            [null, 'validator-notblank-and-length', self::MSG_LENGTH],
            ['', 'validator-notblank-and-length', self::MSG_LENGTH],
            ['123', 'validator-notblank-and-length', self::MSG_LENGTH],
            ['123456', 'validator-notblank-and-length', self::MSG_LENGTH],
            ['12345', 'validator-notblank-and-length', self::MSG_LENGTH],
        ];
    }

    /**
     * @dataProvider validationDataProvider
     */
    public function testValidateField(
        ?string $value,
        string $fixturesToLoad,
        ?string $expectedError = null
    ): void {
        $this->tester->loadJsonFixturesFrom(__DIR__  . '/resources/' . $fixturesToLoad);

        $field = GridRepositoryTest::FIELD_NAME;

        $model = new Model();
        $model->$field = $value;

        $flowStep = $this->tester->grabEntityFromRepository(FlowStep::class, ['id' => 'my-flowstep']);

        // run test
        $result = $this->validator->validateField($flowStep, $model, $field);

        // assertions
        $this->tester->assertInstanceOf(\stdClass::class, $result);
        $this->tester->assertSame($expectedError !== null, isset($result->$field), 'result has error for field');
        if ($expectedError) {
            $this->tester->assertNotEmpty($result->$field);
            $this->tester->assertTrue(isset($result->$field[0]), 'result has 1 error message for field');
            $this->tester->assertSame($expectedError, $result->$field[0]);
        }
    }

    public function validationStepDataProvider(): array
    {
        return [
            [null, 'validator-notblank', self::MSG_NOT_BLANK],
            ['', 'validator-notblank', self::MSG_NOT_BLANK],
            ['boo', 'validator-notblank'],
            [null, 'validator-notblank-and-length', self::MSG_NOT_BLANK],
            ['', 'validator-notblank-and-length', self::MSG_LENGTH],
            ['123', 'validator-notblank-and-length', self::MSG_LENGTH],
            ['123456', 'validator-notblank-and-length', self::MSG_LENGTH],
            ['12345', 'validator-notblank-and-length'],
        ];
    }

    /**
     * This method tests validateFlow and validateFlowStep since the errors are located in a repeated row
     *
     * @dataProvider validationStepDataProvider
     */
    public function testValidateFlowStep(
        ?string $value = null,
        string $fixturesToLoad,
        ?string $expectedError = null
    ): void {
        $this->tester->loadJsonFixturesFrom(__DIR__  . '/resources/validator-flowstep.fixtures.json');
        $this->tester->loadJsonFixturesFrom(__DIR__  . '/resources/' . $fixturesToLoad);

        // link the validators to the field in the child flow
        $this->tester->deleteFromDatabase('flw_guidancefields_flw_guidancefieldvalidators_1_c', [
            'validator_id' => 'my-validator',
        ]);
        $this->tester->deleteFromDatabase('flw_guidancefields_flw_guidancefieldvalidators_1_c', [
            'validator_id' => 'my-validator-2',
        ]);
        $this->tester->linkGuidanceFieldToFieldValidator('my-child-field', 'my-validator');
        if ($fixturesToLoad === 'validator-notblank-and-length') {
            $this->tester->linkGuidanceFieldToFieldValidator('my-child-field', 'my-validator-2');
        }

        $repeatField = GridRepositoryTest::FIELD_NAME;
        $modelKey = 'packageProduct';
        $field = 'someChildField';
        $selectedRepeatTriggerValue = '123';

        $model = new Model([
            'someParentField' => $value,
            $repeatField => [
                $selectedRepeatTriggerValue,
            ],
            $modelKey => [
                $selectedRepeatTriggerValue => [
                    $field => $value,
                ],
            ],
        ]);

        $flowStep = $this->tester->grabEntityFromRepository(FlowStep::class, ['id' => 'my-flowstep']);

        $result = new ValidationResult();

        // run test
        $this->validator->validateFlowStep($flowStep, $model, $result);

        // assertions
        if ($expectedError) {
            $this->tester->assertFalse($result->isValid());
            $errors = $result->getErrors();
            $this->tester->assertNotEmpty($errors);
            $this->tester->assertTrue(isset($errors[$modelKey][$selectedRepeatTriggerValue][$field][0]));
            $errorMessage = $errors[$modelKey][$selectedRepeatTriggerValue][$field][0];
            $this->tester->assertSame($expectedError, $errorMessage);
        } else {
            $this->tester->assertTrue($result->isValid());
            $this->tester->assertEmpty($result->getErrors());
            $this->tester->assertEmpty($result->getFields());
        }
    }
}
