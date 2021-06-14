<?php declare(strict_types=1);

namespace Helper;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Additional methods for DB module
 *
 * Save this file as DbHelper.php in _support folder
 * Enable DbHelper in your suite.yml file
 * Execute `codeception build` to integrate this class in your codeception
 */
class CleanupHelper extends \Codeception\Module
{
    protected function getDbHelperModule(): DbHelper
    {
        return $this->getModule('\Helper\DbHelper');
    }

    public function cleanDatabaseForUser(string $userId): void
    {
        $dbHelper = $this->getDbHelperModule();

        $dbHelper->deleteFromDatabase('acl_roles_users', ['user_id' => $userId]);
        $dbHelper->deleteFromDatabase('securitygroups_users', ['user_id' => $userId]);
        $dbHelper->deleteFromDatabase('users', ['id' => $userId]);
    }

    public function cleanDirectory(string $directory): void
    {
        $items = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            if ($item->isFile()) {
                \unlink($item);
            } else {
                \rmdir($item);
            }
        }
    }
}
