<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Sidebar\Factory;

use ExEss\Cms\Component\Sidebar\ConsumerBlueSidebar;
use ExEss\Cms\Component\Sidebar\SidebarInterface;

class SidebarFactory
{
    public function __construct()
    {
    }

    public function createSidebar(object $baseObject): SidebarInterface
    {
        // @todo implement this in a configurable way
        return new ConsumerBlueSidebar();
    }
}
