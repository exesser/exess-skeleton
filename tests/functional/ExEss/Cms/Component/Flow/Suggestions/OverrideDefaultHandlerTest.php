<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\Component\Flow\Suggestions;

use ExEss\Bundle\CmsBundle\Entity\Flow;
use ExEss\Bundle\CmsBundle\Component\Flow\Request\FlowAction;
use ExEss\Bundle\CmsBundle\Component\Flow\Response;
use ExEss\Bundle\CmsBundle\Component\Flow\Response\Model;
use ExEss\Bundle\CmsBundle\Component\Flow\Suggestions\OverrideDefaultHandler;
use Helper\Testcase\FunctionalTestCase;

class OverrideDefaultHandlerTest extends FunctionalTestCase
{
    private OverrideDefaultHandler $handler;

    public function _before(): void
    {
        $this->handler = $this->tester->grabService(OverrideDefaultHandler::class);
        $this->tester->loadJsonFixturesFrom(
            __DIR__ . '/OverrideDefaultHandlerTestResources/overrideDefault.fixtures.json'
        );
    }

    public function testHandleModel(): void
    {
        $model = [
            'modelField' => 'RodeGummiboot',
            'modelField99' => 'GeleGummiboot',
            'gf_1' => 'SomethingNotTheCorrectValue',
            'gf_2' => 'SomethingNotTheCorrectValue',
            'gf_3' => 'ToBeUnchanged',
            'selectWithSearch' => [new Model(['key' => 'URBA', 'value' => 'NUS'])],
            'toBeURBA' => '',
        ];
        $response = new Response();
        $response->setModel(new Model($model));

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

        $action = new FlowAction(['event' => FlowAction::EVENT_CONFIRM]);

        $flow = $this->tester->grabEntityFromRepository(Flow::class, ['id' => 'whatEvahDude']);

        $this->handler->handleModel($response, $action, $flow);

        $newModel = $response->getModel();

        $this->tester->assertEquals('RodeGummiboot', $newModel->offsetGet('modelField'));
        $this->tester->assertEquals('GeleGummiboot', $newModel->offsetGet('modelField99'));
        $this->tester->assertEquals('RodeGummiboot', $newModel->offsetGet('gf_1'));
        $this->tester->assertEquals('HARDEWAARDE', $newModel->offsetGet('gf_2'));
        $this->tester->assertEquals('ToBeUnchanged', $newModel->offsetGet('gf_3'));
        $this->tester->assertEquals('URBA', $newModel->offsetGet('toBeURBA'));
        $this->tester->assertEquals($newModel->offsetGet('selectWithSearch'), $newModel->offsetGet('selectWithSearch'));
    }
}
