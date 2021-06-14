<?php

namespace ExEss\Cms\Exception;

class ExternalListFetchException extends \DomainException
{
    /**
     * @var string
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
    protected $message = 'There have been an error if fetching data from external list source.';
}
