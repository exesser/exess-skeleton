<?php declare(strict_types=1);

namespace Test\Api\Api\Dashboard;

use ApiTester;
use ExEss\Bundle\CmsBundle\Entity\User;
use ExEss\Bundle\CmsBundle\Component\Flow\SaveFlow;
use Test\Api\V8_Custom\Crud\CrudTestUser;

class CrudCest
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
        // When
        $I->sendGet('/Api/dashboard/CrudAllBeans');

        // Then
        $I->seeResponseIsDwpResponse(200);
        $I->seeResponseContainsJson(["title" => "Crud Records"]);
        $I->seeResponseContainsJson(["type" => "list"]);
        $I->seeResponseContainsJson(["listKey" => "CrudAllBeans"]);
    }

    public function shouldSeeCrudRecordsDashboard(ApiTester $I): void
    {
        // When
        $I->sendGet('/Api/dashboard/CrudBeanRecords?recordType=' . User::class);

        // Then
        $I->seeResponseIsDwpResponse(200);
        $I->seeResponseContainsJson(["type" => "list"]);
        $I->seeResponseContainsJson(["listKey" => "CrudRecordsList"]);
    }

    public function shouldSeeRecordDetailDashboard(ApiTester $I): void
    {
        // When
        $I->sendGet('/Api/dashboard/CrudRecordView/1?recordType=' . User::class);

        // Then
        $I->seeResponseIsDwpResponse(200);
        $I->seeResponseContainsJson(["type" => "embeddedGuidance"]);
        $I->seeResponseContainsJson(["flowId" => SaveFlow::CRUD_RECORD_DETAILS]);
    }
}
