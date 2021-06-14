<?php
namespace ExEss\Cms\Api\V8_Custom\Sidebar;

class ConsumerBlueSidebar extends AbstractBlueSidebar
{
    protected function getTitle(): string
    {
        return 'CUSTOMER';
    }

    protected function getTitleContact(): string
    {
        return 'Contact Details';
    }
}
