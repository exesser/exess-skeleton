<?php

namespace ExEss\Cms\CRUD\Helpers;

use ExEss\Cms\Api\V8_Custom\Service\Security;
use ExEss\Cms\Entity;
use ExEss\Cms\Entity\User;
use ExEss\Cms\Exception\NotAllowedException;

class SecurityService
{
    public const CRUD_UPDATE = 'crud_update';
    public const CRUD_VIEW = 'crud_view';
    public const CRUD_MAIN_MODULES = [
        Entity\Dashboard::class,
        Entity\ListDynamic::class,
        Entity\Flow::class,
        Entity\FlowField::class,
    ];
    public const ALL_CONFIG_MODULES = [
        Entity\AclRole::class,
        Entity\AclAction::class,
        Entity\ConditionalMessage::class,
        Entity\ConfDefaults::class,
        Entity\Dashboard::class,
        Entity\DashboardMenu::class,
        Entity\DashboardMenuAction::class,
        Entity\DashboardMenuActionGroup::class,
        Entity\FindSearch::class,
        Entity\Filter::class,
        Entity\FilterField::class,
        Entity\FilterFieldGroup::class,
        Entity\Flow::class,
        Entity\FlowAction::class,
        Entity\FlowStep::class,
        Entity\Property::class,
        Entity\FlowStepLink::class,
        Entity\FlowField::class,
        Entity\Validator::class,
        Entity\GridPanel::class,
        Entity\GridTemplate::class,
        Entity\ListDynamic::class,
        Entity\ListCell::class,
        Entity\ListCellLink::class,
        Entity\ExternalObject::class,
        Entity\ExternalObjectLink::class,
        Entity\ListRowAction::class,
        Entity\ListRowBar::class,
        Entity\ListSortingOption::class,
        Entity\ListTopAction::class,
        Entity\ListTopBar::class,
        Entity\Menu::class,
        Entity\SecurityGroup::class,
        Entity\SecurityGroupApi::class,
        Entity\SecuritygroupsUser::class,
        Entity\SelectWithSearch::class,
        Entity\Translation::class,
        Entity\User::class,
    ];

    private array $crudAllModules;

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
        $this->crudAllModules = \array_merge(self::CRUD_MAIN_MODULES, self::ALL_CONFIG_MODULES);
    }

    public function getViewMainModules(): array
    {
        if (!$this->security->getCurrentUser()->hasMatchedUserGroup([self::CRUD_VIEW])) {
            return [];
        }

        return self::CRUD_MAIN_MODULES;
    }

    public function getViewModules(): array
    {
        if (!$this->security->getCurrentUser()->hasMatchedUserGroup([self::CRUD_VIEW])) {
            return [];
        } // Should be Roles probably

        return \array_diff(self::ALL_CONFIG_MODULES, self::CRUD_MAIN_MODULES);
    }

    private function isCrudModule(string $recordType): bool
    {
        return \in_array($recordType, $this->crudAllModules, true);
    }

    public function checkIfRecordTypeAllowed(string $recordType): void
    {
        if (
            !$this->isCrudModule($recordType)
            || (
                !$this->security->getCurrentUser()->hasMatchedUserGroup([self::CRUD_VIEW, self::CRUD_UPDATE])
            )
        ) {
            throw new NotAllowedException('Access Denied.');
        }
    }

    public function isIfRelationAllowed(array $relation): bool
    {
        try {
            $this->checkIfRecordTypeAllowed($relation['lhs_module']);
            $this->checkIfRecordTypeAllowed($relation['rhs_module']);
        } catch (NotAllowedException $e) {
            return false;
        }

        return true;
    }

    public function canUpdate(string $recordType): bool
    {
        return $this->isCrudModule($recordType)
            && $this->security->getCurrentUser()->hasMatchedUserGroup([self::CRUD_UPDATE])
            && $recordType !== User::class;
    }
}
