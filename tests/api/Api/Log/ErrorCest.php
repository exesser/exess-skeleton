<?php declare(strict_types=1);

namespace Test\Api\Api\Log;

use ApiTester;
use ExEss\Cms\Http\SuccessResponse;

class ErrorCest
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
        // Given
        $I->getAnApiTokenFor('adminUser');

        // When
        $I->sendPOST('/Api/log/error', \json_decode($this->body, true));

        // Then
        $I->seeResponseIsDwpResponse(200);
        $I->seeAssertPathsInJson([
            '$.message' => SuccessResponse::MESSAGE_SUCCESS,
        ]);
    }
}
