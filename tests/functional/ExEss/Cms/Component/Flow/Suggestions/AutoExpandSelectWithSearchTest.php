<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\Component\Flow\Suggestions;

use ExEss\Bundle\CmsBundle\Entity\Flow;
use ExEss\Bundle\CmsBundle\Component\Flow\Request\FlowAction;
use ExEss\Bundle\CmsBundle\Component\Flow\Response;
use ExEss\Bundle\CmsBundle\Component\Flow\Response\Model;
use ExEss\Bundle\CmsBundle\Component\Flow\Suggestions\AutoExpandSelectWithSearch;
use ExEss\Bundle\CmsBundle\Helper\DataCleaner;
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
                DataCleaner::jsonDecode(\json_encode($data->expectedOnModel)),
                $response->getModel()->toArray()
            );
        }
    }

    public function handleProvider(): array
    {
        return $this->getResources(__DIR__ . '/AutoExpandSelectWithSearchTestResources/', 'test');
    }
}
