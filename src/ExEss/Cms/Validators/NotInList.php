<?php

namespace ExEss\Cms\Validators;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\MissingOptionsException;

/**
 * @Annotation
 */
class NotInList extends Constraint
{
    /**
     * @var array|string|null
     */
    public $list;

    public string $message = "The value `{{ value }}` has been found in the list!";

    /**
     * @param mixed $options
     * @throws MissingOptionsException When one of the needed parameters is not present in the options.
     */
    public function __construct($options = null)
    {
        parent::__construct($options);

        if ($this->list === null) {
            throw new MissingOptionsException(
                'Error handling NotInList validator, the list parameter is not specified',
                []
            );
        }

        if (\is_string($this->list) && \strpos($this->list, '|') !== false) {
            $this->list = \explode('|', $this->list);
        }
    }

    public function getDefaultOption(): string
    {
        return 'list';
    }
}
