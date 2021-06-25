<?php declare(strict_types=1);

namespace ExEss\Cms\Http;

use ExEss\Cms\Helper\DataCleaner;
use Symfony\Component\HttpFoundation\Response;

class ErrorResponse extends AbstractJsonResponse
{
    public const MESSAGE_ERROR = 'ERROR';

    public const TYPE_NOT_FOUND_EXCEPTION = 'NOT_FOUND_EXCEPTION';
    public const TYPE_NOT_ALLOWED_EXCEPTION = 'NOT_ALLOWED_EXCEPTION';
    public const TYPE_DOMAIN_EXCEPTION = 'DOMAIN_EXCEPTION';
    public const TYPE_FATAL_ERROR = 'FATAL_ERROR';

    /**
     * @param array|string|null $information
     */
    public function __construct(
        int $status,
        $information = null,
        array $headers = []
    ) {
        if ($status < Response::HTTP_MULTIPLE_CHOICES) {
            throw new \InvalidArgumentException(
                "Expected a status code in the 300+ range, $status was given"
            );
        }

        $response = [
            'status' => $status,
            'data' => \is_array($information) ?
                $information :
                ['message' => $information ?? self::$statusTexts[$status] ?? null],
            'message' => self::MESSAGE_ERROR,
        ];

        parent::__construct($response, $status, $headers);
    }

    public function addDebugInformation(\Throwable $e): void
    {
        $data = DataCleaner::jsonDecode($this->data);
        $data['data']['debug'] = [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'stacktrace' => \explode(\PHP_EOL, $e->getTraceAsString()),
        ];

        $this->setData($data);
    }

    public static function errorData(string $type, string $message): array
    {
        return [
            'type' => $type,
            'message' => $message,
        ];
    }
}
