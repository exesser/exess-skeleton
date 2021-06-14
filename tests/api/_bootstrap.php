<?php declare(strict_types=1);

use ExEss\Cms\Entity\User;

\Codeception\Util\Fixtures::add('adminUser', [
    'username' =>
        $settings['admin_user'] ?? $settings['settings']['admin_user'] ?? User::USERNAME_ADMIN,
    'password' =>
        $settings['admin_pass'] ?? $settings['settings']['admin_pass'] ?? 'ch4ng3m3pl5',
]);
