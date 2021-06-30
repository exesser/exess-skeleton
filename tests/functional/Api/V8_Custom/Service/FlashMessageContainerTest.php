<?php declare(strict_types=1);

namespace Test\Functional\Api\V8_Custom\Service;

use ExEss\Bundle\CmsBundle\Api\V8_Custom\Service\FlashMessages\FlashMessage;
use ExEss\Bundle\CmsBundle\Api\V8_Custom\Service\FlashMessages\FlashMessageContainer;
use Helper\Testcase\FunctionalTestCase;

class FlashMessageContainerTest extends FunctionalTestCase
{
    private FlashMessageContainer $container;

    public function _before(): void
    {
        $this->container = $this->tester->grabService(FlashMessageContainer::class);
    }

    public function _after(): void
    {
        $this->container->reset();
    }

    public function testContainer(): void
    {
        $this->container->addFlashMessage(new FlashMessage('test', FlashMessage::TYPE_ERROR));

        $flashMessageContainer2 = $this->tester->grabService(FlashMessageContainer::class);

        static::assertEquals($this->container->count(), $flashMessageContainer2->count());
    }

    public function testCount(): void
    {
        $this->tester->assertEquals(
            0,
            $this->container->count()
        );

        $this->container->addFlashMessage(new FlashMessage('something', FlashMessage::TYPE_ERROR));

        $this->tester->assertEquals(
            1,
            $this->container->count()
        );

        $this->container->addFlashMessage(new FlashMessage('something', FlashMessage::TYPE_ERROR));

        $this->tester->assertEquals(
            1,
            $this->container->count()
        );

        $this->tester->assertEquals(
            $this->container->getFlashMessages()[0]->getCount(),
            2
        );

        $this->container->addFlashMessage(new FlashMessage('somethingNew', FlashMessage::TYPE_ERROR));

        $this->tester->assertEquals(
            2,
            $this->container->count()
        );

        $this->tester->assertEquals(
            $this->container->getFlashMessages()[0]->getCount(),
            2
        );

        $this->tester->assertEquals(
            $this->container->getFlashMessages()[1]->getCount(),
            1
        );

        $this->container->reset();

        $this->tester->assertEquals(
            0,
            $this->container->count()
        );
    }
}
