<?php declare(strict_types=1);

namespace Test\Api\V8_Custom\Lists;

use ApiTester;
use ExEss\Cms\Doctrine\Type\CellType;
use ExEss\Cms\Entity\User;

class GetListRowActionCest
{
    private string $userId;

    public function _before(ApiTester $I): void
    {
        $this->userId = $I->generateUser('bah boo boo');

        $listId = $I->generateDynamicList([
            'name' => 'UserList',
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
        $I->getAnApiTokenFor('adminUser');

        $I->sendPOST('/Api/V8_Custom/ListRowAction/UserList/' . $this->userId);

        // assertions
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $assertPaths = [
            '$.data.buttons[0].action.recordId' => $this->userId,
            '$.data.buttons[0].action.recordType' => User::class,
        ];

        $I->seeAssertPathsInJson($assertPaths);
    }
}
