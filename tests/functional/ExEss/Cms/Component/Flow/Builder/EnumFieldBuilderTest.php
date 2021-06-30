<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\Component\Flow\Builder;

use ExEss\Bundle\CmsBundle\Doctrine\Type\FlowFieldType;
use ExEss\Bundle\CmsBundle\Doctrine\Type\UserStatus;
use ExEss\Bundle\CmsBundle\Entity\User;
use ExEss\Bundle\CmsBundle\Component\Flow\Builder\EnumFieldBuilder;
use ExEss\Bundle\CmsBundle\Component\Flow\EnumRecord;
use ExEss\Bundle\CmsBundle\Component\Flow\Response\Model;
use ExEss\Bundle\CmsBundle\Helper\DataCleaner;
use Helper\Testcase\FunctionalTestCase;

class EnumFieldBuilderTest extends FunctionalTestCase
{
    protected EnumFieldBuilder $enumFieldBuilder;

    public function _before(): void
    {
        $this->enumFieldBuilder = $this->tester->grabService(EnumFieldBuilder::class);
    }

    public function testExpandEnums(): void
    {
        $formSections = [
            'r1c1' => [
                DataCleaner::jsonDecode(
                    '{
                            "guid": "77d12147-c399-b50d-3b40-59dc74b18113",
                            "required": false,
                            "id": "selection_invoice_date",
                            "label": "Factuurdatum",
                            "default": null,
                            "type": "enum",
                            "auto_select_suggestions": false,
                            "hasBorder": true,
                            "readonly": true,
                            "noBackendInteraction": false,
                            "generateByServer": false,
                            "enumValues": [
                                {
                                  "key": true,
                                  "value": "1st of month"
                                },
                                {
                                  "key": 2,
                                  "value": "2nd of month"
                                },
                                {
                                  "key": 3.5,
                                  "value": "3rd of month"
                                },
                                {
                                  "key": "4",
                                  "value": "4th of month"
                                },
                                {
                                  "key": false,
                                  "value": "5th of month"
                                },
                                {
                                  "key": 0,
                                  "value": "6th of month"
                                },
                                {
                                  "key": "0",
                                  "value": "7th of month"
                                }
                            ],
                              "overwrite_value": ""
                            }',
                    false
                )
            ]
        ];

        $this->enumFieldBuilder->expandEnums($formSections, new Model);
        $enumValues = $formSections['r1c1'][0]->enumValues;

        // boolean converts to string
        $this->tester->assertSame('1', $enumValues[0]->key);

        // integers preserved
        $this->tester->assertSame(2, $enumValues[1]->key);
        $this->tester->assertSame(0, $enumValues[5]->key);

        // float converts to string
        $this->tester->assertSame('3.5', $enumValues[2]->key);

        // strings preserved
        $this->tester->assertSame('4', $enumValues[3]->key);
        $this->tester->assertSame('0', $enumValues[6]->key);

        // null preserved
        $this->tester->assertNull($enumValues[4]->key);
    }

    public function testExpandIfConditionalEnum(): void
    {
        // arrange
        $model = new Model([
            'pricing_group_c' => 'TC1',
            'accounts|record_type' => 'B2C',
            'quote_type_c' => 'SALES',
            'sales_channel_id' => '3870fc89-07cb-c636-94c6-58ac5d855546',
            'stage' => 'PRICED',
            'dwp|sales_channel' => null,
            'sign_channel_c' => 'ONLINE',
            'sign_date_c' => null,
            'signature_option_c' => '',
            'sign_location_c' => null,
            'sales_channel_legal_label' => '',
            'dwp|guidanceFlowId' => 'B2C_CQFA',
            'aos_products_quotes' => [],
            'dwp|payment_details|com_prefs(type=\'LEGAL\')|channel|matchedCondition' => 'default',
            // @codingStandardsIgnoreLine
            'dwp|signature_option_c|matchedCondition' => 'model[\'dwp|customer_wants_to_sign\'] && model[\'sign_channel_c\'] !== \'TABLET\' &&  model[\'quote_type_c\'] !== \'MOVE\''
        ]);

        $formSections = $this->tester->loadJsonWithParams(__DIR__ . '/resources/formSections.json', [], false);

        // act
        $this->enumFieldBuilder->expandIfConditionalEnum($formSections, $model, true);

        // assert
        $this->tester->assertSame('TABLET', $model->sign_channel_c);
    }

    public function testExpandIfConditionalEnumContactType(): void
    {
        // Given
        $option = new \stdClass();
        $option->value = UserStatus::INACTIVE;
        $option->key = UserStatus::INACTIVE;

        $item = new \stdClass();
        $item->condition = true;
        $item->values = [$option];

        $field = new \stdClass();

        $field->id = $this->tester->generateUuid();
        $field->enumValues = [$item];
        $field->type = FlowFieldType::FIELD_TYPE_ENUM;
        $field->module = User::class;
        $field->moduleField = 'status';

        // When
        $suggestion = $this->enumFieldBuilder->expandIfConditionalEnum($field, new Model());

        // Then
        $this->tester->assertEquals(
            [new EnumRecord(UserStatus::INACTIVE, UserStatus::INACTIVE)],
            $suggestion,
            'Should contain same value as in app_list_strings.php; check contacts.custom.vardef for contact_type_c_list'
        );
    }
}
