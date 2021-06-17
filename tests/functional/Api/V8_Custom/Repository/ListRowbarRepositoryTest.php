<?php declare(strict_types=1);

namespace Test\Functional\Api\V8_Custom\Repository;

use ExEss\Cms\Api\V8_Custom\Repository\ListRowbarRepository;
use ExEss\Cms\Entity\User;
use Helper\Testcase\FunctionalTestCase;

class ListRowbarRepositoryTest extends FunctionalTestCase
{
    private ListRowbarRepository $listRowbarRepository;

    private string $recordId;

    public function _before(): void
    {
        $this->listRowbarRepository = $this->tester->grabService(ListRowbarRepository::class);

        // Given
        $this->addListFixtures();
        $this->recordId = $this->tester->generateUser("test user");
    }

    public function testGetListRowBarActionsWithoutActionData(): void
    {
        // When
        $retVal = $this->listRowbarRepository->findListRowActions('ListTest', $this->recordId, []);

        // Then
        $this->tester->assertCount(1, $retVal);
        $action = $retVal[0]->action;
        $this->tester->assertEquals($this->recordId, $action['recordId']);
        $this->tester->assertEquals("ListTest", $action['listKey']);
        $this->tester->assertEquals(User::class, $action['recordType']);
        $this->tester->assertNull($action['actionData']);
    }

    public function testGetListRowBarActionsWithActionData(): void
    {
        // When
        $retVal = $this->listRowbarRepository->findListRowActions(
            'ListTest',
            $this->recordId,
            ["testName" => "testValue"]
        );

        // Then
        $this->tester->assertCount(1, $retVal);
        $this->tester->assertNotEmpty($retVal[0]->label);
        $action = $retVal[0]->action;
        $this->tester->assertEquals($this->recordId, $action['recordId']);
        $this->tester->assertEquals("ListTest", $action['listKey']);
        $this->tester->assertEquals(User::class, $action['recordType']);
        $this->tester->assertIsArray($action['actionData']);
        $this->tester->assertEquals("testValue", $action['actionData']['testName']);
    }

    private function addListFixtures(): void
    {
        $listId = $this->tester->generateDynamicList([
            "name" => "ListTest",
            "base_object" => User::class,
            "title" => "Contracts",
            "items_per_page" => 30,
            "filters_have_changed" => 1,
        ]);

        $listRowBarId = $this->tester->generateListRowBar([
            "name" => "test rowbar",
            "created_by" => "1",
        ]);
        $listCellId = $this->tester->generateListCell([
            "name" => "testcell",
            "type" => "list_plus_cell",
        ]);

        $this->tester->generateListLinkCell($listId, [
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
            "created_by" => "1",
        ]);
    }
}
