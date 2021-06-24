<?php declare(strict_types=1);

namespace Test\Api\Api\Flow;

use ApiTester;
use ExEss\Cms\FLW_Flows\SaveFlow;
use Test\Api\V8_Custom\Crud\CrudTestUser;

class FlowCest
{
    private string $gridId;
    private string $id;
    private string $dashboardMenuId;

    private CrudTestUser $user;

    public function _before(ApiTester $I): void
    {
        $this->dashboardMenuId = $I->generateDashboardMenu(['name' => 'test-dashboard-menu']);
        $this->gridId = $I->generateGrid(['name' => 'test-grid']);
        $this->id = $I->generateDashboard([
            'name' => 'dash_name',
            'key_c' => 'any',
            'grid_gridtemplates_id_c' => $this->gridId,
            'dashboard_menu_id' => $this->dashboardMenuId,
        ]);

        $this->user = new CrudTestUser($I);
    }

    public function _after(ApiTester $I): void
    {
        $I->deleteFromDatabase('trans_translation', ['modified_user_id' => $this->user->getId()]);
    }

    public function shouldNotSeeConfigRecordDetailsFlow(ApiTester $I): void
    {
        // Given
        $this->user->login();

        // When
        $I->sendPost(
            '/Api/flow/ExEss%5CCms%5CEntity%5CDashboard/' . SaveFlow::CRUD_RECORD_DETAILS . '/' . $this->id
        );

        // Then
        $I->seeResponseIsDwpResponse(403);
    }

    public function shouldSeeConfigRecordDetailsFlow(ApiTester $I): void
    {
        // Given
        $this->user->linkSecurity(CrudTestUser::CRUD_VIEW_CONFIG_SECURITY);
        $this->user->login();

        // When
        $I->sendPost(
            '/Api/flow/ExEss%5CCms%5CEntity%5CDashboard/' . SaveFlow::CRUD_RECORD_DETAILS . '/' . $this->id
        );

        // Then
        $I->seeResponseIsDwpResponse(200);
        $I->seeResponseContainsJson(['id' => $this->id]);
        $I->seeResponseContainsJson(['name' => 'dash_name']);
        $I->seeResponseContainsJson(['key' => 'any']);
    }

    public function shouldNotSeeConfigRecordEditFlow(ApiTester $I): void
    {
        // Given
        $this->user->login();

        // When
        $I->sendPost(
            '/Api/flow/ExEss%5CCms%5CEntity%5CDashboard/' . SaveFlow::CRUD_EDIT . '/' . $this->id
        );

        // Then
        $I->seeResponseIsDwpResponse(403);
    }

    public function shouldSeeConfigRecordEditFlow(ApiTester $I): void
    {
        // Given
        $this->user->linkSecurity(CrudTestUser::CRUD_VIEW_CONFIG_SECURITY);
        $this->user->linkSecurity(CrudTestUser::CRUD_EDIT_CONFIG_SECURITY);
        $this->user->login();

        // When
        $I->sendPost(
            '/Api/flow/ExEss%5CCms%5CEntity%5CDashboard/' . SaveFlow::CRUD_EDIT . '/' . $this->id
        );

        // Then
        $I->seeResponseIsDwpResponse(200);
        $expectedResponse = $I->loadJsonWithParams(__DIR__ . '/resources/expectedResponseForEdit.json', [
            "dashboardId" => $this->id,
            "dashboardMenuId" => $this->dashboardMenuId,
            "gridId" => $this->gridId,
        ]);
        $I->assertArrayEqual($expectedResponse, \json_decode($I->grabResponse(), true));
    }
}
