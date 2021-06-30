<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\Sidebar;

use JsonSerializable;

interface SidebarInterface extends JsonSerializable
{
    /**
     * Get data of the sidebar
     */
    public function jsonSerialize(): array;
}
