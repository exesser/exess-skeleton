<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\FLW_Flows\Suggestions;

use ExEss\Cms\Doctrine\Type\FlowFieldType;
use ExEss\Cms\Entity\Flow;
use Mockery\Mock;
use ExEss\Cms\FESelectWithSearch\SelectWithSearchService;
use ExEss\Cms\FLW_Flows\Request\FlowAction;
use ExEss\Cms\FLW_Flows\Response;
use ExEss\Cms\FLW_Flows\Response\Model;
use ExEss\Cms\FLW_Flows\Suggestions\AutoSelectWithSearchSuggestionHandler;
use Helper\Testcase\FunctionalTestCase;

class AutoSelectWithSearchSuggestionHandlerTest extends FunctionalTestCase
{
    private AutoSelectWithSearchSuggestionHandler $handler;

    /**
     * @var SelectWithSearchService|Mock
     */
    private $selectWithSearchService;

    public function _before(): void
    {
        $this->selectWithSearchService = \Mockery::mock(SelectWithSearchService::class);
        $this->tester->mockService(SelectWithSearchService::class, $this->selectWithSearchService);

        $this->handler = $this->tester->grabService(AutoSelectWithSearchSuggestionHandler::class);
    }

    public function testHandle(): void
    {
        $model = [
            "testflow" => true,
            "alreadyInModel" => true,
        ];

        // arrange
        $response = new Response();
        $action = new FlowAction([
            'event' => FlowAction::EVENT_CHANGED,
            'focus' => 'id',
        ]);
        $response->setModel(new Model($model));
        $response->setForm(
            (new Response\Form('id', 'DEFAULT', 'key', 'name'))
                ->setGroup(
                    'r1c1',
                    [
                        (object) [
                            "id" => "shouldNotBeInModelNoAutoSelect",
                            "type" => FlowFieldType::FIELD_TYPE_SELECT_WITH_SEARCH,
                            "auto_select_suggestions" => 0,
                        ],
                        (object) [
                            "id" => "alreadyInModel",
                            "type" => FlowFieldType::FIELD_TYPE_SELECT_WITH_SEARCH,
                            "auto_select_suggestions" => 1,
                        ],
                        (object) [
                            "id" => "shouldNotBeInModelMultipleResults",
                            "type" => FlowFieldType::FIELD_TYPE_SELECT_WITH_SEARCH,
                            "datasourceName" => "testDataSourceMultipleReturn",
                            "auto_select_suggestions" => 1,
                        ],
                        (object) [
                            "id" => "shouldBeInModel",
                            "type" => FlowFieldType::FIELD_TYPE_SELECT_WITH_SEARCH,
                            "datasourceName" => "testDataSourceSingleReturn",
                            "auto_select_suggestions" => 1,
                        ],
                    ]
                )
        );

        $this->selectWithSearchService
            ->shouldNotReceive('getSelectOptions')
            ->with(
                'shouldNotBeInModelNoAutoSelect',
                [
                    'fullModel' => ["testflow" => true],
                ]
            );

        $this->selectWithSearchService
            ->shouldNotReceive('getSelectOptions')
            ->with(
                'alreadyInModel',
                [
                    'fullModel' => ["testflow" => true],
                ]
            );

        $this->selectWithSearchService
            ->shouldReceive('getSelectOptions')
            ->with(
                'testDataSourceMultipleReturn',
                [
                    'fullModel' => $model,
                ]
            )
            ->once()
            ->andReturn([
                'rows' => [
                    1 => 'shouldNotBeInModel',
                    2 => 'shouldNotBeInModel',
                ]
            ]);

        $this->selectWithSearchService
            ->shouldReceive('getSelectOptions')
            ->with(
                'testDataSourceSingleReturn',
                [
                    'fullModel' => $model,
                ]
            )
            ->once()
            ->andReturn([
                'rows' => [
                    1 => 'shouldBeInModel',
                ]
            ]);

        $this->handler->handleModel($response, $action, new Flow());

        $model = $response->getModel()->toArray();
        $this->tester->assertArrayHasKey('shouldBeInModel', $model);
        $this->tester->assertEquals([1 => 'shouldBeInModel'], $model['shouldBeInModel']);
        $this->tester->assertArrayNotHasKey('shouldNotBeInModelMultipleResults', $model);
        $this->tester->assertArrayNotHasKey('shouldNotBeInModelNoAutoSelect', $model);
    }
}
