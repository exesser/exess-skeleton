<?php

namespace ExEss\Cms\Api\Core;

use ExEss\Cms\Http\ErrorResponse;
use ExEss\Cms\Http\SuccessResponse;
use Slim\Http\Response;
use ExEss\Cms\Api\V8_Custom\Service\DataCleaner;

abstract class AbstractApiController
{
    protected bool $decode = true;

    /**
     * @param mixed $data
     */
    public function generateResponse(
        Response $response,
        int $status,
        $data = null,
        ?string $message = null
    ): Response {
        $body = [
            'status' => $status,
            'data' => $this->decode ? DataCleaner::decodeData($data) : $data,
            'message' => $message ??
                ($status > 320 ? ErrorResponse::MESSAGE_ERROR : SuccessResponse::MESSAGE_SUCCESS),
        ];

        return $response
            ->withStatus($status)
            ->withHeader('Content-type', 'application/json')
            ->write(
                \json_encode(
                    $body,
                    \JSON_PRETTY_PRINT | \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES
                )
            );
    }
}
