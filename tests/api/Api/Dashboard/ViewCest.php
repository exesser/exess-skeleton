<?php declare(strict_types=1);

namespace Test\Api\Api\Dashboard;

use ApiTester;
use ExEss\Bundle\CmsBundle\Entity\Dashboard;
use ExEss\Bundle\CmsBundle\Entity\User;

class ViewCest
{
    public function withKeyAndRecordId(ApiTester $I): void
    {
        // Given
        $recordType = User::class;

        /** @var User $user */
        $user = $I->grabEntityFromRepository(User::class, ['id' => '1']);
        $I->haveInRepository(Dashboard::class, [
            'createdBy' => $user,
            'dateEntered' => new \DateTime(),
            'key' => $key = $I->generateUuid(),
            'mainRecordType' => $recordType,
            'gridTemplate' => [
                'createdBy' => $user,
                'dateEntered' => new \DateTime(),
                'jsonFields' => [
                    'columns' => [
                        [
                            'size' => '1-4',
                            'cssClasses' => ['blue-sidebar'],
                            'hasMargin' => false,
                            'rows' => [
                                [
                                    'size' => '1-1',
                                    'type' => 'blueSidebar',
                                    'options' => [
                                        'id' => '%recordId%',
                                        'dashboardName' => 'my-dashboard',
                                        'recordType' => $recordType,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        ]);

        $I->getAnApiTokenFor('adminUser');

        // When
        $I->sendGet("/Api/dashboard/$key/1");

        // Then
        $I->seeResponseIsDwpResponse(200);
        $I->seeAssertPathsInJson([
            '$.data.grid.columns[0].rows[0].options.id' => '1',
            '$.data.grid.columns[0].rows[0].options.recordType' => $recordType,
            '$.data.baseEntity' => "[ User ] - {$user->getUserName()}",
        ]);
    }

    public function withKey(ApiTester $I): void
    {
        // Given
        $user = $I->grabEntityFromRepository(User::class, ['id' => '1']);
        $I->haveInRepository(Dashboard::class, [
            'createdBy' => $user,
            'dateEntered' => new \DateTime(),
            'key' => $key = $I->generateUuid(),
            'name' => $name = $I->generateUuid(),
            'gridTemplate' => [
                'createdBy' => $user,
                'dateEntered' => new \DateTime(),
            ],
        ]);

        $I->getAnApiTokenFor('adminUser');

        // When
        $I->sendGet("/Api/dashboard/$key");

        // Then
        $I->seeResponseIsDwpResponse(200);
        $I->seeAssertPathsInJson([
            '$.data.title' => $name,
        ]);
    }
}
