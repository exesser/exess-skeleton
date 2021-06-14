<?php declare(strict_types=1);

namespace Test\Api\V8_Custom\Health;

use ApiTester;
use Codeception\Util\Xml;

class PingCest
{
    public function aPingCheckSucceeds(ApiTester $I): void
    {
        $xml = [
            'rs-response' => [
                'result' => 'true',
            ],
        ];

        $I->sendGET('/Api/V8_Custom/check/ping');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsXml();
        $I->seeXmlResponseMatchesXpath('/rs-response/result');
        $I->seeXmlResponseIncludes(Xml::toXml(\array_shift($xml)));
    }
}
