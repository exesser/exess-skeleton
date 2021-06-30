<?php
namespace ExEss\Bundle\CmsBundle\Acl;

use Doctrine\ORM\EntityManagerInterface;
use ExEss\Bundle\CmsBundle\Entity\Behavior\Acl;
use ExEss\Bundle\CmsBundle\Factory\Mapping\ClassMetadata;
use ExEss\Bundle\CmsBundle\Api\V8_Custom\Service\Security;
use ExEss\Bundle\CmsBundle\Entity\User;
use ExEss\Bundle\CmsBundle\Users\Security\DecisionManager;

class AclService
{
    public const ACL_ALLOW_ADMIN_DEV = 100;
    public const ACL_ALLOW_ADMIN = 99;
    public const ACL_ALLOW_ALL = 90;
    public const ACL_ALLOW_ENABLED = 89;
    public const ACL_ALLOW_GROUP_DEFAULT_ALLOW = 81; // security group default allow
    public const ACL_ALLOW_GROUP = 80; // security group default deny
    public const ACL_ALLOW_OWNER = 75;
    public const ACL_ALLOW_NORMAL = 1;
    public const ACL_ALLOW_DEFAULT = 0;
    public const ACL_ALLOW_DISABLED = -98;
    public const ACL_ALLOW_NONE = -99;
    public const ACL_ALLOW_DEV = 95;

    public const ACL_ALLOW = [
        self::ACL_ALLOW_ALL => 'All',
        self::ACL_ALLOW_NONE => 'None',
        self::ACL_ALLOW_OWNER => 'Owner',
        self::ACL_ALLOW_GROUP => 'Group',
        self::ACL_ALLOW_GROUP_DEFAULT_ALLOW => 'Group Default Allow',
        self::ACL_ALLOW_NORMAL => 'Normal',
        self::ACL_ALLOW_ADMIN => 'Admin',
        self::ACL_ALLOW_ENABLED => 'Enabled',
        self::ACL_ALLOW_DISABLED => 'Disabled',
        self::ACL_ALLOW_DEV => 'Developer',
        self::ACL_ALLOW_ADMIN_DEV => 'Admin & Developer',
        self::ACL_ALLOW_DEFAULT => 'Not Set',
    ];

    public const ACL_ALLOW_ACTION = [
        'access' => [
            self::ACL_ALLOW_ENABLED,
            self::ACL_ALLOW_DEFAULT,
            self::ACL_ALLOW_DISABLED,
        ],
        'create' => [
            self::ACL_ALLOW_ALL,
            self::ACL_ALLOW_DEFAULT,
            self::ACL_ALLOW_NONE,
        ],
        'import' => [
            self::ACL_ALLOW_ALL,
            self::ACL_ALLOW_DEFAULT,
            self::ACL_ALLOW_NONE,
        ],
        'view' => [
            self::ACL_ALLOW_ALL,
            self::ACL_ALLOW_GROUP,
            self::ACL_ALLOW_GROUP_DEFAULT_ALLOW,
            self::ACL_ALLOW_OWNER,
            self::ACL_ALLOW_DEFAULT,
            self::ACL_ALLOW_NONE,
        ],
        'list' => [
            self::ACL_ALLOW_ALL,
            self::ACL_ALLOW_GROUP,
            self::ACL_ALLOW_GROUP_DEFAULT_ALLOW,
            self::ACL_ALLOW_OWNER,
            self::ACL_ALLOW_DEFAULT,
            self::ACL_ALLOW_NONE,
        ],
        'edit' => [
            self::ACL_ALLOW_ALL,
            self::ACL_ALLOW_GROUP,
            self::ACL_ALLOW_GROUP_DEFAULT_ALLOW,
            self::ACL_ALLOW_OWNER,
            self::ACL_ALLOW_DEFAULT,
            self::ACL_ALLOW_NONE,
        ],
        'delete' => [
            self::ACL_ALLOW_ALL,
            self::ACL_ALLOW_GROUP,
            self::ACL_ALLOW_GROUP_DEFAULT_ALLOW,
            self::ACL_ALLOW_OWNER,
            self::ACL_ALLOW_DEFAULT,
            self::ACL_ALLOW_NONE,
        ],
        'export' => [
            self::ACL_ALLOW_ALL,
            self::ACL_ALLOW_GROUP,
            self::ACL_ALLOW_GROUP_DEFAULT_ALLOW,
            self::ACL_ALLOW_OWNER,
            self::ACL_ALLOW_DEFAULT,
            self::ACL_ALLOW_NONE,
        ],
    ];

    public const ACL_ACTIONS = [
        'access' =>
            [
                'aclaccess' => [
                    self::ACL_ALLOW_ENABLED,
                    self::ACL_ALLOW_DEFAULT,
                    self::ACL_ALLOW_DISABLED,
                ],
                'default' => self::ACL_ALLOW_ENABLED,
            ],
        'view' =>
            [
                'aclaccess' => [
                    self::ACL_ALLOW_ALL,
                    self::ACL_ALLOW_GROUP,
                    self::ACL_ALLOW_GROUP_DEFAULT_ALLOW,
                    self::ACL_ALLOW_OWNER,
                    self::ACL_ALLOW_DEFAULT,
                    self::ACL_ALLOW_NONE,
                ],
                'default' => self::ACL_ALLOW_ALL,
            ],
        'list' =>
            [
                'aclaccess' => [
                    self::ACL_ALLOW_ALL,
                    self::ACL_ALLOW_GROUP,
                    self::ACL_ALLOW_GROUP_DEFAULT_ALLOW,
                    self::ACL_ALLOW_OWNER,
                    self::ACL_ALLOW_DEFAULT,
                    self::ACL_ALLOW_NONE,
                ],
                'default' => self::ACL_ALLOW_ALL,
            ],
        'create' =>
            [
                'aclaccess' => [
                    self::ACL_ALLOW_ALL,
                    self::ACL_ALLOW_DEFAULT,
                    self::ACL_ALLOW_NONE,
                ],
                'default' => self::ACL_ALLOW_ALL,

            ],
        'edit' =>
            [
                'aclaccess' => [
                    self::ACL_ALLOW_ALL,
                    self::ACL_ALLOW_GROUP,
                    self::ACL_ALLOW_GROUP_DEFAULT_ALLOW,
                    self::ACL_ALLOW_OWNER,
                    self::ACL_ALLOW_DEFAULT,
                    self::ACL_ALLOW_NONE,
                ],
                'default' => self::ACL_ALLOW_ALL,

            ],
        'delete' =>
            [
                'aclaccess' => [
                    self::ACL_ALLOW_ALL,
                    self::ACL_ALLOW_GROUP,
                    self::ACL_ALLOW_GROUP_DEFAULT_ALLOW,
                    self::ACL_ALLOW_OWNER,
                    self::ACL_ALLOW_DEFAULT,
                    self::ACL_ALLOW_NONE,
                ],
                'default' => self::ACL_ALLOW_ALL,

            ],
        'import' =>
            [
                'aclaccess' => [
                    self::ACL_ALLOW_ALL,
                    self::ACL_ALLOW_DEFAULT,
                    self::ACL_ALLOW_NONE,
                ],
                'default' => self::ACL_ALLOW_ALL,
            ],
        'export' =>
            [
                'aclaccess' => [
                    self::ACL_ALLOW_ALL,
                    self::ACL_ALLOW_GROUP,
                    self::ACL_ALLOW_GROUP_DEFAULT_ALLOW,
                    self::ACL_ALLOW_OWNER,
                    self::ACL_ALLOW_DEFAULT,
                    self::ACL_ALLOW_NONE,
                ],
                'default' => self::ACL_ALLOW_ALL,
            ],
    ];

    // phpcs:disable
    private const JOIN_TABLES = [
        ['security_group_conditional_message', 'conditional_message_id', \ConditionalMessage::class],
        ['security_group_dashboard', 'dashboard_id', \DASH_Dashboard::class],
        ['security_group_dashboard_menu', 'dashboard_menu_id', \DASH_DashboardMenu::class],
        ['security_group_dashboard_menu_action', 'dashboard_menu_action_id', \DASH_MenuActions::class],
        ['security_group_dashboard_menu_action_group', 'dashboard_menu_action_group_id', \DASH_DashboardMenuActionGroup::class],
        ['security_group_property', 'dashboard_property_id', \DASH_DashboardProperties::class],
        ['security_group_external_object_link', 'external_object_link_id', \LIST_external_object_linkfields::class],
        ['security_group_filter', 'filter_id', \FLTRS_Filters::class],
        ['security_group_filter_field', 'filter_field_id', \FLTRS_Fields::class],
        ['security_group_filter_field_group', 'filter_field_group_id', \FLTRS_FieldsGroup::class],
        ['security_group_find_search', 'find_search_id', \FIND_Search::class],
        ['security_group_flow', 'flow_id', \FLW_Flows::class],
        ['security_group_flow_action', 'flow_action_id', \FLW_Actions::class],
        ['security_group_flow_field', 'flow_field_id', \FLW_GuidanceFields::class],
        ['security_group_flow_step_link', 'flow_step_link_id', \FLW_FlowStepsLink::class],
        ['security_group_property', 'property_id', \FLW_FlowStepProperties::class],
        ['security_group_grid_panel', 'grid_panel_id', \GRID_Panels::class],
        ['security_group_grid_template', 'grid_template_id', \GRID_GridTemplates::class],
        ['security_group_list_cell_link', 'list_cell_link_id', \LIST_Cells::class],
        ['security_group_list', 'list_id', \LIST_dynamic_list::class],
        ['security_group_list_row_action', 'list_row_action_id', \LIST_row_action::class],
        ['security_group_list_sorting_option', 'list_sorting_option_id', \LIST_sorting_options::class],
        ['security_group_list_top_action', 'list_top_action_id', \LIST_top_action::class],
        ['security_group_list_top_bar', 'list_top_bar_id', \LIST_topbar::class],
        ['security_group_menu', 'menu_id', \menu_MainMenu::class],
        ['security_group_security_group_api', 'security_group_api_id', 'SecurityGroups_API'],
        ['security_group_validator', 'validator_id', \FLW_GuidanceFieldValidators::class],
    ];
    // phpcs:enable

    private array $acl = [];

    private DecisionManager $decisionManager;

    private Security $security;

    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        Security $security,
        DecisionManager $decisionManager
    ) {
        $this->entityManager = $entityManager;
        $this->decisionManager = $decisionManager;
        $this->security = $security;
    }

    public function reset(): void
    {
        $this->acl = [];
    }

    public function checkAccess(
        string $category,
        string $action,
        object $fatEntity
    ): bool {
        $user = $this->security->getCurrentUser();
        if ($user->isAdmin()
            || $this->decisionManager->hasAccess($action, $fatEntity)
        ) {
            return true;
        }

        $userId = $user->getId();

        if (empty($this->acl[$userId][$category][$action])) {
            $this->getUserActions($userId);
        }

        //check if we don't have it set in the cache if not lets reload the cache
        if ($this->getUserAccessLevel($userId, $category, 'access') < self::ACL_ALLOW_ENABLED) {
            return false;
        }

        if (!empty($this->acl[$userId][$category][$action])) {
            return $this->hasAccess(
                $this->acl[$userId][$category][$action]['aclaccess']
            );
        }

        return false;
    }

    /**
     * @param bool|string $category Yep no kidding...
     */
    public function requireOwner(User $user, $category): bool
    {
        if ($user->isAdmin()) {
            return false;
        }

        //check if we don't have it set in the cache if not lets reload the cache
        $action = 'list';
        if (empty($this->acl[$user->getId()][$category][$action])) {
            $this->getUserActions($user->getId());
        }

        if (!empty($this->acl[$user->getId()][$category][$action])) {
            return $this->acl[$user->getId()][$category][$action]['aclaccess'] == self::ACL_ALLOW_OWNER;
        }

        return false;
    }

    public function requireSecurityGroup(User $user, string $category): bool
    {
        return $this->requireSecurityGroupLevel($user, $category, 'list', self::ACL_ALLOW_GROUP);
    }

    public function requireSecurityGroupDefaultAllow(User $user, string $category): bool
    {
        return $this->requireSecurityGroupLevel($user, $category, 'list', self::ACL_ALLOW_GROUP_DEFAULT_ALLOW);
    }

    /**
     * @param bool|string $category Yep no kidding...
     */
    private function requireSecurityGroupLevel(User $user, $category, string $action, int $level): bool
    {
        if ($user->isAdmin()) {
            return false;
        }

        //check if we don't have it set in the cache if not lets reload the cache
        if (empty($this->acl[$user->getId()][$category][$action])) {
            $this->getUserActions($user->getId());
        }

        if (!empty($this->acl[$user->getId()][$category][$action])) {
            return $this->hasACLAccess($user->getId(), $category, $action, $level);
        }

        return false;
    }

    public function getUserActions(?string $userId, bool $refresh = false): array
    {
        if (empty($userId)) {
            return [];
        }

        //check in the session if we already have it loaded
        if (!$refresh && !empty($this->acl[$userId])) {
            return $this->acl[$userId];
        }

        $actions = $this->fetchUserActions($userId);

        if (!isset($this->acl)) {
            $this->acl = [];
        }
        $this->acl[$userId] = $actions;

        return $actions;
    }

    private function fetchUserActions(string $userId): array
    {
        $query = "
            (
                SELECT acl_actions .*, 1 as user_role
                FROM acl_actions
                INNER JOIN acl_roles_users ON 
                    acl_roles_users.user_id = '$userId' 
                LEFT JOIN acl_roles_actions ON 
                    acl_roles_actions.acl_role_id = acl_roles_users.acl_role_id 
                    AND acl_roles_actions.acl_action_id = acl_actions.id 
            ) UNION (
                SELECT acl_actions .*, 0 as user_role
                FROM acl_actions
                INNER JOIN securitygroups_users ON 
                    securitygroups_users.user_id = '$userId' 
                INNER JOIN securitygroups_acl_roles ON 
                    securitygroups_users.securitygroup_id = securitygroups_acl_roles.security_group_id 
                LEFT JOIN acl_roles_actions ON 
                    acl_roles_actions.acl_role_id = securitygroups_acl_roles.acl_role_id 
                    AND acl_roles_actions.acl_action_id = acl_actions.id 
            ) UNION (
                SELECT acl_actions.*, -1 as user_role
                FROM acl_actions
            )
            ORDER BY user_role desc, category,name desc
        "; //want non-null to show first
        $result = $this->entityManager->getConnection()->executeQuery($query);
        $actions = [];
        $has_user_role = false; //used for user_role_precedence
        $has_role = false; //used to determine if default actions can be ignored. If a user has a defined role don't
        // use the defaults
        while ($row = $result->fetchAssociative()) {
            if ($has_user_role == false && $row['user_role'] == 1) {
                $has_user_role = true;
            }
            if ($has_role == false && ($row['user_role'] == 1 || $row['user_role'] == 0)) {
                $has_role = true;
            }
            //if user roles should take precedence over group roles and we have a user role
            //break when we get to processing the group roles
            if ($has_user_role == true && $row['user_role'] == 0) {
                break;
            }
            if ($row['user_role'] == -1 && $has_role == true) {
                break; //no need for default actions when a role is assigned to the user or user's group already
            }

            $category = $row['category'];
            $name = $row['name'];

            if (!isset($actions[$category])) {
                $actions[$category] = [];
            }
            if (!isset($actions[$category][$name])) {
                $actions[$category][$name] = $row;
                $actions[$category][$name]['isDefault'] = true;
            }
        }

        \ksort($actions);

        return $actions;
    }

    /**
     * @param bool|string $category Yep no kidding...
     * @return int|void
     */
    private function getUserAccessLevel(?string $userId, $category, string $action)
    {
        if (!empty($this->acl[$userId][$category][$action])) {
            if (!empty($this->acl[$userId][$category]['admin'])
                && $this->acl[$userId][$category]['admin']['aclaccess'] >= self::ACL_ALLOW_ADMIN
            ) {
                // If you have admin access for a module, all ACL's are allowed
                return $this->acl[$userId][$category]['admin']['aclaccess'];
            }

            return $this->acl[$userId][$category][$action]['aclaccess'];
        }
    }

    /**
     * @param bool|string $category Yep no kidding...
     */
    private function hasACLAccess(?string $userId, $category, string $action, int $lvl): bool
    {
        $sessionAccess = $this->acl[$userId][$category][$action]['aclaccess'] ?? null;

        return ((int) $sessionAccess) === ((int) $lvl);
    }

    /**
     * @param mixed $access
     */
    private function hasAccess($access): bool
    {
        return $access != 0
            && (
                $access == self::ACL_ALLOW_ALL
                    ||
                $access == self::ACL_ALLOW_OWNER
                    ||
                $access == self::ACL_ALLOW_GROUP
            )
        ;
    }

    /**
     * @todo fix this
     */
    public function getAccessQuery(ClassMetadata $metadata, ?User $forUser = null): string
    {
        if ($metadata->has(Acl::class)) {
            if (!$forUser instanceof User) {
                $forUser = $this->security->getCurrentUser();
            }

            if ($this->requireOwner($forUser, $metadata->getModuleName())) {
                $ownerWhere = $this->getOwnerWhere($metadata, $forUser->getId());
            } elseif ($this->requireSecurityGroup($forUser, $metadata->getModuleName())) {
                $ownerWhere = $this->getOwnerWhere($metadata, $forUser->getId());
                $groupWhere = $this->getGroupWhere(
                    $metadata->getTableName(),
                    $metadata->getModuleName(),
                    $forUser->getId()
                );
            } elseif ($this->requireSecurityGroupDefaultAllow($forUser, $metadata->getModuleName())) {
                $ownerWhere = $this->getOwnerWhere($metadata, $forUser->getId());
                $groupWhere = $this->getGroupWhereDefaultAllow(
                    $metadata->getTableName(),
                    $metadata->getModuleName(),
                    $forUser->getId()
                );
            }
        }

        if (!empty($ownerWhere) && !empty($groupWhere)) {
            return "(" . $ownerWhere . " OR " . $groupWhere . ") ";
        } elseif (!empty($groupWhere)) {
            return $groupWhere;
        } elseif (!empty($ownerWhere)) {
            return $ownerWhere;
        }

        return '';
    }

    private function getOwnerWhere(ClassMetadata $metadata, string $userId): string
    {
        if ($metadata->hasField('assigned_user_id')) {
            return " {$metadata->getTableName()}.assigned_user_id ='$userId' ";
        }
        if ($metadata->hasField('created_by')) {
            return " {$metadata->getTableName()}.created_by ='$userId' ";
        }
        return '';
    }

    /**
     * Gets the join statement used for returning all rows in a list view that a user has group rights to.
     * Make sure any use of this also return records that the user has owner access to.
     * (e.g. caller uses getOwnerWhere as well)
     */
    private function getGroupWhere(string $table_name, string $module, string $user_id): string
    {
        //need a different query if doing a securitygroups check
        if ($module === "SecurityGroups") {
            return " $table_name.id in (
                select secg.id from securitygroups secg
                inner join securitygroups_users secu on secg.id = secu.securitygroup_id 
                    and secu.user_id = '$user_id'
            )";
        }

        foreach (self::JOIN_TABLES as [$joinTable, $lhsField, $lhsModule]) {
            if ($lhsModule === $module) {
                return " EXISTS (SELECT  1
                  FROM securitygroups secg
                          INNER JOIN securitygroups_users secu
                            ON secg.id = secu.securitygroup_id
                               AND secu.user_id = '$user_id'
                          INNER JOIN $joinTable secr
                            ON secg.id = secr.security_group_id
                       WHERE secr.$lhsField = $table_name.id) ";
            }
        }

        return '';
    }

    /**
     * Default allow
     *
     * Gets the join statement used for returning all rows in a list view that a user has group rights to.
     * Make sure any use of this also return records that the user has owner access to.
     * (e.g. caller uses getOwnerWhere as well)
     */
    private function getGroupWhereDefaultAllow(string $table_name, string $module, string $user_id): string
    {
        //need a different query if doing a securitygroups check
        if ($module === "SecurityGroups") {
            return " $table_name.id in (
                select secg.id from securitygroups secg
                inner join securitygroups_users secu on secg.id = secu.securitygroup_id 
                    and secu.user_id = '$user_id'
            )";
        }

        foreach (self::JOIN_TABLES as [$joinTable, $lhsField, $lhsModule]) {
            if ($lhsModule === $module) {
                return " (
                    EXISTS (
                        SELECT 1 
                        FROM securitygroups secg 
                        INNER JOIN securitygroups_users secu 
                            ON secg.id = secu.securitygroup_id 
                            AND secu.user_id = '$user_id'
                        INNER JOIN $joinTable secr
                            ON secg.id = secr.security_group_id
                        WHERE secr.$lhsField = $table_name.id
                    ) 
                    OR NOT EXISTS (
                        SELECT 1 
                        FROM securitygroups secg    
                          INNER JOIN $joinTable secr
                            ON secg.id = secr.security_group_id
                          WHERE secr.$lhsField = $table_name.id
                    )
                )";
            }
        }

        return '';
    }
}
