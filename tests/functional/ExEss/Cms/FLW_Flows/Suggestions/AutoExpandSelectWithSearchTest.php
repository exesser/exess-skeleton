<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\FLW_Flows\Suggestions;

use ExEss\Cms\Entity\Flow;
use ExEss\Cms\FLW_Flows\Request\FlowAction;
use ExEss\Cms\FLW_Flows\Response;
use ExEss\Cms\FLW_Flows\Response\Model;
use ExEss\Cms\FLW_Flows\Suggestions\AutoExpandSelectWithSearch;
use Helper\Testcase\FunctionalTestCase;

class AutoExpandSelectWithSearchTest extends FunctionalTestCase
{
    private AutoExpandSelectWithSearch $handler;

    public function _before(): void
    {
        $this->handler = $this->tester->grabService(AutoExpandSelectWithSearch::class);
        $this->tester->loadJsonFixturesFrom(
            __DIR__ . '/AutoExpandSelectWithSearchTestResources/autoExpandSelectWithSearch'
        );
    }

    /**
     * @dataProvider handleProvider
     */
    public function testHandleModel(\stdClass $data): void
    {
        $response = new Response();
        $response->setModel(new Model($data->givenModel));

        $field = new \stdClass();
        $field->type = 'selectWithSearch';
        $field->id = 'toExpand';
        $field->preFill = 'all';
        $field->datasourceName = 'someSWS';

        $form = new Response\Form('id', 'DEFAULT', 'key', 'name');
        $form->addCard('card01');
        $form->addField('card01', $field);
        $response->setForm($form);

        $flow = new Flow();
        if (!empty($data->givenAction)) {
            $action = new FlowAction((array) $data->givenAction);
        } else {
            $action = new FlowAction(['event' => FlowAction::EVENT_CONFIRM]);
        }

        if (!empty($data->exception)) {
            //execute method to test when we have an exception
            $this->tester->expectThrowable(
                new $data->exception($data->exceptionMessage),
                function () use ($response, $action, $flow): void {
                    $this->handler->handleModel($response, $action, $flow);
                }
            );
        } else {
            //execute method to test
            $this->handler->handleModel($response, $action, $flow);

            //assert
            $this->tester->assertEquals(
                \json_decode(\json_encode($data->expectedOnModel), true),
                $response->getModel()->toArray()
            );
        }
    }

    public function handleProvider(): array
    {
        return $this->getResources(__DIR__ . '/AutoExpandSelectWithSearchTestResources/', 'test');
    }
}
