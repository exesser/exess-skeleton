<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\FLW_Flows\Services;

use ExEss\Cms\FLW_Flows\DefaultValueService;
use ExEss\Cms\FLW_Flows\Response\Model;
use ExEss\Cms\Test\Testcase\FunctionalTestCase;

class DefaultValueServiceTest extends FunctionalTestCase
{
    private string $recordId;

    private DefaultValueService $defaultValueService;

    public function _before(): void
    {
        $this->recordId = $this->tester->generateDynamicList();
        $this->defaultValueService = $this->tester->grabService(DefaultValueService::class);
    }

    public function testGetDefaultValueForFieldWithResolve(): void
    {
        $response = $this->defaultValueService->getDefaultValueForField(
            $this->getField(true),
            $this->getModel(),
            false
        );
        $this->tester->assertEquals('before 300 after', $response);
    }

    public function testGetDefaultValueForFieldWithoutResolve(): void
    {
        $response = $this->defaultValueService->getDefaultValueForField(
            $this->getField(false),
            $this->getModel(),
            false
        );
        $this->tester->assertEquals('before {sum}100;200;0{/sum} after', $response);
    }

    private function getField(bool $resolveFunctions): \stdClass
    {
        return (object) [
            'guid' => $this->tester->generateUuid(),
            'required' => false,
            'id' => 'accounts',
            'default' => 'before {sum}100;200;0{/sum} after',
            'type' => 'textarea',
            'auto_select_suggestions' => false,
            'hasBorder' => true,
            'readonly' => false,
            'noBackendInteraction' => false,
            'resolveFunctions' => $resolveFunctions,
            'overwrite_value' => '',
        ];
    }

    private function getModel(): Model
    {
        return new Model(
            [
                'recordTypeOfRecordId' => 'Accounts',
                'recordId' => $this->recordId,
                'baseModule' => 'Accounts'
            ]
        );
    }
}
