<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\Component\Cache;

use ExEss\Bundle\CmsBundle\Component\Cache\CacheAdapterFactory;
use Helper\Testcase\FunctionalTestCase;

class CacheAdapterFactoryTest extends FunctionalTestCase
{
    public function testActiveCache(): void
    {
        // Given
        $cache = (new CacheAdapterFactory(
            $this->tester->grabService('test.monolog.logger'),
            false,
            $this->tester->grabParameter('cache_host'),
            $this->tester->grabParameter('cache_port')
        ))->create('');

        $key = $this->tester->generateUuid();

        // When
        $item = $cache->getItem($key);

        // Then
        $this->tester->assertFalse($item->isHit());

        // Given
        $value = $this->tester->generateUuid();
        $item->set($value);

        // When
        $cache->save($item);

        // Then
        $item = $cache->getItem($key);
        $this->tester->assertTrue($item->isHit());
        $this->tester->assertEquals($value, $item->get());

        // When
        $cache->deleteItem($key);

        // Then
        $this->tester->assertFalse($cache->getItem($key)->isHit());
    }
}
