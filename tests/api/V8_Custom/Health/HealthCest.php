<?php declare(strict_types=1);

namespace Test\Api\V8_Custom\Health;

use ApiTester;

class HealthCest
{
    public function aHealthCheckSucceeds(ApiTester $I): void
    {
        $I->sendGET('/Api/V8_Custom/check/health');

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsXml();
        $I->seeXmlResponseMatchesXpath('//result');
    }
}
