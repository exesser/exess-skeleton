<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\Service;

use ExEss\Bundle\CmsBundle\Doctrine\Type\CellType;
use ExEss\Bundle\CmsBundle\Entity\ListDynamic;
use ExEss\Bundle\CmsBundle\Entity\User;
use ExEss\Bundle\CmsBundle\Service\ListRowBarService;
use Helper\Testcase\FunctionalTestCase;

class ListRowBarServiceTest extends FunctionalTestCase
{
    private ListRowBarService $listRowBarService;
    private string $recordId;
    private string $listId;

    public function _before(): void
    {
        $this->listRowBarService = $this->tester->grabService(ListRowBarService::class);

        // Given
        $this->addListFixtures();
        $this->recordId = $this->tester->generateUser("test user");
    }

    public function testGetListRowBarActionsWithoutActionData(): void
    {
        // Given
        /** @var ListDynamic $list */
        $list = $this->tester->grabEntityFromRepository(ListDynamic::class, ['id' => $this->listId]);

        // When
        $return = $this->listRowBarService->findListRowActions($list, $this->recordId, []);

        // Then
        $this->tester->assertCount(1, $return);
        $action = $return[0]->action;
        $this->tester->assertEquals($this->recordId, $action['recordId']);
        $this->tester->assertEquals("ListTest", $action['listKey']);
        $this->tester->assertEquals(User::class, $action['recordType']);
        $this->tester->assertNull($action['actionData']);
    }

    public function testGetListRowBarActionsWithActionData(): void
    {
        // Given
        /** @var ListDynamic $list */
        $list = $this->tester->grabEntityFromRepository(ListDynamic::class, ['id' => $this->listId]);
        $actionData = ['testName' => 'testValue'];

        // When
        $return = $this->listRowBarService->findListRowActions($list, $this->recordId, $actionData);

        // Then
        $this->tester->assertCount(1, $return);
        $this->tester->assertNotEmpty($return[0]->label);
        $action = $return[0]->action;
        $this->tester->assertEquals($this->recordId, $action['recordId']);
        $this->tester->assertEquals("ListTest", $action['listKey']);
        $this->tester->assertEquals(User::class, $action['recordType']);
        $this->tester->assertIsArray($action['actionData']);
        $this->tester->assertEquals($actionData, $action['actionData']);
    }

    private function addListFixtures(): void
    {
        $this->listId = $this->tester->generateDynamicList([
            "name" => "ListTest",
            "base_object" => User::class,
            "title" => "Contracts",
            "items_per_page" => 30,
            "filters_have_changed" => 1,
        ]);

        $listRowBarId = $this->tester->generateListRowBar([
            "name" => "test rowbar",
        ]);
        $listCellId = $this->tester->generateListCell([
            "name" => "testcell",
            "type" => CellType::PLUS,
        ]);

        $this->tester->generateListLinkCell($this->listId, [
            "name" => "testcells",
            'row_bar_id' => $listRowBarId,
            'cell_id' => $listCellId,
        ]);
        $this->tester->generateListLinkCell($this->tester->generateDynamicList(), [
            "name" => "testcells - 2",
            'cell_id' => $listCellId,
        ]);

        $this->tester->generateListRowBarAction($listRowBarId, null, [
            "name" => "test list row action",
            "type" => "CALLBACK",
            "icon" => "icon-wijzigen",
            "action_name" => "Update contractline",
            "order_c" => 30,
        ]);
    }
}
