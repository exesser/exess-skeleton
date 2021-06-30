<?php declare(strict_types=1);

namespace Test\Api\Api\ListDynamic;

use ApiTester;
use ExEss\Bundle\CmsBundle\Doctrine\Type\CellType;
use ExEss\Bundle\CmsBundle\Entity\User;

class RowBarCest
{
    private string $listName;
    private string $userId;

    public function _before(ApiTester $I): void
    {
        $this->userId = $I->generateUser('bah boo boo');

        $listId = $I->generateDynamicList([
            'name' => $this->listName = $I->generateUuid(),
            'items_per_page' => 10,
            'base_object' => User::class,
        ]);
        $I->generateListLinkCell($listId, [
            'row_bar_id' => $rowBarId = $I->generateListRowBar(),
            'cell_id' => $I->generateListCell(['type' => CellType::PLUS]),
        ]);
        $I->generateListRowBarAction($rowBarId, $I->generateFlowAction());
    }

    public function shouldReturn(ApiTester $I): void
    {
        // Given
        $I->getAnApiTokenFor('adminUser');

        // When
        $I->sendPOST("/Api/list/$this->listName/row/bar/$this->userId");

        // Then
        $I->seeResponseIsDwpResponse(200);
        $I->seeAssertPathsInJson([
            '$.data.buttons[0].action.recordId' => $this->userId,
            '$.data.buttons[0].action.recordType' => User::class,
        ]);
    }
}
