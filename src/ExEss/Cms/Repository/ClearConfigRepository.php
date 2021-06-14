<?php declare(strict_types=1);

namespace ExEss\Cms\Repository;

use ExEss\Cms\Entity\FlowAction;

/**
 * Auto-wired
 */
class ClearConfigRepository
{
    public const FAT_ENTITIES_MAIN_RECORDS = [
        \FLW_Flows::class,
        \LIST_dynamic_list::class,
        \DASH_Dashboard::class,
    ];

    private \PDO $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function findAllOrphanConfigIds(): array
    {
        $orphanRecords = [];
        foreach ($this->getUsedRecords() as $table => $ids) {
            $orphanRecords[$table] = $this->getOrphanRecordsForTable($table, $ids);
        }

        return $orphanRecords;
    }

    public function findAllFatEntityConfigIds(string $fatEntity, string $fatEntityId): array
    {
        $flowId = null;
        $listId = null;
        $dashId = null;

        switch ($fatEntity) {
            case \FLW_Flows::class:
                $flowId = $fatEntityId;
                $records = $this->getUsedFlowRecords($flowId);
                break;
            case \LIST_dynamic_list::class:
                $listId = $fatEntityId;
                $records = $this->getUsedListRecords($listId);
                break;
            case \DASH_Dashboard::class:
                $dashId = $fatEntityId;
                $records = $this->getUsedDashRecords($dashId);
                break;
            default:
                throw new \InvalidArgumentException(\sprintf(
                    'Invalid values specified for FatEntity, allowed values: %s',
                    \implode(', ', self::FAT_ENTITIES_MAIN_RECORDS)
                ));
        }

        $usedRecords = $this->mergeMultipleRecords(
            $this->getUsedFlowRecords(null, $flowId),
            $this->getUsedListRecords(null, $listId),
            $this->getUsedDashRecords(null, $dashId),
            $this->getUsedMiscRecords()
        );

        $safeToRemoveConfig = [];
        $usedForOthersConfig = [];

        foreach ($records as $table => $ids) {
            $safeToRemoveConfig[$table] = \array_diff($ids, $usedRecords[$table]);
            $usedForOthersConfig[$table] = \array_diff($ids, $safeToRemoveConfig[$table]);
        }

        return [$safeToRemoveConfig, $usedForOthersConfig];
    }

    public function findRelationTables(string $table): array
    {
        $query = \sprintf(
            "SELECT
                IF(r.rhs_table = '%1\$s', r.join_key_rhs, r.join_key_lhs) AS rel_field,
                r.join_table AS rel_table
            FROM relationships AS r
            WHERE r.join_table IS NOT NULL
                AND (
                    (r.rhs_table = '%1\$s' AND r.rhs_key = 'id')
                    OR (r.lhs_table = '%1\$s' AND r.lhs_key = 'id')
                )
            ",
            $table
        );

        return $this->db->query($query)->fetchAll();
    }

    public function deleteRecords(string $table, array $ids): int
    {
        $query = \sprintf(
            "DELETE FROM %s WHERE id IN ('%s')",
            $table,
            \implode("', '", $ids)
        );

        return $this->db->query($query)->rowCount();
    }

    public function deleteRelationRecords(
        string $table,
        string $relTable,
        string $relField
    ): int {
        $query = \sprintf(
            "DELETE FROM %s WHERE %s NOT IN (SELECT id FROM %s)",
            $relTable,
            $relField,
            $table
        );

        return $this->db->query($query)->rowCount();
    }

    public function setDatabase(string $database): void
    {
        $this->db->exec("USE $database");
    }

    private function getTablesWithIds(string $query): array
    {
        $rows = $this->db->query($query)->fetchAll(\PDO::FETCH_ASSOC);

        $tables = [];
        foreach ($rows as $row) {
            foreach ($row as $table => $id) {
                $table = \explode("::", $table)[0];
                if ($id) {
                    $tables[$table][$id] = $id;
                }
            }
        }

        return $tables;
    }

    private function getOrphanRecordsForTable(string $table, array $usedIds): array
    {
        $query = \sprintf(
            "SELECT id, name FROM %s WHERE id NOT IN ('%s') ORDER BY name DESC",
            $table,
            \implode("', '", $usedIds)
        );
        $rows = $this->db->query($query)->fetchAll();

        $records = [];
        foreach ($rows as $row) {
            $records[$row['id']] = $row['name'];
        }

        return $records;
    }

    private function getUsedFlowRecords(?string $forFlowId = null, ?string $ignoreFlowId = null): array
    {
        $query = \sprintf(
            "SELECT
                f.id AS 'flw_flows',
                fsl.id AS 'flw_flowstepslink',
                fs.id AS 'flw_flowsteps',
                gf.id AS 'flw_guidancefields',
                gfv.id AS 'flw_guidancefieldvalidators::1',
                gfv2.id AS 'flw_guidancefieldvalidators::2',
                gfv3.id AS 'flw_guidancefieldvalidators::3',
                fa.id AS 'flw_actions',
                f.action_id AS 'flw_actions::1',
                fsp.id AS 'flw_flowstepproperties',
                t.id AS 'grid_gridtemplates'
            FROM flw_flows AS f
            INNER JOIN flw_flowstepslink AS fsl ON
                fsl.flow_id = f.id
            INNER JOIN flw_flowsteps AS fs ON
                fs.id = fsl.flow_step_id
            LEFT JOIN flw_guidancefields_flw_flowsteps_c AS gf_fs ON
                gf_fs.flow_step_id = fs.id
            LEFT JOIN flw_guidancefields gf ON
                gf.id = gf_fs.flow_field_id
            LEFT JOIN flw_guidancefields_flw_guidancefieldvalidators_1_c AS gf_gfv ON
                gf_gfv.flow_field_id = gf.id
            LEFT JOIN flw_guidancefieldvalidators AS gfv on
                gfv.id = gf_gfv.validator_id
            LEFT JOIN flw_guidancefieldsvalidators_conditions gfv_gfv2 ON
                gfv_gfv2.parent_id = gfv.id
            LEFT JOIN flw_guidancefieldvalidators AS gfv2 on
                gfv2.id = gfv_gfv2.child_id
            LEFT JOIN flw_guidancefieldsvalidators_conditions gfv_gfv3 ON
                gfv_gfv3.parent_id = gfv2.id
            LEFT JOIN flw_guidancefieldvalidators AS gfv3 on
                gfv3.id = gfv_gfv3.child_id
            LEFT JOIN flw_actions AS fa ON
                fa.id = gf.flow_action_id   
            LEFT JOIN flw_flowsteps_flw_flowstepproperties_1_c AS fs_fsp ON
                fs_fsp.flow_step_id = fs.id
            LEFT JOIN properties AS fsp ON
                fsp.id = fs_fsp.property_id
            LEFT JOIN grid_gridtemplates AS t ON
                t.id = fs.grid_template_id
            WHERE 1 = 1 %s %s",
            !empty($forFlowId) ? "AND f.id = '$forFlowId'" : "",
            !empty($ignoreFlowId) ? "AND f.id <> '$ignoreFlowId'" : ""
        );

        return $this->getTablesWithIds($query);
    }

    private function getUsedListRecords(?string $forListId = null, ?string $ignoreListId = null): array
    {
        $query = \sprintf(
            "SELECT
                   l.id as 'list_dynamic_list',
                   lcs.id as 'list_cells',
                   lc.id as 'list_cell',
                   leo.id as 'list_external_object',
                   leolf.id AS 'list_external_object_linkfields',
                   ltb.id AS 'list_topbar',
                   lso.id AS 'list_sorting_options',
                   lta.id AS 'list_top_action',
                   lrb.id AS 'list_row_bar',
                   lra.id AS 'list_row_action',
                   lta.flw_actions_id_c AS 'flw_actions::1',
                   fa.id AS 'flw_actions::2'
            FROM list_dynamic_list as l
            INNER JOIN list_cells AS lcs ON
                l.id = lcs.list_id
            INNER JOIN list_cell AS lc ON
                lc.id = lcs.cell_id
            LEFT JOIN list_external_object AS leo ON
                leo.id = l.external_object_id
            LEFT JOIN list_external_object_linkfields AS leolf ON
                leolf.external_object_id = leo.id
            LEFT JOIN list_topbar as ltb ON
                ltb.id = l.top_bar_id
            LEFT JOIN list_topbar_list_sorting_options_c AS ltb_lso ON
                ltb_lso.list_top_bar_id = ltb.id
            LEFT JOIN list_sorting_options AS lso ON
                lso.id = ltb_lso.list_sorting_option_id
            LEFT JOIN list_topbar_list_top_action_c AS ltb_lta ON
                ltb_lta.list_top_bar_id = ltb.id
            LEFT JOIN list_top_action AS lta ON
                lta.id = ltb_lta.list_top_action_id
            LEFT JOIN list_row_bar AS lrb ON
                lrb.id = lcs.row_bar_id
            LEFT JOIN list_row_action AS lra ON
                lra.row_bar_id = lrb.id
            LEFT JOIN flw_actions as fa ON
                fa.id = lra.flow_action_id
           WHERE 1 = 1 %s %s",
            !empty($forListId) ? " AND l.id = '$forListId'" : "",
            !empty($ignoreListId) ? " AND l.id <> '$ignoreListId'" : ""
        );

        return $this->getTablesWithIds($query);
    }

    private function getUsedDashRecords(?string $forDashId = null, ?string $ignoreDashId = null): array
    {
        $query = \sprintf(
            "SELECT
                d.id as dash_dashboard,
                dp.id as dash_dashboardproperties,
                t.id AS grid_gridtemplates
            FROM dash_dashboard AS d
            LEFT JOIN dash_dashboard_dash_dashboardproperties_c AS d_dp ON
                d_dp.dashboard_id = d.id
            LEFT JOIN properties AS dp ON
                dp.id = d_dp.property_id
            LEFT JOIN grid_gridtemplates AS t ON
                t.id = d.grid_gridtemplates_id_c
            AND 1 = 1 %s %s 
            ",
            !empty($forDashId) ? " AND d.id = '$forDashId'" : "",
            !empty($ignoreDashId) ? " AND d.id <> '$ignoreDashId'" : ""
        );

        $usedRecords = $this->getTablesWithIds($query);

        $menuQuery = \sprintf(
            "SELECT
                dm.id as dash_dashboardmenu,
                dma.id as dash_menuactions,
                dmag1.id as dash_dashboardmenuactiongroup,
                dma1.id as 'dash_menuactions::1',
                dmag2.id as 'dash_dashboardmenuactiongroup::2',
                dma.flw_actions_id_c AS 'flw_actions::0',
                dma1.flw_actions_id_c AS 'flw_actions::1',
                dma2.flw_actions_id_c AS 'flw_actions::2',
                dma2.id as 'dash_menuactions::2'
            FROM dash_dashboardmenu AS dm
            LEFT JOIN dash_dashboardmenu_dash_menuactions_1_c AS dm_dma ON
                dm_dma.dashboard_menu_id = dm.id
            LEFT JOIN dash_menuactions AS dma ON
                dma.id = dm_dma.dashboard_menu_action_id
            LEFT JOIN dash_dashboardmenu_dash_dashboardmenuactiongroup_1_c AS dm_dmag1 ON
                dm_dmag1.dashboard_menu_id = dm.id
            LEFT JOIN dash_dashboardmenuactiongroup AS dmag1 ON
                dmag1.id = dm_dmag1.dashboard_menu_action_group_id
            LEFT JOIN dash_dashboardmenuactiongroup_dash_menuactions AS dmag1_dma1 ON
                dmag1_dma1.dashboard_menu_action_group_id = dmag1.id
            LEFT JOIN dash_menuactions AS dma1 ON
                dma1.id = dmag1_dma1.dashboard_menu_action_id
            LEFT JOIN dash_menuactiongroup_x_dash_menuactiongroup AS dmag1_dmag2 ON
                dmag1_dmag2.parent_id = dmag1.id
            LEFT JOIN dash_dashboardmenuactiongroup AS dmag2 ON
                dmag2.id = dmag1_dmag2.child_id
            LEFT JOIN dash_dashboardmenuactiongroup_dash_menuactions AS dmag2_dma2 ON
                dmag2_dma2.dashboard_menu_action_group_id = dmag2.id
            LEFT JOIN dash_menuactions AS dma2 ON
                dma2.id = dmag2_dma2.dashboard_menu_action_id
            WHERE dm.id in (
                    SELECT
                        dm_d.dashboard_menu_id
                    FROM dash_dashboard AS d
                    WHERE 1 = 1 %s %s 
            )",
            !empty($forDashId) ? " AND d.id = '$forDashId'" : "",
            !empty($ignoreDashId) ? " AND d.id <> '$ignoreDashId'" : ""
        );

        return $this->mergeRecords($usedRecords, $this->getTablesWithIds($menuQuery));
    }

    private function getUsedMiscRecords(): array
    {
        $validatorsQuery = "SELECT
                gfv.id as 'flw_guidancefieldvalidators', 
                gfv2.id as 'flw_guidancefieldvalidators::2'
            FROM conditionalmessage as cm
            LEFT JOIN conditional_message_validators AS cm_gfv ON
                cm_gfv.conditional_message_id = cm.id
            LEFT JOIN flw_guidancefieldvalidators AS gfv ON
                gfv.id = cm_gfv.validator_id
            LEFT JOIN flw_guidancefieldsvalidators_conditions gfv_gfv2 ON
                gfv_gfv2.parent_id = gfv.id
            LEFT JOIN flw_guidancefieldvalidators AS gfv2 on
                gfv2.id = gfv_gfv2.child_id";

        $usedRecords = $this->getTablesWithIds($validatorsQuery);

        $validatorsPanelQuery = "SELECT
                DISTINCT gfv.id as 'flw_guidancefieldvalidators'
            FROM grid_panels as gp
            LEFT JOIN grid_panels_flw_guidancefieldvalidators_1_c AS gp_gfv ON
                gp_gfv.grid_panel_id = gp.id
            LEFT JOIN flw_guidancefieldvalidators AS gfv ON
                gfv.id = gp_gfv.validator_id";

        $usedRecords = $this->mergeRecords($usedRecords, $this->getTablesWithIds($validatorsPanelQuery));

        $gridUsedOnListExtraActionQuery = "SELECT
                id as grid_gridtemplates
            FROM grid_gridtemplates 
            WHERE 
                key_c in (
                    SELECT json_extract(c.params_c, '$.grid') 
                    FROM list_cell as c 
                    WHERE 
                        c.params_c like '%grid%' 
                        AND JSON_CONTAINS_PATH(c.params_c, 'one', '$.grid')
                )
        ";
        $usedRecords = $this->mergeRecords($usedRecords, $this->getTablesWithIds($gridUsedOnListExtraActionQuery));

        $row = $this->db->query("
            SELECT
                group_concat(JSON_EXTRACT(json_fields_c,'$**.action.id'))
            FROM grid_gridtemplates
            WHERE json_fields_c like '%\"action\"%'
        ")->fetchColumn();
        $usedActionsOnGrid = \array_filter(\explode(" ", \str_replace(["[", "]", '"', ','], " ", $row)));

        $flowActionQuery = \sprintf(
            "SELECT
                fa.id AS 'flw_actions'
            FROM flw_actions as fa
            WHERE (
                    fa.guid IN ('%s')
                    OR fa.guid in (
                        SELECT
                            DISTINCT IFNULL(
                                json_extract(json, '$.arguments.confirmCommandKey'),
                                json_extract(json, '$.arguments.params.confirmCommandKey')
                            )
                        FROM flw_actions 
                        WHERE (
                            JSON_CONTAINS_PATH(json, 'one', '$.arguments.confirmCommandKey')
                            OR JSON_CONTAINS_PATH(json, 'one', '$.arguments.params.confirmCommandKey')
                            )
                    )
                    OR fa.guid IN (
                        SELECT 
                            cm.action_c 
                        FROM conditionalmessage as cm 
                        WHERE cm.action_c is not null
                    )
                )",
            \implode("', '", \array_merge(FlowAction::USED_ACTIONS, $usedActionsOnGrid))
        );

        return $this->mergeRecords($usedRecords, $this->getTablesWithIds($flowActionQuery));
    }

    private function getUsedRecords(): array
    {
        return $this->mergeMultipleRecords(
            $this->getUsedFlowRecords(),
            $this->getUsedListRecords(),
            $this->getUsedDashRecords(),
            $this->getUsedMiscRecords()
        );
    }

    private function mergeMultipleRecords(array ...$args): array
    {
        $allRecords = [];

        foreach ($args as $records) {
            $allRecords = $this->mergeRecords($allRecords, $records);
        }

        return  $allRecords;
    }

    private function mergeRecords(array $records1, array $records2): array
    {
        $records = $records1 + $records2;

        foreach ($records as $table => $ids) {
            if (isset($records1[$table], $records2[$table])) {
                $records[$table] = \array_merge($records1[$table], $records2[$table]);
            }
        }

        return $records;
    }
}
