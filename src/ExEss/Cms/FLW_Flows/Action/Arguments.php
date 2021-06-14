<?php
namespace ExEss\Cms\FLW_Flows\Action;

use ExEss\Cms\Generic\ToArray;

class Arguments implements ToArray
{
    /**
     * @return array
     */
    public function toArray(): array
    {
        return (array) $this;
    }
}
