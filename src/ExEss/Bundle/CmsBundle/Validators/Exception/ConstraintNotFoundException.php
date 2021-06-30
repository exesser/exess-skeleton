<?php
namespace ExEss\Bundle\CmsBundle\Validators\Exception;

class ConstraintNotFoundException extends \Exception
{
    /**
     * Generates an exception with a default message
     *
     */
    public static function fromName(string $name): ConstraintNotFoundException
    {
        return new ConstraintNotFoundException(\sprintf('Could not find a constraint class for %s', $name));
    }
}
