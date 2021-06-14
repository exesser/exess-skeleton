<?php

namespace ExEss\Cms\Validators;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\MissingOptionsException;

/**
 * @Annotation
 */
class InList extends Constraint
{
    public ?array $list;

    public string $message = "The value `{{ value }}` has not been found in the list!";

    /**
     * @param mixed $options
     * @throws MissingOptionsException When one of the needed parameters is not present in the options.
     */
    public function __construct($options = null)
    {
        parent::__construct($options);

        if ($this->list === null) {
            throw new MissingOptionsException(
                'Error handling InList validator, the list parameter is not specified',
                []
            );
        }
    }
}
