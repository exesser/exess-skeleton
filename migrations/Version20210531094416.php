<?php

declare(strict_types=1);
// phpcs:ignoreFile

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210531094416 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_aclaction_id_del ON acl_actions');
        $this->addSql('ALTER TABLE acl_actions DROP deleted');
        $this->addSql('DROP INDEX idx_aclrole_id_del ON acl_roles');
        $this->addSql('ALTER TABLE acl_roles DROP deleted');
        $this->addSql('ALTER TABLE conditionalmessage DROP deleted');
        $this->addSql('ALTER TABLE conf_defaults DROP deleted');
        $this->addSql('ALTER TABLE dash_dashboard DROP deleted');
        $this->addSql('ALTER TABLE dash_dashboardmenu DROP deleted');
        $this->addSql('ALTER TABLE dash_menuactions DROP deleted');
        $this->addSql('ALTER TABLE dash_dashboardmenuactiongroup DROP deleted');
        $this->addSql('ALTER TABLE dash_dashboardproperties DROP deleted');
        $this->addSql('ALTER TABLE list_external_object DROP deleted');
        $this->addSql('ALTER TABLE list_external_object_linkfields DROP deleted');
        $this->addSql('ALTER TABLE fltrs_filters DROP deleted');
        $this->addSql('ALTER TABLE fltrs_fields DROP deleted');
        $this->addSql('ALTER TABLE fltrs_fieldsgroup DROP deleted');
        $this->addSql('ALTER TABLE find_search DROP deleted');
        $this->addSql('ALTER TABLE flw_flows DROP deleted');
        $this->addSql('ALTER TABLE flw_actions DROP deleted');
        $this->addSql('ALTER TABLE flw_guidancefields DROP deleted');
        $this->addSql('ALTER TABLE flw_flowsteps DROP deleted');
        $this->addSql('ALTER TABLE flw_flowstepslink DROP deleted');
        $this->addSql('ALTER TABLE flw_flowstepproperties DROP deleted');
        $this->addSql('ALTER TABLE grid_panels DROP deleted');
        $this->addSql('ALTER TABLE grid_gridtemplates DROP deleted');
        $this->addSql('ALTER TABLE list_cell DROP deleted');
        $this->addSql('ALTER TABLE list_cells DROP deleted');
        $this->addSql('ALTER TABLE list_dynamic_list DROP deleted');
        $this->addSql('ALTER TABLE list_row_action DROP deleted');
        $this->addSql('ALTER TABLE list_row_bar DROP deleted');
        $this->addSql('ALTER TABLE list_sorting_options DROP deleted');
        $this->addSql('ALTER TABLE list_top_action DROP deleted');
        $this->addSql('ALTER TABLE list_topbar DROP deleted');
        $this->addSql('ALTER TABLE menu_mainmenu DROP deleted');
        $this->addSql('ALTER TABLE securitygroups DROP deleted');
        $this->addSql('ALTER TABLE securitygroups_api DROP deleted');
        $this->addSql('DROP INDEX securitygroups_users_idxd ON securitygroups_users');
        $this->addSql('ALTER TABLE securitygroups_users DROP deleted');
        $this->addSql('CREATE INDEX securitygroups_users_idxd ON securitygroups_users (user_id, securitygroup_id)');
        $this->addSql('ALTER TABLE fe_selectwithsearch DROP deleted');
        $this->addSql('ALTER TABLE trans_translation DROP deleted');
        $this->addSql('ALTER TABLE users DROP deleted');
        $this->addSql('ALTER TABLE user_guidance_recovery DROP deleted');
        $this->addSql('ALTER TABLE user_login DROP deleted');
        $this->addSql('ALTER TABLE flw_guidancefieldvalidators DROP deleted');
        $this->addSql('ALTER TABLE acl_actions_aud DROP deleted');
        $this->addSql('ALTER TABLE acl_roles_aud DROP deleted');
        $this->addSql('ALTER TABLE conditionalmessage_aud DROP deleted');
        $this->addSql('ALTER TABLE conf_defaults_aud DROP deleted');
        $this->addSql('ALTER TABLE dash_dashboard_aud DROP deleted');
        $this->addSql('ALTER TABLE dash_dashboardmenu_aud DROP deleted');
        $this->addSql('ALTER TABLE dash_menuactions_aud DROP deleted');
        $this->addSql('ALTER TABLE dash_dashboardmenuactiongroup_aud DROP deleted');
        $this->addSql('ALTER TABLE dash_dashboardproperties_aud DROP deleted');
        $this->addSql('ALTER TABLE list_external_object_aud DROP deleted');
        $this->addSql('ALTER TABLE list_external_object_linkfields_aud DROP deleted');
        $this->addSql('ALTER TABLE fltrs_filters_aud DROP deleted');
        $this->addSql('ALTER TABLE fltrs_fields_aud DROP deleted');
        $this->addSql('ALTER TABLE fltrs_fieldsgroup_aud DROP deleted');
        $this->addSql('ALTER TABLE find_search_aud DROP deleted');
        $this->addSql('ALTER TABLE flw_flows_aud DROP deleted');
        $this->addSql('ALTER TABLE flw_actions_aud DROP deleted');
        $this->addSql('ALTER TABLE flw_guidancefields_aud DROP deleted');
        $this->addSql('ALTER TABLE flw_flowsteps_aud DROP deleted');
        $this->addSql('ALTER TABLE flw_flowstepslink_aud DROP deleted');
        $this->addSql('ALTER TABLE flw_flowstepproperties_aud DROP deleted');
        $this->addSql('ALTER TABLE grid_panels_aud DROP deleted');
        $this->addSql('ALTER TABLE grid_gridtemplates_aud DROP deleted');
        $this->addSql('ALTER TABLE list_cell_aud DROP deleted');
        $this->addSql('ALTER TABLE list_cells_aud DROP deleted');
        $this->addSql('ALTER TABLE list_dynamic_list_aud DROP deleted');
        $this->addSql('ALTER TABLE list_row_action_aud DROP deleted');
        $this->addSql('ALTER TABLE list_row_bar_aud DROP deleted');
        $this->addSql('ALTER TABLE list_sorting_options_aud DROP deleted');
        $this->addSql('ALTER TABLE list_top_action_aud DROP deleted');
        $this->addSql('ALTER TABLE list_topbar_aud DROP deleted');
        $this->addSql('ALTER TABLE menu_mainmenu_aud DROP deleted');
        $this->addSql('ALTER TABLE securitygroups_aud DROP deleted');
        $this->addSql('ALTER TABLE securitygroups_api_aud DROP deleted');
        $this->addSql('ALTER TABLE securitygroups_users_aud DROP deleted');
        $this->addSql('ALTER TABLE fe_selectwithsearch_aud DROP deleted');
        $this->addSql('ALTER TABLE trans_translation_aud DROP deleted');
        $this->addSql('ALTER TABLE users_aud DROP deleted');
        $this->addSql('ALTER TABLE user_guidance_recovery_aud DROP deleted');
        $this->addSql('ALTER TABLE user_login_aud DROP deleted');
        $this->addSql('ALTER TABLE flw_guidancefieldvalidators_aud DROP deleted');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE acl_actions ADD deleted TINYINT(1) NOT NULL');
        $this->addSql('CREATE INDEX idx_aclaction_id_del ON acl_actions (id, deleted)');
        $this->addSql('ALTER TABLE acl_actions_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE acl_roles ADD deleted TINYINT(1) NOT NULL');
        $this->addSql('CREATE INDEX idx_aclrole_id_del ON acl_roles (id, deleted)');
        $this->addSql('ALTER TABLE acl_roles_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE conditionalmessage ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE conditionalmessage_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE conf_defaults ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE conf_defaults_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE dash_dashboard ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE dash_dashboard_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE dash_dashboardmenu ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE dash_dashboardmenu_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE dash_dashboardmenuactiongroup ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE dash_dashboardmenuactiongroup_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE dash_dashboardproperties ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE dash_dashboardproperties_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE dash_menuactions ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE dash_menuactions_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE fe_selectwithsearch ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE fe_selectwithsearch_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE find_search ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE find_search_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE fltrs_fields ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE fltrs_fields_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE fltrs_fieldsgroup ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE fltrs_fieldsgroup_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE fltrs_filters ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE fltrs_filters_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE flw_actions ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE flw_actions_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE flw_flows ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE flw_flows_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE flw_flowstepproperties ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE flw_flowstepproperties_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE flw_flowsteps ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE flw_flowsteps_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE flw_flowstepslink ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE flw_flowstepslink_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE flw_guidancefields ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE flw_guidancefields_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE flw_guidancefieldvalidators ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE flw_guidancefieldvalidators_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE grid_gridtemplates ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE grid_gridtemplates_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE grid_panels ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE grid_panels_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE list_cell ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE list_cell_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE list_cells ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE list_cells_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE list_dynamic_list ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE list_dynamic_list_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE list_external_object ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE list_external_object_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE list_external_object_linkfields ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE list_external_object_linkfields_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE list_row_action ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE list_row_action_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE list_row_bar ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE list_row_bar_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE list_sorting_options ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE list_sorting_options_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE list_top_action ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE list_top_action_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE list_topbar ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE list_topbar_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE menu_mainmenu ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE menu_mainmenu_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE securitygroups ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE securitygroups_api ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE securitygroups_api_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE securitygroups_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('DROP INDEX securitygroups_users_idxd ON securitygroups_users');
        $this->addSql('ALTER TABLE securitygroups_users ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('CREATE INDEX securitygroups_users_idxd ON securitygroups_users (user_id, deleted, securitygroup_id)');
        $this->addSql('ALTER TABLE securitygroups_users_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE trans_translation ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE trans_translation_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE user_guidance_recovery ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE user_guidance_recovery_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE user_login ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE user_login_aud ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD deleted TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE users_aud ADD deleted TINYINT(1) DEFAULT NULL');
    }
}
