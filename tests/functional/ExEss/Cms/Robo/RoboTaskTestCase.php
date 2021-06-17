<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\Robo;

use Robo\Robo;
use Helper\Testcase\FunctionalTestCase;

class RoboTaskTestCase extends FunctionalTestCase
{
    public function _before(): void
    {
        Robo::createDefaultContainer();
    }
}
