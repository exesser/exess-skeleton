<?php declare(strict_types=1);

namespace Test\Api\V8_Custom\Crud;

use ApiTester;
use ExEss\Cms\Entity\Dashboard;
use ExEss\Cms\Entity\Property;

class ListCest
{
    private CrudTestUser $user;

    public function shouldNotSeeRecordTypeList(ApiTester $I): void
    {
        // act
        $this->user = new CrudTestUser($I);
        $this->user->login();

        $I->sendPOST('/Api/V8_Custom/List/CrudAllBeans');

        // assert
        $I->seeResponseIsDwpResponse(403, "You don't have the rights to perform this action");
    }

    public function shouldSeeConfigRecordTypeList(ApiTester $I): void
    {
        // arrange
        $this->user = new CrudTestUser($I);
        $this->user->linkSecurity(CrudTestUser::CRUD_VIEW_CONFIG_SECURITY);
        $this->user->login();

        // act
        $I->sendPOST('/Api/V8_Custom/List/CrudAllBeans');

        // assert
        $I->seeResponseIsDwpResponse(200);
        $I->seeResponseJsonMatchesJsonPath('$.data.rows');
        $rows = $I->grabDataFromResponseByJsonPath('$.data.rows')[0];
        $I->assertNotEmpty($rows);
        $I->seeResponseContainsJson(['line1' => 'Flow Actions']);
    }

    public function shouldNotSeeRecordList(ApiTester $I): void
    {
        // act
        $this->user = new CrudTestUser($I);
        $this->user->login();

        $I->sendPOST('/Api/V8_Custom/List/CrudRecordsList', ['page' => 1, 'recordType' => 'Account']);

        // assert
        $I->seeResponseIsDwpResponse(403, "You don't have the rights to perform this action");
    }

    public function shouldSeeConfigRecordList(ApiTester $I): void
    {
        $this->user = new CrudTestUser($I);
        $this->user->linkSecurity(CrudTestUser::CRUD_VIEW_CONFIG_SECURITY);
        $this->user->login();

        // act
        $I->sendPOST(
            '/Api/V8_Custom/List/CrudRecordsList',
            [
                "page" => 1,
                "recordType" => Dashboard::class,
                "uniqueListKey" => "CrudRecordsList::" . Dashboard::class,
                "filters" => [
                    "name" => [
                        "default" => [
                            "value" => "crud",
                            "fieldId" => '17e6dccb-e2e3-e5c2-a3c4-5c265b411b06',
                        ]
                    ]
                ]
            ]
        );

        // assert
        $I->seeResponseIsDwpResponse(200);
        $I->seeResponseJsonMatchesJsonPath('$.data.rows');
        $rows = $I->grabDataFromResponseByJsonPath('$.data.rows')[0];
        $I->assertNotEmpty($rows);
        $I->seeResponseContainsJson(['line1' => 'Crud Records']);
        $I->seeResponseContainsJson(['line1' => 'CrudBeanRecords']);
        $I->seeResponseContainsJson(['line1' => 'CrudRecordView']);
    }

    public function shouldQuickSearchRecordList(ApiTester $I): void
    {
        // arrange
        $this->user = new CrudTestUser($I);
        $this->user->linkSecurity(CrudTestUser::CRUD_VIEW_CONFIG_SECURITY);
        $this->user->login();

        // act
        $I->sendPOST(
            '/Api/V8_Custom/List/CrudRecordsList',
            [
                "page" => 1,
                "recordType" => "ExEss\Cms\Entity\Dashboard",
                "uniqueListKey" => "CrudRecordsList::ExEss\Cms\Entity\Dashboard",
                "quickSearch" => 'crud',
            ]
        );

        // assert
        $I->seeResponseIsDwpResponse(200);
        $I->seeResponseJsonMatchesJsonPath('$.data.rows');
        $rows = $I->grabDataFromResponseByJsonPath('$.data.rows')[0];
        $I->assertNotEmpty($rows);
        $I->seeResponseContainsJson(['line1' => 'Crud Records']);
        $I->seeResponseContainsJson(['line1' => 'CrudBeanRecords']);
        $I->seeResponseContainsJson(['line1' => 'CrudRecordView']);
    }

    public function shouldSeeRecordRelationsList(ApiTester $I): void
    {
        // Given
        $dashboardId = $I->generateDashboard();
        $propertyId1 = $I->generateDashboardProperty($dashboardId);
        $propertyId2 = $I->generateDashboardProperty($dashboardId);

        $relation = "properties";
        $recordType = Dashboard::class;

        $this->user = new CrudTestUser($I);
        $this->user->linkSecurity(CrudTestUser::CRUD_VIEW_CONFIG_SECURITY);
        $this->user->login();

        // When
        $I->sendPOST(
            '/Api/V8_Custom/List/crud_relations_list',
            [
                "page" => 1,
                'relationName' => $relation,
                'dwp|relationName' => $relation,
                'parentId' => $dashboardId,
                'parentType' => $recordType,
                "recordType" => Property::class,
                "uniqueListKey" => "crud_relations_list::$relation",
                'extraActionData' => [
                    "recordType" => Property::class,
                    "relationName" => $relation,
                    'parentId' => $dashboardId,
                    'parentType' => $recordType,
                ],
            ]
        );

        // Then
        $I->seeResponseIsDwpResponse(200);
        $I->seeResponseJsonMatchesJsonPath('$.data.rows');
        $rows = $I->grabDataFromResponseByJsonPath('$.data.rows')[0];
        $I->assertCount(2, $rows);
        $I->seeResponseContainsJson(['id' => $propertyId1]);
        $I->seeResponseContainsJson(['id' => $propertyId2]);
    }
}
