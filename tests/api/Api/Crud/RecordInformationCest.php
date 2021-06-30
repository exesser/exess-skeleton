<?php declare(strict_types=1);

namespace Test\Api\Api\Crud;

use ApiTester;
use ExEss\Bundle\CmsBundle\Helper\DataCleaner;
use ExEss\Bundle\CmsBundle\Http\SuccessResponse;

class RecordInformationCest
{
    public function shouldReturnRecordsInformation(ApiTester $I): void
    {
        // Given
        $I->getAnApiTokenFor('adminUser');

        // When
        $I->sendGet('/Api/crud/record/information');

        // Then
        $I->seeResponseIsDwpResponse(200);
        $response = DataCleaner::jsonDecode($I->grabResponse());

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
            "message" => SuccessResponse::MESSAGE_SUCCESS,
        ];
        $files = \glob(__DIR__ . '/resources/records_information/*.json');

        foreach ($files as $file) {
            $recordData = DataCleaner::jsonDecode(\file_get_contents($file));
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
