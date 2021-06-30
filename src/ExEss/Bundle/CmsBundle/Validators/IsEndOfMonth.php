<?php

namespace ExEss\Bundle\CmsBundle\Validators;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsEndOfMonth extends Constraint
{
    public string $message = "The value `{{ value }}` should be the last day of the month!";

    /**
     * @param mixed $options
     */
    public function __construct($options = null)
    {
        parent::__construct($options);
    }
}
