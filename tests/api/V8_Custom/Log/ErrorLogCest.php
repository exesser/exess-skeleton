<?php declare(strict_types=1);

namespace Test\Api\V8_Custom\Log;

use ApiTester;
use ExEss\Cms\Http\SuccessResponse;

class ErrorLogCest
{
    // @codingStandardsIgnoreStart
    private $body = <<<JSON
        {  
           "url":"http://localhost:9005/#/sales-marketing/dashboard/accounts_list/",
           "state":{  
              "mainMenuKey":"sales-marketing",
              "dashboardId":"accounts_list",
              "recordId":""
           },
           "name":"HTTP error: 500"
        }
JSON;
    // @codingStandardsIgnoreEnd

    public function shouldReturn(ApiTester $I): void
    {
        $I->getAnApiTokenFor('adminUser');

        $I->sendPOST('/Api/V8_Custom/log/error', \json_decode($this->body, true));

        // assertions
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $assertPaths = [
            '$.data.' => 'OK',
            '$.message' => SuccessResponse::MESSAGE_SUCCESS,
        ];

        $I->seeAssertPathsInJson($assertPaths);
    }
}
