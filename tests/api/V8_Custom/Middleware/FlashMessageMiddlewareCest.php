<?php declare(strict_types=1);

namespace Test\Api\V8_Custom\Middleware;

use ExEss\Cms\Api\V8_Custom\Service\FlashMessages\FlashMessage;
use ExEss\Cms\Api\V8_Custom\Service\FlashMessages\FlashMessageContainer;

class FlashMessageMiddlewareCest
{
    public function testIfMessageIsReturned(\ApiTester $I): void
    {
        // Given
        $I->getAnApiTokenFor('adminUser');

        /** @var FlashMessageContainer $flashMessageContainer */
        $flashMessageContainer = $I->grabService(FlashMessageContainer::class);
        $flashMessageContainer->addFlashMessage(new FlashMessage('test'));

        // When
        $I->sendGET('/Api/V8_Custom/CRUD/records-information');

        // Then
        $I->seeResponseCodeIs(200);
        $I->seeResponseJsonMatchesJsonPath('flashMessages.0.text', 'test');
    }
}
