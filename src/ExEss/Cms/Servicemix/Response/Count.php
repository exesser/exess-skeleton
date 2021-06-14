<?php

namespace ExEss\Cms\Servicemix\Response;

use ExEss\Cms\Base\Response\BaseResponse;

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
