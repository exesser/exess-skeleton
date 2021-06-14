<?php

namespace ExEss\Cms\Validators;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UserId extends Constraint
{
    public const USER_NOT_FOUND_ERROR = 'd5912334-cc06-4748-83dc-6c19f75b6c98';

    /**
     * @var string[]
     */
    protected static array $errorNames = [
        self::USER_NOT_FOUND_ERROR => 'USER_NOT_FOUND'
    ];

    public string $id;

    public string $userNotValid = 'User with id {{ id }} not found';

    public function validatedBy(): string
    {
        return UserIdValidator::class;
    }
}
