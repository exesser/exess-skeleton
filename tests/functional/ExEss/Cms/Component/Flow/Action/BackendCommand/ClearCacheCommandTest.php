<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\Component\Flow\Action\BackendCommand;

use ExEss\Cms\Api\V8_Custom\Service\FlashMessages\FlashMessage;
use ExEss\Cms\Api\V8_Custom\Service\FlashMessages\FlashMessageContainer;
use ExEss\Cms\Cache\CacheAdapterFactory;
use ExEss\Cms\Dictionary\Model\Dwp;
use ExEss\Cms\Component\Flow\Action\BackendCommand\ClearCacheCommand;
use ExEss\Cms\Component\Flow\Response\Model;
use Helper\Testcase\FunctionalTestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class ClearCacheCommandTest extends FunctionalTestCase
{
    public function testCanClearCaches(): void
    {
        // Given
        $cacheNamespace = 'ny-namespace';
        $cacheItemKey = 'some-item';
        $cacheItemValue = 'some-item-value';

        $cache = new ArrayAdapter();

        $cacheFactory = \Mockery::mock(CacheAdapterFactory::class);
        $cacheFactory->shouldReceive('create')->once()->with($cacheNamespace)->andReturn($cache);
        $this->tester->mockService(CacheAdapterFactory::class, $cacheFactory);

        $command = $this->tester->grabService(ClearCacheCommand::class);
        /** @var FlashMessageContainer $flashMessageContainer */
        $flashMessageContainer = $this->tester->grabService(FlashMessageContainer::class);
        $flashMessageContainer->reset();

        // When
        $item = $cache->getItem($cacheItemKey);
        $item->set($cacheItemValue);
        $cache->save($item);

        // Then
        $this->tester->assertTrue($cache->getItem($cacheItemKey)->isHit(), 'cache was hit');
        $this->tester->assertEquals($cacheItemValue, $cache->getItem($cacheItemKey)->get(), 'cache has value');

        // When
        $command->execute([], new Model([Dwp::CACHE_KEY => $cacheNamespace]));

        // Then
        $this->tester->assertFalse($cache->getItem($cacheItemKey)->isHit());
        $this->tester->assertCount(1, $flashMessageContainer->getFlashMessages());
        $this->tester->assertTrue($flashMessageContainer->getFlashMessages()[0]->equals(
            new FlashMessage(
                "Cache of type $cacheNamespace have been cleared",
                FlashMessage::TYPE_SUCCESS
            )
        ));

        $flashMessageContainer->reset();
    }
}
