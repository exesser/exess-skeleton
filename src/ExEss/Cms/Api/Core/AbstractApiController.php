<?php

namespace ExEss\Cms\Api\Core;

use Slim\Http\Response as HttpResponse;
use ExEss\Cms\Api\V8_Custom\Service\DataCleaner;
use ExEss\Cms\Dictionary\Response;

abstract class AbstractApiController
{
    protected bool $decode = true;

    /**
     * @param mixed $data
     */
    public function generateResponse(
        HttpResponse $response,
        int $status,
        $data = null,
        ?string $message = null
    ): HttpResponse {
        $body = [
            'status' => $status,
            'data' => $this->decode ? DataCleaner::decodeData($data) : $data,
            'message' => $message ?? ($status > 320 ? Response::MESSAGE_ERROR : Response::MESSAGE_SUCCESS),
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
