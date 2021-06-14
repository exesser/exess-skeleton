<?php declare(strict_types=1);

namespace Test\Api\V8_Custom\Access;

use ApiTester;
use ExEss\Cms\Doctrine\Type\HttpMethod;

class PreflightCest
{
    public function shouldSucceedForV8Custom(ApiTester $I): void
    {
        $dashboard = $I->generateDashboard();
        $I->haveHttpHeader('Access-Control-Request-Method', HttpMethod::GET);
        $I->sendOPTIONS(
            \sprintf('/Api/V8_Custom/dashboard/%s', $dashboard)
        );

        // assertions
        $I->seeResponseCodeIs(200);
    }
}
