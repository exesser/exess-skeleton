<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\Logger\Processor;

use ExEss\Bundle\CmsBundle\Component\Session\Headers;

class XLogIdProcessor
{
    private ?Headers $headers = null;

    public function __construct(?Headers $headers = null)
    {
        $this->headers = $headers;
    }

    public function __invoke(array $record): array
    {
        if ($this->headers instanceof Headers && $this->headers->has(Headers::LOG_ID)) {
            $record['extra'][Headers::LOG_ID] = $this->headers->get(Headers::LOG_ID);
        } elseif (isset($_SERVER['HTTP_X_LOG_ID'])) {
            $record['extra'][Headers::LOG_ID] = $_SERVER['HTTP_X_LOG_ID'];
        }

        return $record;
    }
}
