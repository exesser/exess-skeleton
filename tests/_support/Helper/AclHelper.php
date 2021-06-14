<?php declare(strict_types=1);

namespace Helper;

use Codeception\Module\Doctrine2;
use Codeception\Module\Symfony;
use Codeception\TestInterface;
use ExEss\Cms\Acl\AclService;
use ExEss\Cms\Api\V8_Custom\Service\User\DefaultUser;
use ExEss\Cms\Command\Traits\LoginTrait;
use ExEss\Cms\Entity\AclRole;
use ExEss\Cms\Entity\User;
use ExEss\Cms\Test\Codeception\Module\Db;

class AclHelper extends \Codeception\Module
{
    use LoginTrait;

    private array $recordsToRestore = [];

    private RecordGeneratorHelper $recordGenerator;

    public function _before(TestInterface $test): void
    {
        $this->recordGenerator = $this->getModule('\Helper\RecordGeneratorHelper');

        // shouldn't be needed, but maybe a test changed these and didn't revert
        $this->getDb()->updateInDatabase(
            'users',
            [
                'preferred_locale' => 'en_BE',
                'user_name' => User::USERNAME_ADMIN,
            ],
            ['id' => '1']
        );

        $this->login(
            $this->getSymfony()->grabService('security.token_storage'),
            $this->getSymfony()->grabService(DefaultUser::class)
        );

        $this->getDoctrine()->onReconfigure();
    }

    public function loginAsUser(User $user): void
    {
        $this->loginAs(
            $this->getSymfony()->grabService('security.token_storage'),
            $user
        );
    }

    private function getDbHelper(): DbHelper
    {
        return $this->getModule('\Helper\DbHelper');
    }

    private function getDoctrine(): Doctrine2
    {
        return $this->getModule('Doctrine2');
    }

    private function getSymfony(): Symfony
    {
        return $this->getModule('Symfony');
    }

    protected function getDb(): Db
    {
        return $this->getModule('ExEss\Cms\Test\Codeception\Module\Db');
    }

    public function grantsAclRightsTo(string $userId, string $module, string $name, int $aclAccess): void
    {
        $dbHelper = $this->getDbHelper();

        // acl params for this access
        $criteria = [
            'name' => $name,
            'category' => $module,
        ];

        // query for existing records for this acl_action and soft delete them
        $this->recordsToRestore['acl_actions'] = $dbHelper->grabAllFromDatabase(
            'acl_actions',
            '*',
            $criteria
        );
        $this->recordsToRestore['acl_roles_actions'] = [];
        foreach ($this->recordsToRestore['acl_actions'] as $aclAction) {
            $this->recordsToRestore['acl_roles_actions'] = \array_merge(
                $this->recordsToRestore['acl_roles_actions'],
                $dbHelper->grabAllFromDatabase(
                    'acl_roles_actions',
                    '*',
                    ['acl_action_id' => $aclAction['id']]
                )
            );
        }
        $dbHelper->deleteFromDatabase('acl_actions', $criteria);

        // setup the acl right
        $roleId = $dbHelper->grabAllFromDatabase(
            'acl_roles',
            'id',
            ['code' => AclRole::DEFAULT_ROLE_CODE]
        )[0]['id'];
        $actionId = $this->recordGenerator->generateAclAction($criteria + [
            'aclaccess' => $aclAccess,
        ]);
        $this->recordGenerator->linkDynamicAclRoleToAclAction($roleId, $actionId);

        // trigger (re)loading of the acl for the user
        /** @var AclService $aclService */
        $aclService = $this->getSymfony()->grabService(AclService::class);
        $aclService->reset();
    }

    /**
     * Removes all previously inserted records
     */
    public function _after(TestInterface $test): void
    {
        // undelete the original records in the database
        foreach ($this->recordsToRestore as $table => $records) {
            $this->getDbHelper()->restoreInDatabase($table, $records);
            unset($this->recordsToRestore[$table]);
        }

        // unload acl
        /** @var AclService $aclService */
        $aclService = $this->getSymfony()->grabService(AclService::class);
        $aclService->reset();
    }
}
