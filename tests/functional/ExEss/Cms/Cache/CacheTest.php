<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\Cache;

use ExEss\Cms\Cache\CacheAdapterFactory;
use ExEss\Cms\Test\Testcase\FunctionalTestCase;

class CacheTest extends FunctionalTestCase
{
    public function testActiveCache(): void
    {
        $cache = (new CacheAdapterFactory(
            $this->tester->grabService('test.monolog.logger'),
            false,
            $this->tester->grabParameter('cache_host'),
            $this->tester->grabParameter('cache_port')
        ))->create('');
        $cache->deleteItem('test');

        $key = 'test';
        $item = $cache->getItem($key);

        $this->tester->assertFalse($item->isHit());

        $item->set('test-value');
        $cache->save($item);

        $item = $cache->getItem($key);
        $this->tester->assertTrue($item->isHit());
        $this->tester->assertEquals('test-value', $item->get());

        $cache->deleteItem('test');
        $item = $cache->getItem($key);

        $this->tester->assertFalse($item->isHit());
    }
}
