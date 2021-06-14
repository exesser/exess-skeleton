<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Health\Model;

class HealthCheckResult
{
    public const OK = 'OK';

    private bool $result;

    private string $message;

    public function __construct(bool $result = true, string $message = self::OK)
    {
        $this->result = $result;
        $this->message = $message;
    }

    public function getResult(): bool
    {
        return $this->result;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
