<?php
namespace ExEss\Cms\Api\V8_Custom\Params;

class ApiLoginParams extends LoginParams
{
    public function returnJwt(): bool
    {
        return true;
    }
}
