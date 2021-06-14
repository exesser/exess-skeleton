<?php declare(strict_types=1);

namespace Test\Api\V8_Custom\Action;

use ApiTester;
use ExEss\Cms\Dictionary\Response;

class FetchActionByIdCest
{
    public function shouldReturn(ApiTester $I): void
    {
        // setup
        $I->generateFlowAction([
            'guid' => $key = $I->generateUuid(),
            'json' => '{"command": "reloadPage"}',
        ]);

        // run test
        $I->getAnApiTokenFor('adminUser');
        $I->sendPOST("/Api/V8_Custom/Action/$key");

        // assertions
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeAssertPathsInJson([
            '$.data.command' => 'reloadPage',
        ]);
    }

    public function withEmptyId(ApiTester $I): void
    {
        // setup
        $I->getAnApiTokenFor('adminUser');

        // run test
        $I->sendPOST('/Api/V8_Custom/Action/0');

        // assertions
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeAssertPathsInJson([
            '$.message' => 'No result was found for query although at least one row was expected.'
        ]);
    }

    public function withNonExistingId(ApiTester $I): void
    {
        // setup
        $I->getAnApiTokenFor('adminUser');

        // run test
        $I->sendPOST('/Api/V8_Custom/Action/huppeldepup');

        // assertions
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeAssertPathsInJson([
            '$.message' => 'No result was found for query although at least one row was expected.'
        ]);
    }

    public function actionHasNoCommand(ApiTester $I): void
    {
        // setup
        $I->generateFlowAction([
            'guid' => 'my_test_action',
        ]);

        // run test
        $I->getAnApiTokenFor('adminUser');

        $I->sendPOST('/Api/V8_Custom/Action/my_test_action');

        // assertions
        $I->seeResponseCodeIs(422);
        $I->seeResponseIsJson();

        $response = \json_decode($I->grabResponse(), true);

        $I->assertEquals($response['message'], Response::MESSAGE_ERROR);
        $I->assertEquals($response['data']['type'], Response::TYPE_DOMAIN_EXCEPTION);
        $I->assertTrue(\strpos($response['data']['message'], 'my_test_action') !== false);
    }

    public function actionHasNonExistingBackendCommand(ApiTester $I): void
    {
        // setup
        $I->generateFlowAction([
            'guid' => 'my_test_action',
            'json' => '{"command":"yippie", "backendCommand":"ka-yee"}',
        ]);

        // run test
        $I->getAnApiTokenFor('adminUser');

        $I->sendPOST('/Api/V8_Custom/Action/my_test_action');

        // assertions
        $I->seeResponseCodeIs(422);
        $I->seeResponseIsJson();
        $I->seeAssertPathsInJson([
            '$.message' => Response::MESSAGE_ERROR,
        ]);
    }

    public function actionWithPayloadAndWrongConfig(ApiTester $I): void
    {
        $I->loadJsonFixturesFrom(__DIR__  . '/resources/FetchAction.fixtures.json');

        $payload = '{
            "id": "some_action",
            "recordId": "VKM1770002708",
            "listKey": "InvoicesOnAccount",
            "recordType": null,
            "actionData": {
                "parentId": "67338d36-30aa-249c-b8d8-59dd5800e50a"
            }
        }';

        $I->getAnApiTokenFor('adminUser');
        $I->sendPOST('/Api/V8_Custom/Action/some_action', \json_decode($payload, true));
        $I->seeResponseIsDwpResponse(422);
    }

    public function actionWithPayloadAndCorrectConfig(ApiTester $I): void
    {
        $I->loadJsonFixturesFrom(__DIR__  . '/resources/FetchAction.fixtures.json');

        $payload = '{
            "id": "some_action",
            "recordId": "VKM1770002708",
            "listKey": "InvoicesOnAccount",
            "recordType": null,
            "actionData": {
                "parentId": "67338d36-30aa-249c-b8d8-59dd5800e50a"
            }
        }';

        $I->updateInDatabase(
            'flw_guidancefields',
            ['field_overwrite_value' => '', 'field_default' => '%recordId%'],
            ['id' => 'field_01']
        );

        $I->getAnApiTokenFor('adminUser');
        $I->sendPOST('/Api/V8_Custom/Action/some_action', \json_decode($payload, true));
        $I->canSeeResponseCodeIs(200);
        $I->assertEquals(
            'VKM1770002708',
            $I->grabDataFromResponseByJsonPath("$.data.arguments.model['dwp|id']")[0]
        );
    }
}
