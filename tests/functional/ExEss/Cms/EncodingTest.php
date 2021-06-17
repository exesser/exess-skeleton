<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms;

use Helper\Testcase\FunctionalTestCase;

class EncodingTest extends FunctionalTestCase
{
    public function testEncoding(): void
    {
        $this->tester->assertEquals(
            'en_US.UTF-8',
            \setLocale(\LC_CTYPE, 0)
        );
    }
}
