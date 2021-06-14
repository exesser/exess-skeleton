<?php

namespace ExEss\Cms\Servicemix\Response;

interface SelectableItemInterface
{
    public function getDropdownKey(): string;

    public function getDropdownValue(): string;
}
