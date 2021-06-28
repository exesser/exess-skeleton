<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\Component\Flow\Handler;

use ExEss\Cms\Entity\Flow;
use ExEss\Cms\Entity\ListCell;
use ExEss\Cms\Entity\ListCellLink;
use ExEss\Cms\Entity\ListDynamic;
use ExEss\Cms\Component\Flow\Handler\DataConverter;
use ExEss\Cms\Component\Flow\Handler\FlowData;
use ExEss\Cms\Component\Flow\Handler\ModelConverter;
use ExEss\Cms\Component\Flow\Response\Model;
use Helper\Testcase\FunctionalTestCase;

class DataConverterTest extends FunctionalTestCase
{
    private ModelConverter $modelConverter;

    private DataConverter $dataConverter;

    public function _before(): void
    {
        $this->modelConverter = $this->tester->grabService(ModelConverter::class);
        $this->dataConverter = $this->tester->grabService(DataConverter::class);
    }

    private function getFlowDataForModel(array $model): FlowData
    {
        $flow = new Flow;
        $flow->setIsConfig(true); // forced so the entities are saved
        $flow->setExternal(false);
        $flow->setBaseObject(ListDynamic::class);

        $data = new FlowData($flow, new Model($model));

        $this->modelConverter->handle($data);

        return $data;
    }

    public function testConvertToBeans(): void
    {
        // Given
        $model = $this->tester->loadJsonWithParams(__DIR__ . '/resources/model.json');
        $data = $this->getFlowDataForModel($model);

        // When
        $this->dataConverter->handle($data);

        // Then
        $entities = $data->getEntities();
        $this->tester->assertCount(3, $entities);
        $this->tester->assertInstanceOf(ListDynamic::class, $entities[ListDynamic::class][0] ?? null);
        $this->tester->assertInstanceOf(ListCellLink::class, $entities[ListCellLink::class][0] ?? null);
        $this->tester->assertInstanceOf(ListCell::class, $entities[ListCell::class][0] ?? null);

        /** @var ListDynamic $list */
        $list = $entities[ListDynamic::class][0];
        /** @var ListCellLink $cellLink */
        $cellLink = $entities[ListCellLink::class][0];
        /** @var ListCell $cell */
        $cell = $entities[ListCell::class][0];

        $model = $data->getModel();
        $this->tester->assertTrue($model->offsetExists('id'));
        $this->tester->assertEquals($list->getId(), $model->getFieldValue('id'));
        $this->tester->assertTrue($model->offsetExists('cellLinks|id'));
        $this->tester->assertEquals($cellLink->getId(), $model->getFieldValue('cellLinks|id'));
        $this->tester->assertTrue($model->offsetExists('cellLinks|cell|id'));
        $this->tester->assertEquals($cell->getId(), $model->getFieldValue('cellLinks|cell|id'));

        $this->tester->assertTrue($list->getCellLinks()->contains($cellLink));
        $this->tester->assertEquals($cellLink->getCell(), $cell);
    }

    public function testConvertModelWithRelField(): void
    {
        // Given
        $listId1 = $this->tester->generateDynamicList();
        $linkCellId = $this->tester->generateListLinkCell($listId1, [
            'cell_id' => $cellId = $this->tester->generateListCell()
        ]);

        $listId2 = $this->tester->generateDynamicList();

        $model = $this->tester->loadJsonWithParams(
            __DIR__ . '/resources/modelWithRel.json',
            [
                'list_id' => $listId2,
                'link_cell_id' => $linkCellId,
            ]
        );

        $data = $this->getFlowDataForModel($model);

        // When
        $this->dataConverter->handle($data);

        // Then
        $entities = $data->getEntities();
        $this->tester->assertInstanceOf(ListDynamic::class, $entities[ListDynamic::class][0] ?? null);
        /** @var ListDynamic $list */
        $list = $entities[ListDynamic::class][0];
        $this->tester->assertEquals($listId2, $list->getId());
        $this->tester->assertCount(1, $list->getCellLinks());
        $this->tester->assertEquals($linkCellId, $list->getCellLinks()->first()->getId());
    }

    public function testConvertToBeansNoBaseModule(): void
    {
        // Given
        $model = $this->tester->loadJsonWithParams(__DIR__ . '/resources/model.json');
        unset($model['baseModule']);
        $data = $this->getFlowDataForModel($model);

        // When
        $this->dataConverter->handle($data);

        // Then
        $this->tester->assertCount(0, $data->getEntities());
    }
}
