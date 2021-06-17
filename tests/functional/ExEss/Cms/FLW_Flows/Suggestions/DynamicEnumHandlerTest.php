<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\FLW_Flows\Suggestions;

use ExEss\Cms\Entity\Flow;
use ExEss\Cms\Entity\ListDynamic;
use ExEss\Cms\FLW_Flows\Request\FlowAction;
use ExEss\Cms\FLW_Flows\Response;
use ExEss\Cms\FLW_Flows\Response\Model;
use ExEss\Cms\FLW_Flows\Suggestions\DynamicEnumHandler;
use Helper\Testcase\FunctionalTestCase;

class DynamicEnumHandlerTest extends FunctionalTestCase
{
    private DynamicEnumHandler $handler;

    private string $listId;

    public function _before(): void
    {
        $this->handler = $this->tester->grabService(DynamicEnumHandler::class);

        $this->listId = $this->tester->generateDynamicList(['name' => 'kabouter']);
        $this->tester->generateSelectWithSearchDatasource([
            "name" => "Packages_no_soctar_b2c_cq",
            "base_object" => ListDynamic::class,
            "filters" => "sws.id = '$this->listId'",
            "order_by" => "sws.name"
        ]);
    }

    /**
     * @dataProvider handleProvider
     */
    public function testHandleModelSWS(\stdClass $data): void
    {
        $response = new Response();
        $response->setModel(new Model($data->givenModel));

        $field = new \stdClass();
        $field->type = 'enum';
        $field->id = 'idke';
        $enumOption = new \stdClass();
        $enumOption->enumValueSource = 'Packages_no_soctar_b2c_cq';
        $field->enumValues = [$enumOption];

        $form = new Response\Form('id', 'DEFAULT', 'key', 'name');
        $form->addCard('card01');
        $form->addField('card01', $field);
        $response->setForm($form);

        if (!empty($data->givenAction)) {
            $action = new FlowAction((array) $data->givenAction);
        } else {
            $action = new FlowAction(['event' => FlowAction::EVENT_CONFIRM]);
        }

        $this->handler->handleModel($response, $action, new Flow());
        $this->tester->assertEquals(1, $response->getSuggestions()->getFor('idke')->count());
        $this->tester->assertEquals('kabouter', $response->getSuggestions()->getFor('idke')->current()->getName());
        $this->tester->assertEquals($this->listId, $response->getSuggestions()->getFor('idke')->current()->getValue());
    }

    public function handleProvider(): array
    {
        return $this->getResources(__DIR__ . '/DynamicEnumHandlerTestResources/', 'testSWS');
    }

    /**
     * @dataProvider handleProviderCond
     */
    public function testHandleModelConditional(\stdClass $data): void
    {
        $response = new Response();
        $response->setModel(new Model($data->givenModel));

        $field = new \stdClass();
        $field->type = 'enum';
        $field->id = 'idke';
        $field->enumValues = $data->enumValue;

        $form = new Response\Form('id', 'DEFAULT', 'key', 'name');
        $form->addCard('card01');
        $form->addField('card01', $field);
        $response->setForm($form);

        if (!empty($data->givenAction)) {
            $action = new FlowAction((array) $data->givenAction);
        } else {
            $action = new FlowAction(['event' => FlowAction::EVENT_CONFIRM]);
        }

        $this->handler->handleModel($response, $action, new Flow());
        $this->tester->assertEquals(1, $response->getSuggestions()->getFor('idke')->count());
        $this->tester->assertEquals('Has signed', $response->getSuggestions()->getFor('idke')->current()->getName());
        $this->tester->assertEquals('HAS_SIGNED', $response->getSuggestions()->getFor('idke')->current()->getValue());
    }

    public function handleProviderCond(): array
    {
        return $this->getResources(__DIR__ . '/DynamicEnumHandlerTestResources/', 'testCond');
    }
}
