<?php declare(strict_types=1);

namespace Test\Api\V8_Custom\Crud;

use ApiTester;

class RecordsInformationCest
{
    public function shouldReturnRecordsInformation(ApiTester $I): void
    {
        // setup
        $I->getAnApiTokenFor('adminUser');

        // run test
        $I->sendGET('/Api/V8_Custom/CRUD/records-information');

        // assertions
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $response = \json_decode($I->grabResponse(), true);

        // we want this here commented since it's used when changing the structure,
        // Bogdan can explain this to you in detail
        // $this->generateTestData($response);

        $I->assertArrayEqual($this->getExpectedResponse(), $response);
    }

    private function getExpectedResponse(): array
    {
        $response = [
            "status" => 200,
            "data" => [],
            "message" => "SUCCESS",
        ];
        $files = \glob(__DIR__ . '/resources/records_information/*.json');

        foreach ($files as $file) {
            $recordData = \json_decode(\file_get_contents($file), true);
            $response['data'][$recordData['recordName']] = $recordData;
        }

        return $response;
    }

    /**
     * to be used when test is failing and we want to generate the test data from scratch
     */
    private function generateTestData(array $response): void
    {
        foreach ($response['data'] as $record) {
            $fileName = \str_replace('\\', '_', $record['recordName']);
            \file_put_contents(
                __DIR__ . "/resources/records_information/$fileName.json",
                \json_encode($record, \JSON_PRETTY_PRINT)
            );
        }
    }
}
