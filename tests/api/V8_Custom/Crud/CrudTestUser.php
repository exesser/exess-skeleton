<?php declare(strict_types=1);

namespace Test\Api\V8_Custom\Crud;

use ExEss\Bundle\CmsBundle\Doctrine\Type\Locale;
use ExEss\Bundle\CmsBundle\Doctrine\Type\UserStatus;
use ExEss\Bundle\CmsBundle\Entity\User;

class CrudTestUser
{
    public const CRUD_VIEW_ALL_SECURITY = '820fdd5f-06cc-25b8-b025-5bf4167f9177';
    public const CRUD_EDIT_ALL_SECURITY = '82b26313-7214-3a65-0d39-5bf416338c5d';
    public const CRUD_VIEW_CONFIG_SECURITY = 'c07e4fb0-3479-cb06-8c6f-5bf416af19bd';
    public const CRUD_EDIT_CONFIG_SECURITY = 'a2ea5516-9cc7-e22a-f28d-5bf4163601dd';

    private string $userId;
    private string $userName;
    private string $password;

    private \ApiTester $I;

    public function __construct(\ApiTester $I)
    {
        $this->I = $I;

        $this->userName = \md5($I->generateUuid());
        $this->password = \md5($I->generateUuid());

        $this->userId = $I->generateUser(
            $this->userName,
            [
                'salt' => $salt = (new User)->getSalt(),
                'user_hash' => User::getPasswordHash($this->password, $salt),
                'status' => UserStatus::ACTIVE,
                'preferred_locale' => Locale::EN,
            ]
        );
    }

    public function login(): void
    {
        $this->I->getAnApiTokenFor($this->userName, $this->password);
    }

    public function getId(): string
    {
        return $this->userId;
    }

    public function linkSecurity(string $security): void
    {
        $this->I->linkUserToSecurityGroup($this->userId, $security);
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }
}
