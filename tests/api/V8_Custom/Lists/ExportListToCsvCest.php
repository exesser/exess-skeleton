<?php declare(strict_types=1);

namespace Test\Api\V8_Custom\Lists;

use ApiTester;
use ExEss\Cms\Entity\User;

class ExportListToCsvCest
{
    private const INTERNAL_LIST_NAME = 'Users';
    private const EXTERNAL_LIST_NAME = 'Combined_Users';

    /**
     * @var array
     */
    private array $users = [];

    public function _before(ApiTester $I): void
    {
        // create some accounts
        for ($i = 1; $i <= 5; $i++) {
            $this->users[] = $I->generateUser('User_' . $i, [
                'date_entered' => '2017-01-05 00:00:00',
            ]);
        }

        $I->generateDynamicList([
            'name' => self::INTERNAL_LIST_NAME,
            'items_per_page' => 10,
            'base_object' => User::class,
        ]);

        $externalObjectId = $I->generateListExternalObject([
            'name' => self::EXTERNAL_LIST_NAME,
        ]);
        $I->generateDynamicList([
            'name' => self::EXTERNAL_LIST_NAME,
            'combined' => 1,
            'external_object_id' => $externalObjectId,
        ]);
        $I->generateExternalLinkField([
            'name' => self::INTERNAL_LIST_NAME,
            'external_object_id' => $externalObjectId,
        ]);
        $I->generateExternalLinkField([
            'name' => self::INTERNAL_LIST_NAME,
            'external_object_id' => $externalObjectId,
        ]);
    }

    public function exportDefaultList(ApiTester $I): void
    {
        $I->getAnApiTokenFor('adminUser');

        $I->sendPOST('/Api/V8_Custom/List/' . self::INTERNAL_LIST_NAME . '/export/CSV', ['recordIds' => $this->users]);

        // assertions
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeAssertPathsInJson([
            '$.data.command' => 'openLink',
        ]);
    }

    public function exportCombinedList(ApiTester $I): void
    {
        $I->getAnApiTokenFor('adminUser');

        $I->sendPOST('/Api/V8_Custom/List/' . self::EXTERNAL_LIST_NAME . '/export/CSV');

        // assertions
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeAssertPathsInJson([
            '$.data.command' => 'openLink',
        ]);
    }
}
