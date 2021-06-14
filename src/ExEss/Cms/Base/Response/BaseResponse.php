<?php

namespace ExEss\Cms\Base\Response;

class BaseResponse
{
    protected bool $result = true;

    protected ?string $msg = null;

    /**
     * @return bool
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
    public function getResult()
    {
        return $this->result;
    }

    public function setResult(bool $result): void
    {
        $this->result = $result;
    }

    public function getMessage(): ?string
    {
        return $this->msg;
    }

    public function setMessage(?string $message): void
    {
        $this->msg = $message;
    }
}
