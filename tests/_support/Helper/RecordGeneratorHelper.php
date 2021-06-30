<?php declare(strict_types=1);

namespace Helper;

use ExEss\Bundle\CmsBundle\Dictionary\Format;
use ExEss\Bundle\CmsBundle\Doctrine\Type\UserStatus;
use ExEss\Bundle\CmsBundle\Entity\AclRole;
use Ramsey\Uuid\Uuid;
use Helper\Module\Db;

/**
 *  Methods to generate fat entities on the fly
 *  to make your life less horrid when writing tests with complex data structures
 */
class RecordGeneratorHelper extends \Codeception\Module
{
    private function getDb(): Db
    {
        return $this->getModule(Db::class);
    }

    public function generateConfDefaults(array $data = []): string
    {
        $data += [
            'created_by' => 1,
        ];

        return $this->genArrayFixture('conf_defaults', $data);
    }

    public function generateConditionalMessage(array $data = []): string
    {
        return $this->genArrayFixture(
            'conditionalmessage',
            $data + [
                'created_by' => 1,
                'date_entered' => \date(Format::DB_DATETIME_FORMAT),
            ]
        );
    }

    public function generateTranslation(array $data): string
    {
        $data += [
            'created_by' => 1,
            'date_entered' => \date(Format::DB_DATETIME_FORMAT),
        ];

        return $this->genArrayFixture('trans_translation', $data);
    }

    public function generateUser(string $name, array $data = []): string
    {
        $userId = $this->genArrayFixture(
            'users',
            $data + [
                'user_name' => $name,
                'status' => UserStatus::ACTIVE,
                'employee_status' => 'Active',
                'created_by' => 1,
                'date_entered' => '2017-01-06 00:00:00',
            ]
        );
        $this->linkUserToRole($userId, AclRole::DEFAULT_ROLE_CODE);

        return $userId;
    }

    public function generateUserLogin(string $userId, string $lastLogin): string
    {
        return $this->genArrayFixture(
            'user_login',
            [
                'id' => $userId,
                'last_login' => $lastLogin,
            ]
        );
    }

    public function generateUserGuidanceRecovery(string $userId, ?array $recoveryData = null): string
    {
        return $this->genArrayFixture(
            'user_guidance_recovery',
            [
                'id' => $userId,
                'recovery_data' => $recoveryData ? \json_encode($recoveryData) : null,
            ]
        );
    }

    public function linkUserToRole(string $userId, string $roleCode): void
    {
        //we accept role codes and id's here
        $roleId = null;
        if (!Uuid::isValid($roleCode)) {
            $roleId = $this->getDb()->grabFromDatabase('acl_roles', 'id', ['code' => $roleCode]);
        }

        $this->linkArrayFixture(
            'acl_roles_users',
            [
                'user_id' => $userId,
                'acl_role_id' => $roleId
            ]
        );
    }

    public function generateSecurityGroup(string $name, array $data = []): string
    {
        return $this->genArrayFixture(
            'securitygroups',
            $data + [
                'name' => $name,
                'created_by' => 1,
            ]
        );
    }

    public function generateSecurityApiRecord(
        string $method,
        string $route,
        string $allowedUserGroups,
        array $data = []
    ): string {
        return $this->genArrayFixture(
            'securitygroups_api',
            $data + [
                'name' => $route,
                'created_by' => 1,
                'http_method' => $method,
                'route' => $route,
                'allowed_usergroups' => $allowedUserGroups
            ]
        );
    }

    public function linkUserToSecurityGroup(string $userId, string $securityGroup, array $data = []): string
    {
        //we accept name's and id's here
        if (!Uuid::isValid($securityGroup)) {
            $securityGroup = $this->getDb()->grabFromDatabase('securitygroups', 'id', ['name' => $securityGroup]);
        }

        return $this->genArrayFixture(
            'securitygroups_users',
            $data + [
                'user_id' => $userId,
                'securitygroup_id' => $securityGroup,
            ]
        );
    }

    public function linkSecurityGroupList(string $listId, string $securityGroupId): void
    {
        $this->linkArrayFixture('security_group_list', [
            'list_id' => $listId,
            'security_group_id' => $securityGroupId,
        ]);
    }

    public function linkSecurityGroupConditionalMessage(string $conditionalMessageId, string $securityGroupId): void
    {
        $this->linkArrayFixture('security_group_conditional_message', [
            'conditional_message_id' => $conditionalMessageId,
            'security_group_id' => $securityGroupId,
        ]);
    }

    public function linkSecurityGroupListCellLink(string $cellLinkId, string $securityGroupId): void
    {
        $this->linkArrayFixture('security_group_list_cell_link', [
            'list_cell_link_id' => $cellLinkId,
            'security_group_id' => $securityGroupId,
        ]);
    }

    public function generateSecurityGroupUsers(string $securityGroupId, string $userId, array $data = []): string
    {
        return $this->genArrayFixture(
            'securitygroups_users',
            $data + [
                'securitygroup_id' => $securityGroupId,
                'user_id' => $userId,
            ]
        );
    }

    public function generateDynamicList(array $data = []): string
    {
        return $this->genArrayFixture(
            'list_dynamic_list',
            $data + [
                'created_by' => 1,
                'date_entered' => \date(Format::DB_DATETIME_FORMAT),
            ]
        );
    }

    public function generateTopBar(array $data = []): string
    {
        return $this->genArrayFixture(
            'list_topbar',
            $data + [
                'created_by' => 1,
            ]
        );
    }

    public function generateListCellForList(string $listId, array $data = [], array $linkData = []): string
    {
        $cellId = $this->generateListCell($data);

        $this->generateListLinkCell($listId, $linkData + ['cell_id' => $cellId]);

        return $cellId;
    }

    public function generateListCell(array $data = []): string
    {
        return $this->genArrayFixture(
            'list_cell',
            $data + [
                'created_by' => 1,
                'date_entered' => '2017-01-06 00:00:00',
            ]
        );
    }

    public function generateExternalLinkField(array $data = []): string
    {
        return $this->genArrayFixture(
            'list_external_object_linkfields',
            $data + [
                'created_by' => 1,
            ]
        );
    }

    public function generateListLinkCell(?string $listId = null, array $data = []): string
    {
        return $this->genArrayFixture(
            'list_cells',
            $data + [
                'created_by' => 1,
                'date_entered' => '2017-01-06 00:00:00',
                'list_id' => $listId,
            ]
        );
    }

    public function generateListRowBar(array $data = []): string
    {
        return $this->genArrayFixture(
            'list_row_bar',
            $data + [
                'created_by' => 1,
            ]
        );
    }

    public function generateListRowBarAction(
        ?string $rowBarId = null,
        ?string $flowActionId = null,
        array $data = []
    ): string {
        return $this->genArrayFixture(
            'list_row_action',
            $data + [
                'created_by' => 1,
                'row_bar_id' => $rowBarId,
                'flow_action_id' => $flowActionId,
            ]
        );
    }

    public function generateSelectWithSearchDatasource(array $data = []): string
    {
        $data += [
            'created_by' => 1,
            "items_on_page" => "10",
            "option_label" => "%name%",
            "filter_string" => "%name%",
            "option_key" => "%id%"
        ];

        return $this->genArrayFixture('fe_selectwithsearch', $data);
    }

    public function generateDashboard(array $data = []): string
    {
        $data += [
            'created_by' => 1,
            'dashboard_menu_id' => $this->generateDashboardMenu(),
        ];

        return $this->genArrayFixture('dash_dashboard', $data);
    }

    public function generateDashboardSearch(array $data = []): string
    {
        $data += [
            'created_by' => 1,
            'link_to' => 'dashboard',
        ];

        return $this->genArrayFixture('find_search', $data);
    }

    public function generateDashboardProperty(string $dashboardId, ?string $name = null, array $data = []): string
    {
        if (!$name) {
            $name = $this->generateUuid();
        }

        $this->linkArrayFixture(
            'dash_dashboard_dash_dashboardproperties_c',
            [
                'dashboard_id' => $dashboardId,
                'property_id' => $propertyId = $this->generateProperty($name, $data),
            ]
        );

        return $propertyId;
    }

    public function generateGridPanel(array $data = [], ?array $params = null): string
    {
        $data += [
            'created_by' => 1,
        ];

        if ($params) {
            $data['params'] = \json_encode($params);
        }

        return $this->genArrayFixture('grid_panels', $data);
    }

    public function generateGrid(array $data = [], ?array $grid = null): string
    {
        $data += [
            'created_by' => 1,
        ];

        if ($grid) {
            $data['json_fields_c'] = \json_encode($grid);
        }

        return $this->genArrayFixture('grid_gridtemplates', $data);
    }

    public function generateProperty(string $name, array $data = []): string
    {
        $data += [
            'created_by' => 1,
            'name' => $name,
        ];

        return $this->genArrayFixture('properties', $data);
    }

    public function generateFlowStepProperty(string $flowStepId, string $name, array $data = []): string
    {
        $this->linkArrayFixture(
            'flw_flowsteps_flw_flowstepproperties_1_c',
            [
                'flow_step_id' => $flowStepId,
                'property_id' => $propertyId = $this->generateProperty($name, $data),
            ]
        );

        return $propertyId;
    }

    public function generateDashboardMenu(array $data = []): string
    {
        $data += [
            'created_by' => 1,
        ];

        $menuId = $this->genArrayFixture('dash_dashboardmenu', $data);

        $this->generateMenuActionGroup(['label_c' => 'SALES'], $menuId);
        $this->generateMenuActionGroup(['label_c' => 'SERVICE'], $menuId);
        $this->generateMenuActionGroup(['label_c' => 'BILLING'], $menuId);
        $relation = 'dash_dashboardmenu_dash_menuactions_1';
        $this->generateMenuAction(
            ['params_c' => '{"recordId": "%recordId%","recordType": "%recordType%"}'],
            $relation,
            $menuId
        );
        $this->generateMenuAction(
            ['params_c' => '{"recordId": "%recordId%","recordType": "%recordType%"}'],
            $relation,
            $menuId
        );
        $this->generateMenuAction(
            [],
            $relation,
            $menuId
        );

        return $menuId;
    }

    public function generateMenuActionGroup(array $data = [], string $dashboardMenu = ''): string
    {
        $data += [
            'created_by' => 1,
        ];

        $actionGroupId = $this->genArrayFixture('dash_dashboardmenuactiongroup', $data);

        $this->linkArrayFixture(
            'dash_dashboardmenu_dash_dashboardmenuactiongroup_1_c',
            [
                'dashboard_menu_action_group_id' => $actionGroupId,
                'dashboard_menu_id' => $dashboardMenu
            ]
        );

        $relation = 'dash_dashboardmenuactiongroup_dash_menuactions_1';
        $this->generateMenuAction(
            ['params_c' => '{"recordId": "%recordId%","recordType": "%recordType%"}'],
            $relation,
            $actionGroupId,
        );
        $this->generateMenuAction(
            ['params_c' => '{"recordId": "%recordId%","recordType": "%recordType%"}'],
            $relation,
            $actionGroupId,
        );
        $this->generateMenuAction(
            [],
            $relation,
            $actionGroupId,
        );

        return $actionGroupId;
    }

    public function generateMenuAction(array $data = [], string $relation = '', string $relatedId = ''): string
    {
        $data += [
            'flw_actions_id_c' => $this->generateFlowAction(),
            'created_by' => 1,
        ];
        $menuActionId = $this->genArrayFixture('dash_menuactions', $data);

        switch ($relation) {
            case 'dash_dashboardmenu_dash_menuactions_1':
                $this->linkArrayFixture(
                    'dash_dashboardmenu_dash_menuactions_1_c',
                    [
                        'dashboard_menu_action_id' => $menuActionId,
                        'dashboard_menu_id' => $relatedId,
                    ]
                );
                break;
            case 'dash_dashboardmenuactiongroup_dash_menuactions_1':
                $this->linkArrayFixture(
                    'dash_dashboardmenuactiongroup_dash_menuactions',
                    [
                        'dashboard_menu_action_id' => $menuActionId,
                        'dashboard_menu_action_group_id' => $relatedId,
                    ]
                );
                break;
        }

        return $menuActionId;
    }

    public function generateFlowAction(array $data = []): string
    {
        $data += [
            'guid' => $this->generateUuid(),
            'created_by' => 1,
        ];

        return $this->genArrayFixture('flw_actions', $data);
    }

    public function generateFieldValidator(array $data = []): string
    {
        $data += [
            'created_by' => 1,
        ];

        return $this->genArrayFixture('flw_guidancefieldvalidators', $data);
    }

    protected function genArrayFixture(string $tableName, array $data = []): string
    {
        $data += [
            'id' => $this->generateUuid(),
        ];

        /** @var FixturesHelper $helper */
        $helper = $this->getModule('\Helper\FixturesHelper');
        $helper->haveArrayFixture($tableName, $data);

        return $data['id'];
    }

    protected function linkArrayFixture(string $tableName, array $data = []): void
    {
        /** @var FixturesHelper $helper */
        $helper = $this->getModule('\Helper\FixturesHelper');
        $helper->haveArrayFixture($tableName, $data);
    }

    /**
     * Generates a random UUID
     */
    public function generateUuid(): string
    {
        return Uuid::uuid4()->toString();
    }

    public function generateGuidanceWitRepeatableBlock(
        string $flowKey = 'flow-id',
        string $repeatableKey = 'aos_products_quotes'
    ): string {

        $flowId = $this->generateFlow(
            [
                "name" => $flowKey,
                "key_c" => $flowKey,
            ]
        );

        $this->generateFlowSteps(
            $flowId,
            [],
            [
                "json_fields_c" => \json_encode(
                    [
                        "columns" => [
                            [
                                "size" => "1-1",
                                "rows" => [
                                    [
                                        "size" => "1-1",
                                        "type" => "centeredGuidanceGrid",
                                        "options" => [
                                            "grid" => [
                                                "columns" => [
                                                    [
                                                        "size" => "1-1",
                                                        "rows" => [
                                                            [
                                                                "size" => "1-4",
                                                                "type" => "embeddedGuidance",
                                                                "options" => [
                                                                    "repeatsBy" => "productId",
                                                                    "recordType" => "Accounts",
                                                                    "flowId" => "my-child-flow-key",
                                                                    "recordId" => "%recordId%",
                                                                    "showPrimaryButton" => false,
                                                                    "titleExpression" => "repeat",
                                                                    "modelKey" => $repeatableKey,
                                                                    "modelId" => "%recordId%",
                                                                    "guidanceParams" => [
                                                                        "model" => [
                                                                            "id" => "%repeatsBy|id%",
                                                                            "name" => "%repeatsBy|not-existing%"
                                                                        ]
                                                                    ]
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ),
            ]
        );

        return $flowId;
    }

    public function generateFlow(array $data = []): string
    {
        $data += [
            'created_by' => 1,
            "type_c" => "STANDARD",
        ];

        return $this->genArrayFixture('flw_flows', $data);
    }

    public function generateFlowSteps(
        string $flowId,
        array $data = [],
        ?array $gridData = null,
        array $guidanceFields = [],
        int $order = 1
    ): string {
        if ($gridData) {
            $gridData += [
                "name" => "RR",
                "key_c" => "repeatable-embedded-guidance-key",
                "created_by" => "1"
            ];
            $gridId = $this->genArrayFixture('grid_gridtemplates', $gridData);
        } else {
            $gridId = null;
        }

        $data += [
            'created_by' => 1,
            "type_c" => "DEFAULT",
            "name" => "does not matter",
            "key_c" => "flowstep-key",
            "is_card_c" => 0,
            'grid_template_id' => $gridId,
        ];
        $flowStepId = $this->genArrayFixture('flw_flowsteps', $data);

        $this->genArrayFixture(
            'flw_flowstepslink',
            [
                "name" => "flowsteplink-name",
                "order_c" => $order,
                "created_by" => "1",
                'flow_id' => $flowId,
                'flow_step_id' => $flowStepId,
            ]
        );

        foreach ($guidanceFields as $guidanceField) {
            $this->linkGuidanceFieldToFlowStep($this->generateGuidanceField($guidanceField), $flowStepId);
        }

        return $flowStepId;
    }

    public function linkGuidanceFieldToFlowStep(string $guidanceFieldId, string $flowStepId): void
    {
        $this->linkArrayFixture('flw_guidancefields_flw_flowsteps_c', [
            "flow_field_id" => $guidanceFieldId,
            "flow_step_id" => $flowStepId,
        ]);
    }

    public function generateGuidanceField(array $data): string
    {
        return $this->genArrayFixture(
            'flw_guidancefields',
            $data + [
                'created_by' => 1,
                'date_entered' => \date(Format::DB_DATETIME_FORMAT),
            ]
        );
    }

    public function generateFilter(array $data = []): string
    {
        return $this->genArrayFixture(
            'fltrs_filters',
            $data + [
                'created_by' => 1,
            ]
        );
    }

    public function generateFilterFieldGroup(array $data = []): string
    {
        return $this->genArrayFixture(
            'fltrs_fieldsgroup',
            $data + [
                'created_by' => 1,
            ]
        );
    }

    public function generateFilterField(array $data = []): string
    {
        return $this->genArrayFixture(
            'fltrs_fields',
            $data + [
                'created_by' => 1,
            ]
        );
    }

    public function linkFilterToFieldGroup(string $filterId, string $filterFieldGroupId): void
    {
        $this->linkArrayFixture(
            'fltrs_fieldsgroup_fltrs_filters_1_c',
            [
                'filter_id' => $filterId,
                'filter_field_group_id' => $filterFieldGroupId,
            ]
        );
    }

    public function linkFilterFieldToFieldGroup(string $filterFieldId, string $filterFieldGroupId): void
    {
        $this->linkArrayFixture(
            'fltrs_fieldsgroup_fltrs_fields_1_c',
            [
                'filter_field_id' => $filterFieldId,
                'filter_field_group_id' => $filterFieldGroupId,
            ]
        );
    }

    public function generateListExternalObject(array $data = []): string
    {
        return $this->genArrayFixture('list_external_object', $data + [
                'created_by' => 1,
            ]);
    }

    public function generateAclAction(array $data = []): string
    {
        return $this->genArrayFixture(
            'acl_actions',
            $data + ['created_by' => 1]
        );
    }

    public function generateAclRole(array $data = []): string
    {
        return $this->genArrayFixture(
            'acl_roles',
            $data + ['created_by' => 1]
        );
    }

    public function linkDynamicAclRoleToAclAction(string $roleId, string $actionId): void
    {
        $this->linkArrayFixture('acl_roles_actions', [
            'acl_role_id' => $roleId,
            'acl_action_id' => $actionId,
        ]);
    }

    public function generateMenuMainMenu(array $data = []): string
    {
        return $this->genArrayFixture('menu_mainmenu', $data + [
                'created_by' => 1,
            ]);
    }

    public function linkMenuMainMenuToDashboard(string $menuMainMenuId, string $dashboardId): void
    {
        $this->linkArrayFixture('menu_mainmenu_dash_dashboard_c', [
            'menu_id' => $menuMainMenuId,
            'dashboard_id' => $dashboardId,
        ]);
    }

    public function generateListSortingOptions(array $data = []): string
    {
        return $this->genArrayFixture('list_sorting_options', $data + [
                'date_entered' => \date(Format::DB_DATETIME_FORMAT),
                'created_by' => 1,
            ]);
    }

    public function linkSortingOptionToTopBar(string $sortingOptionId, string $topBarId): void
    {
        $this->linkArrayFixture(
            'list_topbar_list_sorting_options_c',
            [
                'list_sorting_option_id' => $sortingOptionId,
                'list_top_bar_id' => $topBarId,
            ]
        );
    }

    public function generateTopAction(array $data = []): string
    {
        return $this->genArrayFixture(
            'list_top_action',
            $data + [
                'date_entered' => \date(Format::DB_DATETIME_FORMAT),
                'created_by' => 1,
            ]
        );
    }

    public function linkTopActionToTopBar(string $topActionId, string $topBarId): void
    {
        $this->linkArrayFixture(
            'list_topbar_list_top_action_c',
            [
                'list_top_bar_id' => $topBarId,
                'list_top_action_id' => $topActionId,
            ]
        );
    }

    public function linkGuidanceFieldToFieldValidator(string $guidanceFieldId, string $fieldValidatorId): void
    {
        $this->linkArrayFixture('flw_guidancefields_flw_guidancefieldvalidators_1_c', [
            'flow_field_id' => $guidanceFieldId,
            'validator_id' => $fieldValidatorId,
        ]);
    }
}
