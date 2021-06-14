<?php declare(strict_types=1);

namespace Test\Unit\Api\V8_Custom\Service\FlashMessages;

use ExEss\Cms\Test\Testcase\UnitTestCase;
use ExEss\Cms\Api\V8_Custom\Service\FlashMessages\FlashMessage;

class FlashMessageTest extends UnitTestCase
{
    public function testConstructor(): void
    {
        $flashMessage = new FlashMessage('something', FlashMessage::TYPE_ERROR);

        $this->tester->assertEquals(
            [
                'type' => 'ERROR',
                'text' => 'something',
                'group' => FlashMessage::GROUP_DEFAULT
            ],
            $flashMessage->jsonSerialize()
        );

        $flashMessage = new FlashMessage('something', FlashMessage::TYPE_SUCCESS, 'some-group');

        $this->tester->assertEquals(
            [
                'type' => 'SUCCESS',
                'text' => 'something',
                'group' => 'some-group'
            ],
            $flashMessage->jsonSerialize()
        );

        $flashMessage = new FlashMessage('something', FlashMessage::TYPE_INFORMATION);

        $this->tester->assertEquals(
            [
                'type' => 'INFORMATION',
                'text' => 'something',
                'group' => FlashMessage::GROUP_DEFAULT
            ],
            $flashMessage->jsonSerialize()
        );

        $flashMessage = new FlashMessage('something', FlashMessage::TYPE_WARNING);

        $this->tester->assertEquals(
            [
                'type' => 'WARNING',
                'text' => 'something',
                'group' => FlashMessage::GROUP_DEFAULT
            ],
            $flashMessage->jsonSerialize()
        );
    }

    public function testConstructorWrongType(): void
    {
        $flashMessage = new FlashMessage('something', 'something', 'some-group');

        $this->tester->assertEquals(
            [
                'type' => 'ERROR',
                'text' => 'something',
                'group' => 'some-group'
            ],
            $flashMessage->jsonSerialize()
        );
    }
}
