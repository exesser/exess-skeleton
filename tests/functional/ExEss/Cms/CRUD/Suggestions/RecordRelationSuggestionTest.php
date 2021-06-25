<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\CRUD\Suggestions;

use ExEss\Cms\CRUD\Helpers\SecurityService;
use ExEss\Cms\CRUD\Suggestions\RecordRelationSuggestion;
use ExEss\Cms\Dictionary\Model\Dwp;
use ExEss\Cms\Entity\Flow;
use ExEss\Cms\FLW_Flows\Request\FlowAction;
use ExEss\Cms\FLW_Flows\Response;
use ExEss\Cms\FLW_Flows\Response\Model;
use ExEss\Cms\FLW_Flows\SaveFlow;
use ExEss\Cms\Helper\DataCleaner;
use Helper\Testcase\FunctionalTestCase;

class RecordRelationSuggestionTest extends FunctionalTestCase
{
    private const RESOURCE_FILE = __DIR__ . '/Resources/RecordRelationSuggestionTest.json';

    protected RecordRelationSuggestion $handler;

    public function _before(): void
    {
        $this->handler = $this->tester->grabService(RecordRelationSuggestion::class);
    }

    /**
     * @dataProvider handleProvider
     */
    public function testHandleModel(array $model, string $module): void
    {
        // prepare
        $response = (new Response())->setModel(new Model($model));
        $action = new FlowAction(['event' => FlowAction::EVENT_INIT]);

        $flow = $this->tester->grabEntityFromRepository(Flow::class, ['key' => SaveFlow::CRUD_RECORD_DETAILS]);

        // run
        $this->handler->handleModel($response, $action, $flow);

        // fix test - leave this one here
        // $this->fixTestExpectedData($response);

        // assert
        $this->tester->assertEquals(
            DataCleaner::jsonDecode(\file_get_contents(self::RESOURCE_FILE))[$module],
            $response->getModel()->getFieldValue(Dwp::RELATIONS_FIELD)->toArray()
        );
    }

    public function handleProvider(): array
    {
        $data = [];

        $crudAllModules = \array_merge(
            SecurityService::CRUD_MAIN_MODULES,
            SecurityService::ALL_CONFIG_MODULES
        );

        \sort($crudAllModules);
        foreach ($crudAllModules as $module) {
            $data[$module] = [
                [
                    Dwp::RELATIONS_FIELD => [],
                    Dwp::PREFIX . "recordTypeOfRecordId" => $module
                ],
                $module
            ];
        }

        return $data;
    }

    private function fixTestExpectedData(Response $response): void
    {
        $relations = $response->getModel()->getFieldValue(Dwp::RELATIONS_FIELD)->toArray();
        $expectedData = DataCleaner::jsonDecode(\file_get_contents(self::RESOURCE_FILE));
        $expectedData[$response->getModel()->getFieldValue(Dwp::PREFIX . 'recordTypeOfRecordId')] = $relations;

        \file_put_contents(self::RESOURCE_FILE, \json_encode($expectedData, \JSON_PRETTY_PRINT));
    }
}
