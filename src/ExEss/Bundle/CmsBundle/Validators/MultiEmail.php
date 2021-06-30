<?php

namespace ExEss\Bundle\CmsBundle\Validators;

use Symfony\Component\Validator\Constraints\Email;

/**
 * @Annotation
 */
class MultiEmail extends Email
{
    public function validatedBy(): string
    {
        return MultiEmailValidator::class;
    }
}
