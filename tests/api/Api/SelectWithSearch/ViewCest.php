<?php declare(strict_types=1);

namespace Test\Api\Api\SelectWithSearch;

use ApiTester;
use ExEss\Cms\Entity\User;

class ViewCest
{
    private string $fieldName;

    public function _before(ApiTester $I): void
    {
        $I->generateSelectWithSearchDatasource([
            'name' => $this->fieldName = $I->generateUuid(),
            'base_object' => User::class,
            'filter_string' => '%userName%',
            "option_label" => "%firstName% %lastName%",
        ]);

        for ($x = 1; $x <= 10; $x++) {
            $I->generateUser("user_$x");
        }
    }

    public function shouldReturnAllResults(ApiTester $I): void
    {
        // Given
        $I->getAnApiTokenFor('adminUser');

        // When
        $I->sendPOST(
            "/Api/select-with-search/{$this->fieldName}",
            [
                'query' => '',
                'page' => 1,
                'fullModel' => [],
            ]
        );

        // Then
        $I->seeResponseIsDwpResponse(200);
        $I->assertNotEmpty('$.data.rows');
    }

    public function shouldReturnOneResult(ApiTester $I): void
    {
        // Given
        $prefix = $I->generateUuid();
        $expectedUserId = $I->generateUser("{$prefix}user");

        $I->getAnApiTokenFor('adminUser');

        // When
        $I->sendPOST(
            "/Api/select-with-search/{$this->fieldName}",
            [
                'query' => $prefix,
                'page' => 1,
                'fullModel' => [],
            ]
        );

        // Then
        $I->seeResponseIsDwpResponse(200);
        $I->assertNotEmpty('$.data.rows');
        $I->seeAssertPathsInJson([
            '$.data.rows.[0].key' => $expectedUserId,
            '$.data.pagination.total' => 1
        ]);
    }
}
