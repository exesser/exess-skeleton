<?php declare(strict_types=1);

namespace Test\Api\Api\ListDynamic;

use ApiTester;
use ExEss\Cms\Entity\User;

class GetCombinedListCest
{
    private const EXTERNAL_LIST_NAME = 'Combined_Users';

    /**
     * @var array
     */
    private array $users = [];

    /**
     * @var array
     */
    private array $filters = [];

    public function _before(ApiTester $I): void
    {
        // create some accounts
        $this->users = [];
        $firstName = \md5((string) \random_int(10000, 99999));
        for ($i = 1; $i <= 5; $i++) {
            $this->users[] = $I->generateUser($i === 1 ? $firstName : 'User_' . $i, [
                'date_entered' => '2017-01-05 00:00:0'.$i,
            ]);
        }

        // create a list with filter
        $listId = $I->generateDynamicList([
            'items_per_page' => 10,
            'base_object' => User::class,
            'filter_id' => $filterId = $I->generateFilter(),
        ]);

        $I->linkFilterToFieldGroup(
            $filterId,
            $filterFieldGroupId = $I->generateFilterFieldGroup()
        );
        $I->linkFilterFieldToFieldGroup(
            $filterFieldId = $I->generateFilterField([
                'operator' => '=',
                'field_key_c' => $filterField = 'userName',
            ]),
            $filterFieldGroupId
        );

        $this->filters = [
            'page' => 1,
            'filters' => [
                $filterField => [
                    'default' => [
                        'value' => $firstName,
                        'fieldId' => $filterFieldId,
                    ],
                ],
            ],
        ];

        // create external list and link it
        $I->generateDynamicList([
            'name' => self::EXTERNAL_LIST_NAME,
            'combined' => 1,
            'external_object_id' => $externalObjectId = $I->generateListExternalObject(),
        ]);
        $I->generateExternalLinkField([
            'list_id' => $listId,
            'external_object_id' => $externalObjectId,
        ]);
    }

    public function getCombinedList(ApiTester $I): void
    {
        // Given
        $I->getAnApiTokenFor('adminUser');

        // When
        $I->sendPOST('/Api/list/' . self::EXTERNAL_LIST_NAME);

        // Then
        $I->seeResponseIsDwpResponse(200);

        $I->assertTrue(\in_array($I->grabDataFromResponseByJsonPath('$.data.rows[2].id')[0], $this->users));
        $I->assertTrue(\in_array($I->grabDataFromResponseByJsonPath('$.data.rows[3].id')[0], $this->users));
        $I->assertTrue(\in_array($I->grabDataFromResponseByJsonPath('$.data.rows[4].id')[0], $this->users));
        $I->assertTrue(\in_array($I->grabDataFromResponseByJsonPath('$.data.rows[5].id')[0], $this->users));
    }

    public function filterCombinedList(ApiTester $I): void
    {
        // Given
        $I->getAnApiTokenFor('adminUser');

        // When
        $I->sendPOST('/Api/list/' . self::EXTERNAL_LIST_NAME, $this->filters);

        // Then
        $I->seeResponseIsDwpResponse(200);

        $failedPath = $I->grabDataFromResponseByJsonPath('$.data.rows[1].id');
        $I->assertTrue(empty($failedPath));

        $I->seeAssertPathsInJson([
            '$.data.rows[0].id' => $this->users[0],
        ]);
    }
}
