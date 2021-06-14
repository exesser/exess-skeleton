<?php declare(strict_types=1);

namespace Test\Api\V8_Custom\Crud;

use ApiTester;
use ExEss\Cms\Entity\User;
use ExEss\Cms\FLW_Flows\SaveFlow;

class DashboardCest
{
    private CrudTestUser $user;

    public function _before(ApiTester $I): void
    {
        $this->user = new CrudTestUser($I);
        $this->user->linkSecurity(CrudTestUser::CRUD_VIEW_ALL_SECURITY);

        $this->user->login();
    }

    public function shouldSeeCrudDashboard(ApiTester $I): void
    {
        $I->sendGET('/Api/V8_Custom/Dashboard/CrudAllBeans');
        $I->seeResponseIsDwpResponse(200);
        $I->seeResponseContainsJson(["title" => "Crud Records"]);
        $I->seeResponseContainsJson(["type" => "list"]);
        $I->seeResponseContainsJson(["listKey" => "CrudAllBeans"]);
    }

    public function shouldSeeCrudRecordsDashboard(ApiTester $I): void
    {
        $I->sendGET('/Api/V8_Custom/Dashboard/CrudBeanRecords?recordType=' . User::class);
        $I->seeResponseIsDwpResponse(200);
        $I->seeResponseContainsJson(["type" => "list"]);
        $I->seeResponseContainsJson(["listKey" => "CrudRecordsList"]);
    }

    public function shouldSeeRecordDetailDashboard(ApiTester $I): void
    {
        $I->sendGET('/Api/V8_Custom/Dashboard/CrudRecordView/1?recordType=' . User::class);
        $I->seeResponseIsDwpResponse(200);
        $I->seeResponseContainsJson(["type" => "embeddedGuidance"]);
        $I->seeResponseContainsJson(["flowId" => SaveFlow::CRUD_RECORD_DETAILS]);
    }
}
