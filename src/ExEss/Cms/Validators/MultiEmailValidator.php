<?php

namespace ExEss\Cms\Validators;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\EmailValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class MultiEmailValidator extends EmailValidator
{
    /**
     * @param mixed $value
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof MultiEmail) {
            throw new UnexpectedTypeException($constraint, MultiEmail::class);
        }

        if (empty($value)) {
            return;
        }

        foreach (\explode(';', \trim($value)) as $email) {
            parent::validate($email, $constraint);
        }
    }
}
