<?php

namespace ExEss\Bundle\CmsBundle\Servicemix\Response;

interface SelectableItemInterface
{
    public function getDropdownKey(): string;

    public function getDropdownValue(): string;
}
