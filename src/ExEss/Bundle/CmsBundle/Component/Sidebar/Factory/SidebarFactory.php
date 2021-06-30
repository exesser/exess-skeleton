<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\Sidebar\Factory;

use ExEss\Bundle\CmsBundle\Component\Sidebar\ConsumerBlueSidebar;
use ExEss\Bundle\CmsBundle\Component\Sidebar\SidebarInterface;

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
