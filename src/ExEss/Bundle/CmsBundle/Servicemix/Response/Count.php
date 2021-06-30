<?php

namespace ExEss\Bundle\CmsBundle\Servicemix\Response;

use ExEss\Bundle\CmsBundle\Base\Response\BaseResponse;

class Count extends BaseResponse
{
    private int $count = 0;

    public function __construct(int $count = 0)
    {
        $this->count = $count;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
