<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\Component\Flow\Event\Listeners;

use ExEss\Cms\Entity\Flow;
use ExEss\Cms\Entity\ListCell;
use ExEss\Cms\Component\Flow\Event\FlowEvent;
use ExEss\Cms\Component\Flow\Event\Listeners\SaveSubscriber;
use ExEss\Cms\Component\Flow\Handler\FlowData;
use ExEss\Cms\Component\Flow\Request\FlowAction;
use ExEss\Cms\Component\Flow\Response\Model;
use ExEss\Cms\Component\Flow\SaveFlow;
use Helper\Testcase\FunctionalTestCase;

class SaveSubscriberTest extends FunctionalTestCase
{
    private SaveSubscriber $subscriber;

    private \Mockery\MockInterface $saveFlow;

    public function _before(): void
    {
        $this->tester->mockService(
            SaveFlow::class,
            $this->saveFlow = \Mockery::mock(SaveFlow::class)
        );
        $this->subscriber = $this->tester->grabService(SaveSubscriber::class);
        $this->tester->loadJsonFixturesFrom(__DIR__ . '/resources/SaveSubscriber.fixtures.json');
    }

    public function testMultiRecordIdSave(): void
    {
        // Given
        $listId1 = $this->tester->generateDynamicList();
        $listId2 = $this->tester->generateDynamicList();
        $linkCellId1 = $this->tester->generateListLinkCell($listId1);
        $linkCellId2 = $this->tester->generateListLinkCell($listId1);
        $linkCellId3 = $this->tester->generateListLinkCell($listId2);
        $linkCellId4 = $this->tester->generateListLinkCell($listId2);

        $model = new Model([
            'name' => $this->tester->generateUuid(),
            'recordTypeOfRecordId' => ListCell::class,
            'recordIds' => [$linkCellId1, $linkCellId2, $linkCellId3, $linkCellId4],
        ]);

        $flow = $this->tester->grabEntityFromRepository(Flow::class, ['key' => 'flowForDefault']);

        $event = new FlowEvent(
            'flowForDefault',
            new FlowAction(['event' => FlowAction::EVENT_CONFIRM]),
            $model
        );
        $event->getResponse()->setModel($model);
        $event->setFlow($flow);

        $flowData = new FlowData($flow, $model);

        // Then
        $this->saveFlow->shouldReceive('save')
            ->with(
                $flow,
                \Mockery::on(function (Model $model) use ($linkCellId1, $listId1) {
                    return
                        $model['id'] === $linkCellId1 &&
                        $model['name'] === $listId1;
                }),
                null
            )
            ->once()
            ->andReturn($flowData);

        $this->saveFlow->shouldReceive('save')
            ->with(
                $flow,
                \Mockery::on(function (Model $model) use ($linkCellId2, $listId1) {
                    return
                        $model['id'] === $linkCellId2 &&
                        $model['name'] === $listId1;
                }),
                null
            )
            ->once()
            ->andReturn($flowData);

        $this->saveFlow->shouldReceive('save')
            ->with(
                $flow,
                \Mockery::on(function (Model $model) use ($linkCellId3, $listId2) {
                    return
                        $model['id'] === $linkCellId3 &&
                        $model['name'] === $listId2;
                }),
                null
            )
            ->once()
            ->andReturn($flowData);

        $this->saveFlow->shouldReceive('save')
            ->with(
                $flow,
                \Mockery::on(function (Model $model) use ($linkCellId4, $listId2) {
                    return
                        $model['id'] === $linkCellId4 &&
                        $model['name'] === $listId2;
                }),
                null
            )
            ->once()
            ->andReturn($flowData);

        // When
        $this->subscriber->handleFlowSave($event);
    }
}
