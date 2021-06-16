<?php declare(strict_types=1);

namespace ExEss\Cms\Service;

use ExEss\Cms\Api\V8_Custom\Service\User\CommandService;
use ExEss\Cms\Dictionary\Format;
use ExEss\Cms\Entity\User;

class UserService
{
    private string $fallbackLocale;

    private CommandService $commandService;

    public function __construct(
        string $fallbackLocale,
        CommandService $commandService
    ) {
        $this->fallbackLocale = $fallbackLocale;
        $this->commandService = $commandService;
    }

    public function getDataFor(User $currentUser): array
    {
        try {
            $email = $currentUser->getEmail();
        } catch (\DomainException $e) {
            $email = '';
        }

        $data = [
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

        if (null !== ($command = $this->commandService->getRedirectCommandForUser($currentUser))) {
            $data['command'] = $command;
        }

        return $data;
    }
}
