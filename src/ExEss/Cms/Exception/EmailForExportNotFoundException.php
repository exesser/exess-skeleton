<?php
namespace ExEss\Cms\Exception;

class EmailForExportNotFoundException extends \DomainException
{
    /**
     * @var string
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
    protected $message = 'You have no e-mailaddress to send the csv export to';
}
