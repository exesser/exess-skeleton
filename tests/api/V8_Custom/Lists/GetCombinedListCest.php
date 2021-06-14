<?php declare(strict_types=1);

namespace Test\Api\V8_Custom\Lists;

use ApiTester;
use ExEss\Cms\Entity\User;

class GetCombinedListCest
{
    private const INTERNAL_LIST_NAME = 'Users';
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

        $I->generateDynamicList([
            'name' => self::INTERNAL_LIST_NAME,
            'items_per_page' => 10,
            'base_object' => User::class,
            'filter_id' => $filterId = $I->generateFilter(),
        ]);

        // create external list and link it
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

        // create filter
        $I->linkFilterToFieldGroup(
            $filterId,
            $filterFieldGroupId = $I->generateFilterFieldGroup()
        );
        $filterField = 'userName';
        $filterOperator = '=';
        $I->linkFilterFieldToFieldGroup(
            $filterFieldId = $I->generateFilterField([
                'operator' => $filterOperator,
                'field_key_c' => $filterField,
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
    }

    public function getCombinedList(ApiTester $I): void
    {
        $I->getAnApiTokenFor('adminUser');

        $I->sendPOST('/Api/V8_Custom/List/' . self::EXTERNAL_LIST_NAME);

        // assertions
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->assertTrue(\in_array($I->grabDataFromResponseByJsonPath('$.data.rows[2].id')[0], $this->users));
        $I->assertTrue(\in_array($I->grabDataFromResponseByJsonPath('$.data.rows[3].id')[0], $this->users));
        $I->assertTrue(\in_array($I->grabDataFromResponseByJsonPath('$.data.rows[4].id')[0], $this->users));
        $I->assertTrue(\in_array($I->grabDataFromResponseByJsonPath('$.data.rows[5].id')[0], $this->users));
    }

    public function filterCombinedList(ApiTester $I): void
    {
        $I->getAnApiTokenFor('adminUser');

        $I->sendPOST('/Api/V8_Custom/List/' . self::EXTERNAL_LIST_NAME, $this->filters);

        // assertions
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $assertPaths = [
            '$.data.rows[0].id' => $this->users[0],
        ];

        $failedPath = $I->grabDataFromResponseByJsonPath('$.data.rows[1].id');
        $I->assertTrue(empty($failedPath));

        $I->seeAssertPathsInJson($assertPaths);
    }
}
