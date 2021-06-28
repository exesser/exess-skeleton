<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\Component\Flow\Suggestions;

use ExEss\Cms\Doctrine\Type\FlowFieldType;
use ExEss\Cms\Entity\Flow;
use ExEss\Cms\Entity\User;
use ExEss\Cms\Component\Flow\Request\FlowAction;
use ExEss\Cms\Component\Flow\Response;
use ExEss\Cms\Component\Flow\Suggestions\SelectWithSearchLabelHandler;
use Helper\Testcase\FunctionalTestCase;

class SelectWithSearchLabelHandlerTest extends FunctionalTestCase
{
    private SelectWithSearchLabelHandler $handler;

    public function _before(): void
    {
        $this->handler = $this->tester->grabService(SelectWithSearchLabelHandler::class);
    }

    public function testLabelReplacement(): void
    {
        // Given
        $fieldName = 'my_field';
        $userName = $this->tester->generateUuid();
        $dataSource = $this->tester->generateUuid();
        $userId = $this->tester->generateUser($userName);
        $this->tester->generateSelectWithSearchDatasource([
            'name' => $dataSource,
            'items_on_page' => 45,
            'base_object' => User::class,
            'option_label' => '%userName%',
            'option_key' => '%id%',
        ]);
        $flowId = $this->tester->generateFlow([
            'key_c' => 'some thing',
        ]);
        $fieldId = $this->tester->generateGuidanceField([
            "field_id" => $fieldName,
            "field_custom" => '{"datasourceName": "' . $dataSource . '"}',
            "field_type" => FlowFieldType::FIELD_TYPE_SELECT_WITH_SEARCH,
        ]);
        $flowStepId = $this->tester->generateFlowSteps($flowId);
        $this->tester->linkGuidanceFieldToFlowStep($fieldId, $flowStepId);

        $response = new Response();
        $response->getModel()->$fieldName = [$userId];

        // When
        $this->handler->handleModel(
            $response,
            new FlowAction(['event' => FlowAction::EVENT_CHANGED]),
            $this->tester->grabEntityFromRepository(Flow::class, ['id' => $flowId])
        );

        // Then
        $this->tester->assertEquals(
            [['key' => $userId, 'label' => $userName]],
            $response->getModel()->$fieldName->toArray()
        );
    }
}
