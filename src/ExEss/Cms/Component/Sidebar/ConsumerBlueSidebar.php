<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Sidebar;

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
