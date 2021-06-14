<?php
namespace ExEss\Cms\Api\V8_Custom\Sidebar;

use ExEss\Cms\Api\V8_Custom\Params\SidebarParams;

class SidebarFactory
{
    public function __construct()
    {
    }

    public function createSidebar(SidebarParams $params): SidebarInterface
    {
        // @todo implement this in a configurable way
        return new ConsumerBlueSidebar();
    }
}
