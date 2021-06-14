<?php
namespace ExEss\Cms\Api\V8_Custom\Service\User;

use ExEss\Cms\Dictionary\Format;
use ExEss\Cms\Entity\User;

class UserService
{
    private string $fallbackLocale;

    public function __construct(
        string $fallbackLocale
    ) {
        $this->fallbackLocale = $fallbackLocale;
    }

    public function getData(User $currentUser): array
    {
        try {
            $email = $currentUser->getEmail();
        } catch (\DomainException $e) {
            $email = '';
        }

        return [
            'user_name' => $currentUser->getUserName() ?? '',
            'last_name' => $currentUser->getLastName() ?? '',
            'first_name' => $currentUser->getFirstName() ?? '',
            'full_name' => $currentUser->getName(),
            'date_entered' => $currentUser->getDateEntered()->format(Format::DB_DATETIME_FORMAT) ?? '',
            'email1' => $email,
            'status' => $currentUser->getStatus() ?? '',
            'is_admin' => $currentUser->isAdmin(),
            'preferred_language' => $currentUser->getPreferredLocale() ?? $this->fallbackLocale,
        ];
    }
}
