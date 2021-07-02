<?php
namespace App\Robo\Formatter;

use Symfony\Component\Console\Formatter\OutputFormatter;

class MaskedOutputFormatter extends OutputFormatter
{
    private const REPLACEMENT = '********';

    private string $stringToMask;

    /**
     * @return $this
     */
    public function setStringToMask(string $stringToMask): self
    {
        $this->stringToMask = $stringToMask;

        return $this;
    }

    /**
     * @param string $message
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
    public function format($message): string
    {
        if (!empty($this->stringToMask)) {
            // extra spacing is added to avoid that occurences in a full text are replaced as well
            $message = \str_replace($this->stringToMask . ' ', static::REPLACEMENT . ' ', $message);
        }
        return parent::format($message);
    }
}
