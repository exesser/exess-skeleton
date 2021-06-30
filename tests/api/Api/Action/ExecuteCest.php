<?php declare(strict_types=1);

namespace Test\Api\Api\Action;

use ApiTester;
use ExEss\Bundle\CmsBundle\Helper\DataCleaner;
use ExEss\Bundle\CmsBundle\Http\ErrorResponse;

class ExecuteCest
{
    public function shouldReturn(ApiTester $I): void
    {
        // Given
        $I->generateFlowAction([
            'guid' => $key = $I->generateUuid(),
            'json' => '{"command": "reloadPage"}',
        ]);
        $I->getAnApiTokenFor('adminUser');

        // When
        $I->sendPost("/Api/action/$key");

        // Then
        $I->seeResponseIsDwpResponse(200);
        $I->seeAssertPathsInJson([
            '$.data.command' => 'reloadPage',
        ]);
    }

    public function withEmptyId(ApiTester $I): void
    {
        // Given
        $I->getAnApiTokenFor('adminUser');

        // When
        $I->sendPost('/Api/action/0');

        // Then
        $I->seeResponseIsDwpResponse(404);
        $I->seeAssertPathsInJson([
            '$.data.message' => 'Not Found'
        ]);
    }

    public function withNonExistingId(ApiTester $I): void
    {
        // Given
        $I->getAnApiTokenFor('adminUser');

        // When
        $I->sendPost('/Api/action/huppeldepup');

        // Then
        $I->seeResponseIsDwpResponse(404);
        $I->seeAssertPathsInJson([
            '$.data.message' => 'Not Found'
        ]);
    }

    public function actionHasNoCommand(ApiTester $I): void
    {
        // Given
        $I->generateFlowAction([
            'guid' => 'my_test_action',
        ]);
        $I->getAnApiTokenFor('adminUser');

        // When
        $I->sendPost('/Api/action/my_test_action');

        // Then
        $I->seeResponseIsDwpResponse(404);
        $response = DataCleaner::jsonDecode($I->grabResponse());

        $I->assertEquals($response['message'], ErrorResponse::MESSAGE_ERROR);
        $I->assertEquals($response['data']['type'], ErrorResponse::TYPE_NOT_FOUND_EXCEPTION);
        $I->assertTrue(\strpos($response['data']['message'], 'my_test_action') !== false);
    }

    public function actionHasNonExistingBackendCommand(ApiTester $I): void
    {
        // Given
        $I->generateFlowAction([
            'guid' => 'my_test_action',
            'json' => '{"command":"yippie", "backendCommand":"ka-yee"}',
        ]);
        $I->getAnApiTokenFor('adminUser');

        // When
        $I->sendPost('/Api/action/my_test_action');

        // Then
        $I->seeResponseIsDwpResponse(422);
        $I->seeAssertPathsInJson([
            '$.message' => ErrorResponse::MESSAGE_ERROR,
        ]);
    }

    public function actionWithPayloadAndWrongConfig(ApiTester $I): void
    {
        // Given
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

        // When
        $I->sendPost('/Api/action/some_action', DataCleaner::jsonDecode($payload));

        // Then
        $I->seeResponseIsDwpResponse(422);
    }

    public function actionWithPayloadAndCorrectConfig(ApiTester $I): void
    {
        // Given
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

        // When
        $I->sendPost('/Api/action/some_action', DataCleaner::jsonDecode($payload));

        // Then
        $I->seeResponseIsDwpResponse(200);
        $I->assertEquals(
            'VKM1770002708',
            $I->grabDataFromResponseByJsonPath("$.data.arguments.model['dwp|id']")[0]
        );
    }
}
