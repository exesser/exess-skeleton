<?php declare(strict_types=1);

namespace Test\Api\Api\Filter;

use ApiTester;
use ExEss\Bundle\CmsBundle\Doctrine\Type\FilterFieldType;
use ExEss\Bundle\CmsBundle\Helper\DataCleaner;

class ViewCest
{
    /**
     * @var array
     */
    private array $listsToTest = [];

    public function _before(ApiTester $I): void
    {
        foreach ($I->grabAllFromDatabase('list_dynamic_list') as $list) {
            if (!empty($list['filter_id'])) {
                $this->listsToTest[] = $list['name'];
            }
        }
    }

    public function shouldReturn(ApiTester $I): void
    {
        // Given
        $I->getAnApiTokenFor('adminUser');

        foreach ($this->listsToTest as $list) {
            // When
            $I->sendGet("/Api/filter/$list");

            // Then
            $I->seeResponseIsDwpResponse(200);
            $response = DataCleaner::jsonDecode($I->grabResponse(), false);

            $I->assertTrue(\property_exists($response, 'data'));

            $I->assertEmpty(
                $errors = $this->checkModel($response->data->model),
                \implode(', ', $errors)
            );
            $I->assertEmpty(
                $errors = $this->checkFieldGroups($response->data->fieldGroups),
                \implode(', ', $errors)
            );
        }
    }

    /**
     * @param \stdClass|array $fields
     */
    private function checkModel($fields): array
    {
        $errors = [];
        $allowedOperators = ['<', '=', '>', '<=', '>=', 'in', 'like'];

        foreach ($fields as $name => $field) {
            if (isset($field->default->operator)
                && !\in_array(\strtolower($field->default->operator), $allowedOperators, true)
            ) {
                $errors[] = 'Operator error for ' . $name;
            }
        }

        return $errors;
    }

    private function checkFieldGroups(array $fieldGroups): array
    {
        $errors = [];
        foreach ($fieldGroups as $fieldGroup) {
            foreach ($fieldGroup->fields as $field) {
                if (!\array_key_exists($field->type, FilterFieldType::getValues())) {
                    $errors[] = 'No type for ' . $field->label;
                }

                if (empty($field->id)) {
                    $errors[] = 'Field id must be present for ' . $field->label;
                }

                if ($field->type === FilterFieldType::ENUM) {
                    if (!\property_exists($field, 'enumValues')) {
                        $errors[] = 'No enumValues array present for ' . $field->label;
                    }
                }
            }
        }

        return $errors;
    }
}
