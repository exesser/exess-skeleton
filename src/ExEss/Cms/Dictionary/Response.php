<?php

namespace ExEss\Cms\Dictionary;

class Response
{
    public const MESSAGE_SUCCESS = 'SUCCESS';
    public const MESSAGE_ERROR = 'ERROR';

    public const TYPE_NOT_FOUND_EXCEPTION = 'NOT_FOUND_EXCEPTION';
    public const TYPE_NOT_ALLOWED_EXCEPTION = 'NOT_ALLOWED_EXCEPTION';
    public const TYPE_DOMAIN_EXCEPTION = 'DOMAIN_EXCEPTION';
    public const TYPE_FATAL_ERROR = 'FATAL_ERROR';
    public const TYPE_NON_UPDATABLE_STAGE_EXCEPTION = 'NON_UPDATABLE_STAGE_EXCEPTION';

    /**
     * Creates the error data
     */
    public static function errorData(string $type, string $message): array
    {
        return [
            'type' => $type,
            'message' => $message,
        ];
    }
}
