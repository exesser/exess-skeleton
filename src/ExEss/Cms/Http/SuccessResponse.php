<?php declare(strict_types=1);

namespace ExEss\Cms\Http;

use Symfony\Component\HttpFoundation\Response;

class SuccessResponse extends AbstractJsonResponse
{
    public const MESSAGE_SUCCESS = 'SUCCESS';

    /**
     * @param mixed $data
     */
    public function __construct(
        $data = null,
        int $status = Response::HTTP_OK,
        array $headers = []
    ) {
        if (!($data === null || \is_array($data) || $data instanceof \JsonSerializable)) {
            throw new \InvalidArgumentException(
                "The response data should be null, an array or a json serializable object"
            );
        }
        if ($status < Response::HTTP_OK || $status >= Response::HTTP_MULTIPLE_CHOICES) {
            throw new \InvalidArgumentException(
                "Expected a status code in the 200 range, $status was given"
            );
        }

        $response = [
            'status' => $status,
            'data' => $data,
            'message' => self::MESSAGE_SUCCESS,
        ];

        parent::__construct($response, $status, $headers);
    }
}
