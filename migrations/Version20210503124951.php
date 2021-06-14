<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210503124951 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    // phpcs:disable
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE acl_actions (id CHAR(36) NOT NULL, date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, created_by CHAR(36) NOT NULL, name VARCHAR(150) DEFAULT NULL, deleted TINYINT(1) NOT NULL, category VARCHAR(100) DEFAULT NULL, aclaccess INT DEFAULT NULL, INDEX idx_aclaction_id_del (id, deleted), INDEX idx_category_name (category, name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acl_roles (id CHAR(36) NOT NULL, date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, created_by CHAR(36) NOT NULL, name VARCHAR(150) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) NOT NULL, code VARCHAR(255) NOT NULL, INDEX idx_aclrole_id_del (id, deleted), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acl_roles_users (acl_role_id CHAR(36) NOT NULL, user_id CHAR(36) NOT NULL, INDEX IDX_500BDB06BD33296F (acl_role_id), INDEX IDX_500BDB06A76ED395 (user_id), PRIMARY KEY(acl_role_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acl_roles_actions (acl_role_id CHAR(36) NOT NULL, acl_action_id CHAR(36) NOT NULL, INDEX IDX_6B13067FBD33296F (acl_role_id), INDEX IDX_6B13067FEA37C600 (acl_action_id), PRIMARY KEY(acl_role_id, acl_action_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE conditionalmessage (id CHAR(36) NOT NULL, assigned_user_id CHAR(36) DEFAULT NULL, created_by CHAR(36) NOT NULL, modified_user_id CHAR(36) DEFAULT NULL, domain ENUM(\'sidebar\') DEFAULT \'sidebar\' COMMENT \'(DC2Type:enum_message_domain)\', action_c VARCHAR(255) DEFAULT NULL, icon_c VARCHAR(255) DEFAULT NULL, record_type VARCHAR(255) DEFAULT NULL, record_id VARCHAR(255) DEFAULT NULL, priority_c INT DEFAULT 1, description_params TEXT DEFAULT NULL, date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX fk_users_id_94dc668b (assigned_user_id), INDEX fk_users_id_bca73b85 (modified_user_id), INDEX fk_users_id_b8a72bb4 (created_by), INDEX key_domain (domain), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE conditional_message_validators (conditional_message_id CHAR(36) NOT NULL, validator_id CHAR(36) NOT NULL, INDEX IDX_74E7F6425091F067 (conditional_message_id), INDEX IDX_74E7F642B0644AEC (validator_id), PRIMARY KEY(conditional_message_id, validator_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE conf_defaults (id CHAR(36) NOT NULL, created_by CHAR(36) NOT NULL, modified_user_id CHAR(36) DEFAULT NULL, systemid VARCHAR(255) DEFAULT NULL, parameter VARCHAR(255) DEFAULT NULL, value LONGTEXT DEFAULT NULL, date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX fk_users_id_35ba22c2 (modified_user_id), INDEX fk_users_id_00d36994 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dash_dashboard (id CHAR(36) NOT NULL, grid_gridtemplates_id_c CHAR(36) DEFAULT NULL, dashboard_menu_id CHAR(36) DEFAULT NULL, search_id CHAR(36) DEFAULT NULL, filter_id CHAR(36) DEFAULT NULL, created_by CHAR(36) NOT NULL, modified_user_id CHAR(36) DEFAULT NULL, type_c ENUM(\'DEFAULT\', \'EXTERNAL\') DEFAULT \'DEFAULT\' COMMENT \'(DC2Type:enum_dashboard_type)\', key_c VARCHAR(255) DEFAULT NULL, menu_sort_c VARCHAR(255) DEFAULT NULL, main_record_type_c VARCHAR(255) DEFAULT NULL, filters_listkey VARCHAR(255) DEFAULT NULL, date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_DCEE68C47D333B3F (dashboard_menu_id), INDEX IDX_DCEE68C4650760A9 (search_id), INDEX IDX_DCEE68C4D395B25E (filter_id), INDEX fk_grid_gridtemplates_id_61577793 (grid_gridtemplates_id_c), INDEX fk_users_id_aa7968df (created_by), INDEX fk_users_id_2ebb459b (modified_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dash_dashboard_dash_dashboardproperties_c (dashboard_id CHAR(36) NOT NULL, dashboard_property_id CHAR(36) NOT NULL, INDEX IDX_4B916157B9D04D2B (dashboard_id), INDEX IDX_4B916157EB330A3 (dashboard_property_id), PRIMARY KEY(dashboard_id, dashboard_property_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dash_dashboardmenu (id CHAR(36) NOT NULL, created_by CHAR(36) NOT NULL, modified_user_id CHAR(36) DEFAULT NULL, date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX fk_users_id_817d915c (created_by), INDEX fk_users_id_85910a54 (modified_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dash_dashboardmenu_dash_dashboardmenuactiongroup_1_c (dashboard_menu_id CHAR(36) NOT NULL, dashboard_menu_action_group_id CHAR(36) NOT NULL, INDEX IDX_52F8E72E7D333B3F (dashboard_menu_id), INDEX IDX_52F8E72EA79437DF (dashboard_menu_action_group_id), PRIMARY KEY(dashboard_menu_id, dashboard_menu_action_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dash_dashboardmenu_dash_menuactions_1_c (dashboard_menu_id CHAR(36) NOT NULL, dashboard_menu_action_id CHAR(36) NOT NULL, INDEX IDX_622939B47D333B3F (dashboard_menu_id), INDEX IDX_622939B4D743A99 (dashboard_menu_action_id), PRIMARY KEY(dashboard_menu_id, dashboard_menu_action_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dash_menuactions (id CHAR(36) NOT NULL, flw_actions_id_c CHAR(36) DEFAULT NULL, created_by CHAR(36) NOT NULL, modified_user_id CHAR(36) DEFAULT NULL, label_c VARCHAR(100) DEFAULT NULL, sort_order INT DEFAULT NULL, icon_c VARCHAR(100) DEFAULT NULL, params_c TEXT DEFAULT NULL, conditionsenabled_c TEXT DEFAULT NULL, conditions_hide_c TEXT DEFAULT NULL, date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX fk_flw_actions_id_29cdc032 (flw_actions_id_c), INDEX fk_users_id_44ee9b96 (created_by), INDEX fk_users_id_b842ba88 (modified_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dash_dashboardmenuactiongroup (id CHAR(36) NOT NULL, created_by CHAR(36) NOT NULL, modified_user_id CHAR(36) DEFAULT NULL, label_c VARCHAR(100) DEFAULT NULL, sort_order INT DEFAULT NULL, icon VARCHAR(255) DEFAULT NULL, class VARCHAR(255) DEFAULT NULL, conditions_hide_c TEXT DEFAULT NULL, date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX fk_users_id_40175142 (created_by), INDEX fk_users_id_16e1cd88 (modified_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dash_menuactiongroup_x_dash_menuactiongroup (parent_id CHAR(36) NOT NULL, child_id CHAR(36) NOT NULL, INDEX IDX_2B794B89727ACA70 (parent_id), INDEX IDX_2B794B89DD62C21B (child_id), PRIMARY KEY(parent_id, child_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dash_dashboardmenuactiongroup_dash_menuactions (dashboard_menu_action_group_id CHAR(36) NOT NULL, dashboard_menu_action_id CHAR(36) NOT NULL, INDEX IDX_54BE8525A79437DF (dashboard_menu_action_group_id), INDEX IDX_54BE8525D743A99 (dashboard_menu_action_id), PRIMARY KEY(dashboard_menu_action_group_id, dashboard_menu_action_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dash_dashboardproperties (id CHAR(36) NOT NULL, created_by CHAR(36) NOT NULL, modified_user_id CHAR(36) DEFAULT NULL, value_c VARCHAR(255) DEFAULT NULL, date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX fk_users_id_1f405dcb (modified_user_id), INDEX fk_users_id_2aa901d0 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE list_external_object (id CHAR(36) NOT NULL, created_by CHAR(36) NOT NULL, modified_user_id CHAR(36) DEFAULT NULL, class_handler VARCHAR(255) DEFAULT NULL, module_name_c VARCHAR(255) DEFAULT NULL, list_only TINYINT(1) DEFAULT \'0\' NOT NULL, date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX fk_users_id_b65dbf45 (modified_user_id), INDEX fk_users_id_911f78c5 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE list_external_object_linkfields (id CHAR(36) NOT NULL, external_object_id CHAR(36) DEFAULT NULL, created_by CHAR(36) NOT NULL, modified_user_id CHAR(36) DEFAULT NULL, suite_bean_name VARCHAR(255) DEFAULT NULL, suite_bean_field VARCHAR(255) DEFAULT NULL, date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_65642284776F93F (external_object_id), INDEX fk_users_id_c8fd1c5c (modified_user_id), INDEX fk_users_id_1356a3c9 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fltrs_filters (id CHAR(36) NOT NULL, created_by CHAR(36) NOT NULL, modified_user_id CHAR(36) DEFAULT NULL, default_filters_json_c TEXT DEFAULT NULL, filterskey_c VARCHAR(64) DEFAULT NULL, date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX fk_users_id_0a623723 (modified_user_id), INDEX fk_users_id_6c9c0cd2 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fltrs_fields (id CHAR(36) NOT NULL, created_by CHAR(36) NOT NULL, modified_user_id CHAR(36) DEFAULT NULL, label_c VARCHAR(255) DEFAULT NULL, operator VARCHAR(6) DEFAULT \'=\', field_key_c VARCHAR(255) DEFAULT NULL, field_sql_c TEXT DEFAULT NULL, field_options_c TEXT DEFAULT NULL, field_options_enum_key_c VARCHAR(255) DEFAULT NULL, sort_c VARCHAR(4) DEFAULT \'10\', type_c ENUM(\'bool\', \'checkboxGroup\', \'date\', \'datetime\', \'enum\', \'radioGroup\', \'selectWithSearch\', \'toggleGroup\', \'varchar\') DEFAULT NULL COMMENT \'(DC2Type:enum_filter_field_type)\', date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX fk_users_id_d334c340 (modified_user_id), INDEX fk_users_id_82b17b3c (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fltrs_fieldsgroup (id CHAR(36) NOT NULL, created_by CHAR(36) NOT NULL, modified_user_id CHAR(36) DEFAULT NULL, sort_c VARCHAR(5) DEFAULT \'10\', date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX fk_users_id_05b3773d (modified_user_id), INDEX fk_users_id_11761dc2 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fltrs_fieldsgroup_fltrs_fields_1_c (filter_field_group_id CHAR(36) NOT NULL, filter_field_id CHAR(36) NOT NULL, INDEX IDX_4A6AE485B6665182 (filter_field_group_id), INDEX IDX_4A6AE48579580210 (filter_field_id), PRIMARY KEY(filter_field_group_id, filter_field_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fltrs_fieldsgroup_fltrs_filters_1_c (filter_field_group_id CHAR(36) NOT NULL, filter_id CHAR(36) NOT NULL, INDEX IDX_1B855B10B6665182 (filter_field_group_id), INDEX IDX_1B855B10D395B25E (filter_id), PRIMARY KEY(filter_field_group_id, filter_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE find_search (id CHAR(36) NOT NULL, created_by CHAR(36) NOT NULL, modified_user_id CHAR(36) DEFAULT NULL, link_to ENUM(\'dashboard\', \'focus_mode\', \'guidance_mode\') DEFAULT NULL COMMENT \'(DC2Type:enum_link_to)\', params TEXT DEFAULT NULL, date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX fk_users_id_739bb32e (created_by), INDEX fk_users_id_12ec510a (modified_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE flw_flows (id CHAR(36) NOT NULL, action_id CHAR(36) DEFAULT NULL, created_by CHAR(36) NOT NULL, modified_user_id CHAR(36) DEFAULT NULL, key_c VARCHAR(255) DEFAULT NULL, type_c ENUM(\'DEFAULT\', \'Dashvoard\', \'FORCECREATE\', \'STANDARD\') DEFAULT \'STANDARD\' COMMENT \'(DC2Type:enum_flow_type)\', base_object_c VARCHAR(150) DEFAULT NULL, loading_message_c VARCHAR(150) DEFAULT NULL, error_message TEXT DEFAULT NULL, external TINYINT(1) DEFAULT \'0\', label_c VARCHAR(255) DEFAULT \'1\', use_api_label_c TINYINT(1) NOT NULL, is_config TINYINT(1) NOT NULL, date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX fk_users_id_a08dfff7 (modified_user_id), INDEX fk_users_id_53b16522 (created_by), INDEX idx_key_c (key_c), INDEX fk_flw_actions_id_a235cd23 (action_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE flw_actions (id CHAR(36) NOT NULL, created_by CHAR(36) NOT NULL, modified_user_id CHAR(36) DEFAULT NULL, guid VARCHAR(255) DEFAULT NULL, json TEXT DEFAULT NULL, date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX fk_users_id_cc8489c9 (created_by), INDEX fk_users_id_244b2f9c (modified_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE flw_guidancefields (id CHAR(36) NOT NULL, flow_action_id CHAR(36) DEFAULT NULL, created_by CHAR(36) NOT NULL, modified_user_id CHAR(36) DEFAULT NULL, field_id TEXT DEFAULT NULL, field_label VARCHAR(255) DEFAULT NULL, field_default TEXT DEFAULT NULL, field_type ENUM(\'InputFieldGroup\', \'LabelAndAction\', \'LabelAndText\', \'LargeTextField\', \'TextField\', \'address\', \'bestOffer\', \'bool\', \'custom\', \'date\', \'datetime\', \'drawPad\', \'enum\', \'hashtagText\', \'hidden\', \'json\', \'json-editor\', \'selectWithSearch\', \'tariffCalculation\', \'textarea\', \'toggle\', \'upload\', \'wysiwyg\') DEFAULT NULL COMMENT \'(DC2Type:enum_flow_field_type)\', field_generatebyserver TINYINT(1) DEFAULT \'1\', field_module VARCHAR(255) DEFAULT NULL, field_modulefield VARCHAR(255) DEFAULT NULL, field_generatetype ENUM(\'fixed\', \'repeat-trigger\') DEFAULT NULL COMMENT \'(DC2Type:enum_generated_field_type)\', field_hideexpression TEXT DEFAULT NULL, field_disableexpression TEXT DEFAULT NULL, field_multiple ENUM(\'true\') DEFAULT NULL COMMENT \'(DC2Type:enum_field_multiple)\', field_fieldgroup VARCHAR(255) DEFAULT NULL, field_order INT DEFAULT 100, field_action_json TEXT DEFAULT NULL, field_hasborder TINYINT(1) DEFAULT \'1\', field_orientation ENUM(\'header-top\', \'label-left\', \'label-top\') DEFAULT NULL COMMENT \'(DC2Type:enum_field_orientation)\', field_address_type VARCHAR(255) DEFAULT NULL, field_enum_values TEXT DEFAULT NULL, field_fieldexpression TEXT DEFAULT NULL, field_upload_validation VARCHAR(255) DEFAULT NULL, field_custom TEXT DEFAULT NULL, field_read_only TINYINT(1) DEFAULT \'0\', required_c TINYINT(1) DEFAULT \'0\', field_no_backend_interaction TINYINT(1) DEFAULT \'0\', field_valueexpression TEXT DEFAULT NULL, field_auto_select_suggestions TINYINT(1) DEFAULT \'0\', api_label_c VARCHAR(255) DEFAULT NULL, remove_when_empty TINYINT(1) DEFAULT NULL, field_overwrite_value VARCHAR(1024) DEFAULT NULL, date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_69AB02108F1653AA (flow_action_id), INDEX fk_users_id_a2a4c0e6 (created_by), INDEX idx_field_type (field_type), INDEX fk_users_id_1bd9c57c (modified_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE flw_guidancefields_flw_guidancefieldvalidators_1_c (flow_field_id CHAR(36) NOT NULL, validator_id CHAR(36) NOT NULL, INDEX IDX_1AF8E9C8D77DC8E9 (flow_field_id), INDEX IDX_1AF8E9C8B0644AEC (validator_id), PRIMARY KEY(flow_field_id, validator_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE flw_guidancefields_flw_flowsteps_c (flow_field_id CHAR(36) NOT NULL, flow_step_id CHAR(36) NOT NULL, INDEX IDX_BD603318D77DC8E9 (flow_field_id), INDEX IDX_BD6033183082DA11 (flow_step_id), PRIMARY KEY(flow_field_id, flow_step_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE flw_flowsteps (id CHAR(36) NOT NULL, grid_template_id CHAR(36) DEFAULT NULL, created_by CHAR(36) NOT NULL, modified_user_id CHAR(36) DEFAULT NULL, type_c ENUM(\'DEFAULT\') DEFAULT \'DEFAULT\' COMMENT \'(DC2Type:enum_flow_step_type)\', json_fields_c TEXT DEFAULT NULL, key_c VARCHAR(150) DEFAULT NULL, is_card_c TINYINT(1) DEFAULT NULL, label_c VARCHAR(150) DEFAULT NULL, date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_22D849FD4AAAEE12 (grid_template_id), INDEX fk_users_id_173ce7ba (modified_user_id), INDEX fk_users_id_f1bb7a06 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE flw_flowsteps_flw_flowstepproperties_1_c (flow_step_id CHAR(36) NOT NULL, flow_step_property_id CHAR(36) NOT NULL, INDEX IDX_1CE72E483082DA11 (flow_step_id), INDEX IDX_1CE72E481D1C7A89 (flow_step_property_id), PRIMARY KEY(flow_step_id, flow_step_property_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE flw_flowstepslink (id CHAR(36) NOT NULL, flow_id CHAR(36) DEFAULT NULL, flow_step_id CHAR(36) DEFAULT NULL, created_by CHAR(36) NOT NULL, modified_user_id CHAR(36) DEFAULT NULL, order_c INT DEFAULT 1, date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_A16AEF377EB60D1B (flow_id), INDEX IDX_A16AEF373082DA11 (flow_step_id), INDEX fk_users_id_8367cbfd (created_by), INDEX fk_users_id_91522fa7 (modified_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE flw_flowstepproperties (id CHAR(36) NOT NULL, created_by CHAR(36) NOT NULL, modified_user_id CHAR(36) DEFAULT NULL, value_c VARCHAR(255) DEFAULT NULL, date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX fk_users_id_0a713cf0 (modified_user_id), INDEX fk_users_id_96f8b053 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE grid_panels (id CHAR(36) NOT NULL, created_by CHAR(36) NOT NULL, modified_user_id CHAR(36) DEFAULT NULL, key_c VARCHAR(255) DEFAULT NULL, type ENUM(\'embeddedGuidance\', \'list\') DEFAULT \'list\' COMMENT \'(DC2Type:enum_grid_type)\', params TEXT DEFAULT NULL, record_type VARCHAR(255) DEFAULT NULL, flow_id VARCHAR(255) DEFAULT NULL, flow_action ENUM(\'readOnly\') DEFAULT NULL COMMENT \'(DC2Type:enum_flow_action)\', record_id VARCHAR(255) DEFAULT NULL, show_primary_button TINYINT(1) DEFAULT \'0\', primary_button_title VARCHAR(255) DEFAULT NULL, default_title VARCHAR(255) DEFAULT NULL, title_expression VARCHAR(255) DEFAULT NULL, size VARCHAR(255) DEFAULT NULL, list_key VARCHAR(255) DEFAULT NULL, date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX fk_users_id_83e8c295 (created_by), INDEX fk_users_id_7d732f01 (modified_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE grid_panels_flw_guidancefieldvalidators_1_c (grid_panel_id CHAR(36) NOT NULL, validator_id CHAR(36) NOT NULL, INDEX IDX_8215AEC6E0B613B0 (grid_panel_id), INDEX IDX_8215AEC6B0644AEC (validator_id), PRIMARY KEY(grid_panel_id, validator_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE grid_gridtemplates (id CHAR(36) NOT NULL, created_by CHAR(36) NOT NULL, modified_user_id CHAR(36) DEFAULT NULL, key_c VARCHAR(255) DEFAULT NULL, json_fields_c TEXT DEFAULT NULL, date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX fk_users_id_5a0588cb (modified_user_id), INDEX fk_users_id_45b3d681 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE list_cell (id CHAR(36) NOT NULL, created_by CHAR(36) NOT NULL, modified_user_id CHAR(36) DEFAULT NULL, type ENUM(\'list_action_cell\', \'list_checkbox_cell\', \'list_dropdown_cell\', \'list_icon_link_cell\', \'list_icon_text_cell\', \'list_link_bold_top_two_liner_cell\', \'list_link_pink_down_two_liner_cell\', \'list_plus_cell\', \'list_simple_two_liner_cell\') DEFAULT NULL COMMENT \'(DC2Type:enum_cell_type)\', line1 VARCHAR(4000) DEFAULT NULL, line2 VARCHAR(4000) DEFAULT NULL, line3 VARCHAR(4000) DEFAULT NULL, action_key VARCHAR(255) DEFAULT NULL, column_label VARCHAR(255) DEFAULT NULL, linkto ENUM(\'dashboard\', \'focus_mode\', \'guidance_mode\') DEFAULT NULL COMMENT \'(DC2Type:enum_link_to)\', link VARCHAR(255) DEFAULT NULL, mainmenukey VARCHAR(255) DEFAULT NULL, dashboardid VARCHAR(255) DEFAULT NULL, customhandler VARCHAR(255) DEFAULT NULL, params_c TEXT DEFAULT NULL, icon VARCHAR(255) DEFAULT NULL, line1csvheader VARCHAR(255) DEFAULT NULL, line2csvheader VARCHAR(255) DEFAULT NULL, line3csvheader VARCHAR(255) DEFAULT NULL, visible_c ENUM(\'DEFAULT\', \'IN_CSV\', \'IN_DWP\') DEFAULT \'DEFAULT\' COMMENT \'(DC2Type:enum_cell_visibility)\', date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX fk_users_id_0412f82d (modified_user_id), INDEX fk_users_id_63c8a025 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE list_cells (id CHAR(36) NOT NULL, row_bar_id CHAR(36) DEFAULT NULL, list_id CHAR(36) DEFAULT NULL, cell_id CHAR(36) DEFAULT NULL, created_by CHAR(36) NOT NULL, modified_user_id CHAR(36) DEFAULT NULL, order_c INT DEFAULT 10, date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_8C525FDB58BCF6E8 (row_bar_id), INDEX IDX_8C525FDB3DAE168B (list_id), INDEX IDX_8C525FDBCB39D93A (cell_id), INDEX fk_users_id_c0b56d3a (modified_user_id), INDEX fk_users_id_cbcb290a (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE list_dynamic_list (id CHAR(36) NOT NULL, filter_id CHAR(36) DEFAULT NULL, top_bar_id CHAR(36) DEFAULT NULL, external_object_id CHAR(36) DEFAULT NULL, created_by CHAR(36) NOT NULL, modified_user_id CHAR(36) DEFAULT NULL, display_footer TINYINT(1) DEFAULT \'0\', base_object VARCHAR(255) DEFAULT NULL, standard_filter TEXT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, search_config TEXT DEFAULT NULL, items_per_page VARCHAR(5) DEFAULT \'10\', default_filter_values TEXT DEFAULT NULL, filters_have_changed TINYINT(1) DEFAULT \'0\', combined TINYINT(1) DEFAULT \'0\', responsive TINYINT(1) DEFAULT \'0\' NOT NULL, fix_pagination TINYINT(1) DEFAULT \'1\' NOT NULL, quick_search TINYINT(1) DEFAULT \'0\' NOT NULL, date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_2F5207E7D395B25E (filter_id), INDEX IDX_2F5207E75F678FB6 (top_bar_id), INDEX IDX_2F5207E7776F93F (external_object_id), INDEX fk_users_id_8ba3dc94 (modified_user_id), INDEX fk_users_id_a0c59bd5 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE list_row_action (id CHAR(36) NOT NULL, flow_action_id CHAR(36) DEFAULT NULL, row_bar_id CHAR(36) DEFAULT NULL, created_by CHAR(36) NOT NULL, modified_user_id CHAR(36) DEFAULT NULL, type ENUM(\'CALLBACK\') DEFAULT \'CALLBACK\' COMMENT \'(DC2Type:enum_action_type)\', icon VARCHAR(255) DEFAULT NULL, action_name VARCHAR(255) DEFAULT NULL, conditionsenabled_c TEXT DEFAULT NULL, conditions_hide_c TEXT DEFAULT NULL, order_c INT DEFAULT 10, params_c TEXT DEFAULT NULL, date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_CDC72B9C8F1653AA (flow_action_id), INDEX IDX_CDC72B9C58BCF6E8 (row_bar_id), INDEX fk_users_id_e9284788 (created_by), INDEX fk_users_id_a5f43d19 (modified_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE list_row_bar (id CHAR(36) NOT NULL, created_by CHAR(36) NOT NULL, modified_user_id CHAR(36) DEFAULT NULL, date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX fk_users_id_31ec3949 (created_by), INDEX fk_users_id_8fd6281b (modified_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE list_sorting_options (id CHAR(36) NOT NULL, created_by CHAR(36) NOT NULL, modified_user_id CHAR(36) DEFAULT NULL, sort_key VARCHAR(255) DEFAULT NULL, order_by ENUM(\'ASC\', \'DESC\') DEFAULT \'ASC\' COMMENT \'(DC2Type:enum_order)\', date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX fk_users_id_34c99210 (modified_user_id), INDEX fk_users_id_50a5c25e (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE list_top_action (id CHAR(36) NOT NULL, flw_actions_id_c CHAR(36) DEFAULT NULL, created_by CHAR(36) NOT NULL, modified_user_id CHAR(36) DEFAULT NULL, type ENUM(\'CALLBACK\') DEFAULT \'CALLBACK\' COMMENT \'(DC2Type:enum_action_type)\', icon VARCHAR(255) DEFAULT NULL, action_name VARCHAR(255) DEFAULT NULL, order_c INT DEFAULT 10, params_c TEXT DEFAULT NULL, key_c VARCHAR(255) DEFAULT NULL, conditionsenabled_c TEXT DEFAULT NULL, conditions_hide_c TEXT DEFAULT NULL, date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX fk_users_id_0563b06e (modified_user_id), INDEX fk_flw_actions_id_e466e703 (flw_actions_id_c), INDEX fk_users_id_de98942b (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE list_topbar (id CHAR(36) NOT NULL, created_by CHAR(36) NOT NULL, modified_user_id CHAR(36) DEFAULT NULL, selectall TINYINT(1) DEFAULT \'0\', can_export_to_csv_c TINYINT(1) DEFAULT NULL, date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX fk_users_id_2a9c7a03 (created_by), INDEX fk_users_id_c19ecc09 (modified_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE list_topbar_list_sorting_options_c (list_top_bar_id CHAR(36) NOT NULL, list_sorting_option_id CHAR(36) NOT NULL, INDEX IDX_A9A698854AFB6A (list_top_bar_id), INDEX IDX_A9A698A0237525 (list_sorting_option_id), PRIMARY KEY(list_top_bar_id, list_sorting_option_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE list_topbar_list_top_action_c (list_top_bar_id CHAR(36) NOT NULL, list_top_action_id CHAR(36) NOT NULL, INDEX IDX_D6A3836A854AFB6A (list_top_bar_id), INDEX IDX_D6A3836A68E6E44D (list_top_action_id), PRIMARY KEY(list_top_bar_id, list_top_action_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE menu_mainmenu (id CHAR(36) NOT NULL, created_by CHAR(36) NOT NULL, modified_user_id CHAR(36) DEFAULT NULL, params_c TEXT DEFAULT NULL, display_order_c VARCHAR(255) DEFAULT \'10\', link_c VARCHAR(255) DEFAULT NULL, icon_c VARCHAR(255) DEFAULT NULL, date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX fk_users_id_2a84509c (modified_user_id), INDEX fk_users_id_05843a67 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE menu_mainmenu_dash_dashboard_c (menu_id CHAR(36) NOT NULL, dashboard_id CHAR(36) NOT NULL, INDEX IDX_6BE0E5BCCCD7E912 (menu_id), INDEX IDX_6BE0E5BCB9D04D2B (dashboard_id), PRIMARY KEY(menu_id, dashboard_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE phinxlog (version BIGINT NOT NULL, migration_name VARCHAR(100) DEFAULT NULL, start_time DATETIME DEFAULT NULL, end_time DATETIME DEFAULT NULL, breakpoint TINYINT(1) DEFAULT \'0\' NOT NULL, PRIMARY KEY(version)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE securitygroups (id CHAR(36) NOT NULL, assigned_user_id CHAR(36) DEFAULT NULL, created_by CHAR(36) NOT NULL, modified_user_id CHAR(36) DEFAULT NULL, external_c TINYINT(1) DEFAULT \'0\', main_groups_c ENUM(\'CUSTOMER\', \'DASHBOARD\', \'DEALER\', \'EMPLOYEE\', \'THIRD_PARTY\') DEFAULT \'THIRD_PARTY\' COMMENT \'(DC2Type:enum_security_group_type)\', reliable_c TINYINT(1) DEFAULT \'0\', code VARCHAR(255) NOT NULL, status ENUM(\'Active\', \'Inactive\') DEFAULT \'Active\' NOT NULL COMMENT \'(DC2Type:enum_user_status)\', date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX fk_users_id_06b55379 (assigned_user_id), INDEX fk_users_id_5fc842bf (modified_user_id), INDEX fk_users_id_26ee194a (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE securitygroups_acl_roles (security_group_id CHAR(36) NOT NULL, acl_role_id CHAR(36) NOT NULL, INDEX IDX_A13371969D3F5E95 (security_group_id), INDEX IDX_A1337196BD33296F (acl_role_id), PRIMARY KEY(security_group_id, acl_role_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE securitygroups_api (id CHAR(36) NOT NULL, created_by CHAR(36) NOT NULL, modified_user_id CHAR(36) DEFAULT NULL, http_method ENUM(\'DELETE\', \'GET\', \'PATCH\', \'POST\', \'PUT\') DEFAULT NULL COMMENT \'(DC2Type:enum_http_method)\', route VARCHAR(255) NOT NULL, allowed_usergroups TEXT DEFAULT NULL, date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX idx_route (route), INDEX fk_users_id_9d312a8e (modified_user_id), INDEX fk_users_id_059e836c (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE securitygroups_records (id CHAR(36) NOT NULL, securitygroup_id CHAR(36) DEFAULT NULL, record_id CHAR(36) DEFAULT NULL, module CHAR(36) DEFAULT NULL, date_modified DATETIME DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, created_by CHAR(36) NOT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX idx_securitygroups_records_mod (module, deleted, record_id, securitygroup_id), INDEX idx_securitygroups_records_mod_sec (module, securitygroup_id), INDEX idx_securitygroups_records_del (deleted), INDEX fk_securitygroups_id_86a09ddf (securitygroup_id), INDEX idx_recordid_deleted (record_id, deleted), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE securitygroups_users (id CHAR(36) NOT NULL, securitygroup_id CHAR(36) DEFAULT NULL, user_id CHAR(36) DEFAULT NULL, primary_group TINYINT(1) DEFAULT NULL, date_modified DATETIME DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX securitygroups_users_idxd (user_id, deleted, securitygroup_id), INDEX fk_securitygroups_id_c78f626d (securitygroup_id), INDEX IDX_3B3227D2A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fe_selectwithsearch (id CHAR(36) NOT NULL, created_by CHAR(36) NOT NULL, modified_user_id CHAR(36) DEFAULT NULL, base_object VARCHAR(255) DEFAULT NULL, filters TEXT DEFAULT NULL, items_on_page VARCHAR(255) DEFAULT \'50\', order_by VARCHAR(255) DEFAULT NULL, option_label VARCHAR(255) DEFAULT NULL, filter_string VARCHAR(255) DEFAULT NULL, option_key VARCHAR(255) DEFAULT NULL, needs_query TINYINT(1) NOT NULL, date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX fk_users_id_d9e552b0 (modified_user_id), INDEX fk_users_id_7fce87b3 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trans_translation (id CHAR(36) NOT NULL, created_by CHAR(36) NOT NULL, modified_user_id CHAR(36) DEFAULT NULL, name VARBINARY(255) DEFAULT NULL, description VARBINARY(255) DEFAULT NULL, locale ENUM(\'de_DE\', \'en_BE\', \'fr_BE\', \'nl_BE\') DEFAULT NULL COMMENT \'(DC2Type:enum_locale)\', translation TEXT DEFAULT NULL, domain ENUM(\'FREELETTER\', \'INVOICE\', \'QUOTE\', \'bean\', \'body-all\', \'body-email\', \'body-post\', \'body-post-email\', \'body-render-only\', \'body-sms\', \'conditional-message\', \'dashboard-grid\', \'discount-description\', \'discount-general-conditions\', \'duration\', \'errors\', \'flash-message\', \'guidance-enum\', \'guidance-field\', \'guidance-grid\', \'guidance-title\', \'hashtag\', \'jbilling-item\', \'list-column\', \'list-filter\', \'list-rowbar\', \'list-title\', \'list-topbar\', \'main-menu\', \'messages\', \'module\', \'package-description\', \'package-general-conditions\', \'package-product-description\', \'package-product-general-conditions\', \'package-product-pdf-subtitle\', \'package-product-pdf-title\', \'package-product-summary\', \'package-properties-description\', \'package-properties-general-conditions\', \'package-properties-pdf-conditions\', \'package-special-conditions\', \'package-special-right-of-withdrawal\', \'plus-menu\', \'registration\', \'saleschannel\', \'sepa_block\', \'sidebar\', \'sub-menu\', \'subject-all\', \'subject-email\', \'subject-post\', \'subject-post-email\', \'subject-render-only\', \'subject-sms\', \'template-comment\', \'uom\', \'upsell-package-description\', \'upsell-package-general-conditions\') DEFAULT NULL COMMENT \'(DC2Type:enum_translation_domain)\', date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX key_locale (locale), INDEX fk_users_id_b5f886c4 (created_by), INDEX key_domain (domain), INDEX fk_users_id_349381bf (modified_user_id), UNIQUE INDEX key_name_domain_description_locale (name, domain, description, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id CHAR(36) NOT NULL, user_name VARCHAR(60) DEFAULT NULL, user_hash VARCHAR(255) DEFAULT NULL, system_generated_password TINYINT(1) DEFAULT NULL, pwd_last_changed DATETIME DEFAULT NULL, sugar_login TINYINT(1) DEFAULT \'1\', first_name VARCHAR(30) DEFAULT NULL, last_name VARCHAR(30) DEFAULT NULL, external_auth_only TINYINT(1) DEFAULT \'0\', description TEXT DEFAULT NULL, date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, created_by CHAR(36) NOT NULL, status ENUM(\'Active\', \'Inactive\') DEFAULT NULL COMMENT \'(DC2Type:enum_user_status)\', deleted TINYINT(1) NOT NULL, portal_only TINYINT(1) DEFAULT \'0\', employee_status VARCHAR(100) DEFAULT NULL, is_group TINYINT(1) DEFAULT \'0\', selfcare_toc TINYINT(1) DEFAULT \'0\' NOT NULL, preferred_locale ENUM(\'de_DE\', \'en_BE\', \'fr_BE\', \'nl_BE\') DEFAULT \'en_BE\' COMMENT \'(DC2Type:enum_locale)\', INDEX idx_user_name (user_name, is_group, status, last_name, first_name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_guidance_recovery (id CHAR(36) NOT NULL, recovery_data LONGTEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_login (id CHAR(36) NOT NULL, last_login DATETIME NOT NULL, jwt TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE flw_guidancefieldvalidators (id CHAR(36) NOT NULL, created_by CHAR(36) NOT NULL, modified_user_id CHAR(36) DEFAULT NULL, validator_value TEXT DEFAULT NULL, validator_type ENUM(\'alnum\', \'alpha\', \'bool\', \'choice\', \'digit\', \'double\', \'float\', \'int\', \'long\', \'lower\', \'numeric\', \'real\', \'scalar\', \'upper\') DEFAULT NULL COMMENT \'(DC2Type:enum_validator_type)\', validator_min INT DEFAULT NULL, validator_max INT DEFAULT NULL, validator ENUM(\'AlreadyContracted\', \'Blank\', \'Choice\', \'Date\', \'Ean\', \'Email\', \'EqualTo\', \'File\', \'FixedPhoneNumber\', \'GreaterThan\', \'GreaterThanOrEqual\', \'HasDrop\', \'HasPrepaid\', \'HasResidential\', \'Iban\', \'IsEndOfMonth\', \'IsFalse\', \'IsNotGos\', \'IsNull\', \'IsTrue\', \'Length\', \'LessThan\', \'LessThanOrEqual\', \'MobilePhoneNumber\', \'MultiEmail\', \'Nace\', \'NotBlank\', \'NotEqualTo\', \'NotInList\', \'NotNull\', \'PhoneNumber\', \'Range\', \'Regex\', \'Type\', \'Url\', \'Vat\') DEFAULT \'NotBlank\' COMMENT \'(DC2Type:enum_validator)\', validator_field VARCHAR(255) DEFAULT \'__self__\', validator_mode TINYINT(1) DEFAULT \'0\', validation_group VARCHAR(255) DEFAULT NULL, validator_mutator ENUM(\'day\', \'month\', \'year\') DEFAULT NULL COMMENT \'(DC2Type:enum_validator_mutator)\', custom_error_message VARCHAR(255) DEFAULT NULL, validator_maxfilesize VARCHAR(255) DEFAULT NULL, show_on_top TINYINT(1) NOT NULL, and_not_null TINYINT(1) NOT NULL, date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX fk_users_id_eaaeca80 (created_by), INDEX fk_users_id_b65e1ce5 (modified_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE flw_guidancefieldsvalidators_conditions (parent_id CHAR(36) NOT NULL, child_id CHAR(36) NOT NULL, INDEX IDX_7DE190B1727ACA70 (parent_id), INDEX IDX_7DE190B1DD62C21B (child_id), PRIMARY KEY(parent_id, child_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acl_actions_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, created_by CHAR(36) DEFAULT NULL, name VARCHAR(150) DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, category VARCHAR(100) DEFAULT NULL, aclaccess INT DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acl_roles_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, created_by CHAR(36) DEFAULT NULL, name VARCHAR(150) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, code VARCHAR(255) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acl_roles_users_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', acl_role_id CHAR(36) NOT NULL, user_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, acl_role_id, user_id), PRIMARY KEY(audit_timestamp, acl_role_id, user_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acl_roles_actions_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', acl_role_id CHAR(36) NOT NULL, acl_action_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, acl_role_id, acl_action_id), PRIMARY KEY(audit_timestamp, acl_role_id, acl_action_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE conditionalmessage_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, assigned_user_id CHAR(36) DEFAULT NULL, created_by CHAR(36) DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, domain VARCHAR(255) DEFAULT NULL, action_c VARCHAR(255) DEFAULT NULL, icon_c VARCHAR(255) DEFAULT NULL, record_type VARCHAR(255) DEFAULT NULL, record_id VARCHAR(255) DEFAULT NULL, priority_c INT DEFAULT NULL, description_params TEXT DEFAULT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE conditional_message_validators_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', conditional_message_id CHAR(36) NOT NULL, validator_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, conditional_message_id, validator_id), PRIMARY KEY(audit_timestamp, conditional_message_id, validator_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE conf_defaults_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, created_by CHAR(36) DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, systemid VARCHAR(255) DEFAULT NULL, parameter VARCHAR(255) DEFAULT NULL, value LONGTEXT DEFAULT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dash_dashboard_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, grid_gridtemplates_id_c CHAR(36) DEFAULT NULL, dashboard_menu_id CHAR(36) DEFAULT NULL, search_id CHAR(36) DEFAULT NULL, filter_id CHAR(36) DEFAULT NULL, created_by CHAR(36) DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, type_c VARCHAR(255) DEFAULT NULL, key_c VARCHAR(255) DEFAULT NULL, menu_sort_c VARCHAR(255) DEFAULT NULL, main_record_type_c VARCHAR(255) DEFAULT NULL, filters_listkey VARCHAR(255) DEFAULT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dash_dashboard_dash_dashboardproperties_c_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', dashboard_id CHAR(36) NOT NULL, dashboard_property_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, dashboard_id, dashboard_property_id), PRIMARY KEY(audit_timestamp, dashboard_id, dashboard_property_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dash_dashboardmenu_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, created_by CHAR(36) DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dash_dashboardmenu_dash_dashboardmenuactiongroup_1_c_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', dashboard_menu_id CHAR(36) NOT NULL, dashboard_menu_action_group_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, dashboard_menu_id, dashboard_menu_action_group_id), PRIMARY KEY(audit_timestamp, dashboard_menu_id, dashboard_menu_action_group_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dash_dashboardmenu_dash_menuactions_1_c_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', dashboard_menu_id CHAR(36) NOT NULL, dashboard_menu_action_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, dashboard_menu_id, dashboard_menu_action_id), PRIMARY KEY(audit_timestamp, dashboard_menu_id, dashboard_menu_action_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dash_menuactions_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, flw_actions_id_c CHAR(36) DEFAULT NULL, created_by CHAR(36) DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, label_c VARCHAR(100) DEFAULT NULL, sort_order INT DEFAULT NULL, icon_c VARCHAR(100) DEFAULT NULL, params_c TEXT DEFAULT NULL, conditionsenabled_c TEXT DEFAULT NULL, conditions_hide_c TEXT DEFAULT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dash_dashboardmenuactiongroup_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, created_by CHAR(36) DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, label_c VARCHAR(100) DEFAULT NULL, sort_order INT DEFAULT NULL, icon VARCHAR(255) DEFAULT NULL, class VARCHAR(255) DEFAULT NULL, conditions_hide_c TEXT DEFAULT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dash_menuactiongroup_x_dash_menuactiongroup_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', parent_id CHAR(36) NOT NULL, child_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, parent_id, child_id), PRIMARY KEY(audit_timestamp, parent_id, child_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dash_dashboardmenuactiongroup_dash_menuactions_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', dashboard_menu_action_group_id CHAR(36) NOT NULL, dashboard_menu_action_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, dashboard_menu_action_group_id, dashboard_menu_action_id), PRIMARY KEY(audit_timestamp, dashboard_menu_action_group_id, dashboard_menu_action_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dash_dashboardproperties_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, created_by CHAR(36) DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, value_c VARCHAR(255) DEFAULT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE list_external_object_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, created_by CHAR(36) DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, class_handler VARCHAR(255) DEFAULT NULL, module_name_c VARCHAR(255) DEFAULT NULL, list_only TINYINT(1) DEFAULT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE list_external_object_linkfields_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, external_object_id CHAR(36) DEFAULT NULL, created_by CHAR(36) DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, suite_bean_name VARCHAR(255) DEFAULT NULL, suite_bean_field VARCHAR(255) DEFAULT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fltrs_filters_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, created_by CHAR(36) DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, default_filters_json_c TEXT DEFAULT NULL, filterskey_c VARCHAR(64) DEFAULT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fltrs_fields_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, created_by CHAR(36) DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, label_c VARCHAR(255) DEFAULT NULL, operator VARCHAR(6) DEFAULT NULL, field_key_c VARCHAR(255) DEFAULT NULL, field_sql_c TEXT DEFAULT NULL, field_options_c TEXT DEFAULT NULL, field_options_enum_key_c VARCHAR(255) DEFAULT NULL, sort_c VARCHAR(4) DEFAULT NULL, type_c VARCHAR(255) DEFAULT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fltrs_fieldsgroup_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, created_by CHAR(36) DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, sort_c VARCHAR(5) DEFAULT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fltrs_fieldsgroup_fltrs_fields_1_c_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', filter_field_group_id CHAR(36) NOT NULL, filter_field_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, filter_field_group_id, filter_field_id), PRIMARY KEY(audit_timestamp, filter_field_group_id, filter_field_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fltrs_fieldsgroup_fltrs_filters_1_c_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', filter_field_group_id CHAR(36) NOT NULL, filter_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, filter_field_group_id, filter_id), PRIMARY KEY(audit_timestamp, filter_field_group_id, filter_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE find_search_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, created_by CHAR(36) DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, link_to VARCHAR(255) DEFAULT NULL, params TEXT DEFAULT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE flw_flows_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, action_id CHAR(36) DEFAULT NULL, created_by CHAR(36) DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, key_c VARCHAR(255) DEFAULT NULL, type_c VARCHAR(255) DEFAULT NULL, base_object_c VARCHAR(150) DEFAULT NULL, loading_message_c VARCHAR(150) DEFAULT NULL, error_message TEXT DEFAULT NULL, external TINYINT(1) DEFAULT NULL, label_c VARCHAR(255) DEFAULT NULL, use_api_label_c TINYINT(1) DEFAULT NULL, is_config TINYINT(1) DEFAULT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE flw_actions_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, created_by CHAR(36) DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, guid VARCHAR(255) DEFAULT NULL, json TEXT DEFAULT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE flw_guidancefields_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, flow_action_id CHAR(36) DEFAULT NULL, created_by CHAR(36) DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, field_id TEXT DEFAULT NULL, field_label VARCHAR(255) DEFAULT NULL, field_default TEXT DEFAULT NULL, field_type VARCHAR(255) DEFAULT NULL, field_generatebyserver TINYINT(1) DEFAULT NULL, field_module VARCHAR(255) DEFAULT NULL, field_modulefield VARCHAR(255) DEFAULT NULL, field_generatetype VARCHAR(255) DEFAULT NULL, field_hideexpression TEXT DEFAULT NULL, field_disableexpression TEXT DEFAULT NULL, field_multiple VARCHAR(255) DEFAULT NULL, field_fieldgroup VARCHAR(255) DEFAULT NULL, field_order INT DEFAULT NULL, field_action_json TEXT DEFAULT NULL, field_hasborder TINYINT(1) DEFAULT NULL, field_orientation VARCHAR(255) DEFAULT NULL, field_address_type VARCHAR(255) DEFAULT NULL, field_enum_values TEXT DEFAULT NULL, field_fieldexpression TEXT DEFAULT NULL, field_upload_validation VARCHAR(255) DEFAULT NULL, field_custom TEXT DEFAULT NULL, field_read_only TINYINT(1) DEFAULT NULL, required_c TINYINT(1) DEFAULT NULL, field_no_backend_interaction TINYINT(1) DEFAULT NULL, field_valueexpression TEXT DEFAULT NULL, field_auto_select_suggestions TINYINT(1) DEFAULT NULL, api_label_c VARCHAR(255) DEFAULT NULL, remove_when_empty TINYINT(1) DEFAULT NULL, field_overwrite_value VARCHAR(1024) DEFAULT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE flw_guidancefields_flw_guidancefieldvalidators_1_c_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', flow_field_id CHAR(36) NOT NULL, validator_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, flow_field_id, validator_id), PRIMARY KEY(audit_timestamp, flow_field_id, validator_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE flw_guidancefields_flw_flowsteps_c_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', flow_field_id CHAR(36) NOT NULL, flow_step_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, flow_field_id, flow_step_id), PRIMARY KEY(audit_timestamp, flow_field_id, flow_step_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE flw_flowsteps_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, grid_template_id CHAR(36) DEFAULT NULL, created_by CHAR(36) DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, type_c VARCHAR(255) DEFAULT NULL, json_fields_c TEXT DEFAULT NULL, key_c VARCHAR(150) DEFAULT NULL, is_card_c TINYINT(1) DEFAULT NULL, label_c VARCHAR(150) DEFAULT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE flw_flowsteps_flw_flowstepproperties_1_c_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', flow_step_id CHAR(36) NOT NULL, flow_step_property_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, flow_step_id, flow_step_property_id), PRIMARY KEY(audit_timestamp, flow_step_id, flow_step_property_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE flw_flowstepslink_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, flow_id CHAR(36) DEFAULT NULL, flow_step_id CHAR(36) DEFAULT NULL, created_by CHAR(36) DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, order_c INT DEFAULT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE flw_flowstepproperties_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, created_by CHAR(36) DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, value_c VARCHAR(255) DEFAULT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE grid_panels_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, created_by CHAR(36) DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, key_c VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, params TEXT DEFAULT NULL, record_type VARCHAR(255) DEFAULT NULL, flow_id VARCHAR(255) DEFAULT NULL, flow_action VARCHAR(255) DEFAULT NULL, record_id VARCHAR(255) DEFAULT NULL, show_primary_button TINYINT(1) DEFAULT NULL, primary_button_title VARCHAR(255) DEFAULT NULL, default_title VARCHAR(255) DEFAULT NULL, title_expression VARCHAR(255) DEFAULT NULL, size VARCHAR(255) DEFAULT NULL, list_key VARCHAR(255) DEFAULT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE grid_panels_flw_guidancefieldvalidators_1_c_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', grid_panel_id CHAR(36) NOT NULL, validator_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, grid_panel_id, validator_id), PRIMARY KEY(audit_timestamp, grid_panel_id, validator_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE grid_gridtemplates_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, created_by CHAR(36) DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, key_c VARCHAR(255) DEFAULT NULL, json_fields_c TEXT DEFAULT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE list_cell_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, created_by CHAR(36) DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, line1 VARCHAR(4000) DEFAULT NULL, line2 VARCHAR(4000) DEFAULT NULL, line3 VARCHAR(4000) DEFAULT NULL, action_key VARCHAR(255) DEFAULT NULL, column_label VARCHAR(255) DEFAULT NULL, linkto VARCHAR(255) DEFAULT NULL, link VARCHAR(255) DEFAULT NULL, mainmenukey VARCHAR(255) DEFAULT NULL, dashboardid VARCHAR(255) DEFAULT NULL, customhandler VARCHAR(255) DEFAULT NULL, params_c TEXT DEFAULT NULL, icon VARCHAR(255) DEFAULT NULL, line1csvheader VARCHAR(255) DEFAULT NULL, line2csvheader VARCHAR(255) DEFAULT NULL, line3csvheader VARCHAR(255) DEFAULT NULL, visible_c VARCHAR(255) DEFAULT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE list_cells_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, row_bar_id CHAR(36) DEFAULT NULL, list_id CHAR(36) DEFAULT NULL, cell_id CHAR(36) DEFAULT NULL, created_by CHAR(36) DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, order_c INT DEFAULT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE list_dynamic_list_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, filter_id CHAR(36) DEFAULT NULL, top_bar_id CHAR(36) DEFAULT NULL, external_object_id CHAR(36) DEFAULT NULL, created_by CHAR(36) DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, display_footer TINYINT(1) DEFAULT NULL, base_object VARCHAR(255) DEFAULT NULL, standard_filter TEXT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, search_config TEXT DEFAULT NULL, items_per_page VARCHAR(5) DEFAULT NULL, default_filter_values TEXT DEFAULT NULL, filters_have_changed TINYINT(1) DEFAULT NULL, combined TINYINT(1) DEFAULT NULL, responsive TINYINT(1) DEFAULT NULL, fix_pagination TINYINT(1) DEFAULT NULL, quick_search TINYINT(1) DEFAULT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE list_row_action_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, flow_action_id CHAR(36) DEFAULT NULL, row_bar_id CHAR(36) DEFAULT NULL, created_by CHAR(36) DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, icon VARCHAR(255) DEFAULT NULL, action_name VARCHAR(255) DEFAULT NULL, conditionsenabled_c TEXT DEFAULT NULL, conditions_hide_c TEXT DEFAULT NULL, order_c INT DEFAULT NULL, params_c TEXT DEFAULT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE list_row_bar_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, created_by CHAR(36) DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE list_sorting_options_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, created_by CHAR(36) DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, sort_key VARCHAR(255) DEFAULT NULL, order_by VARCHAR(255) DEFAULT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE list_top_action_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, flw_actions_id_c CHAR(36) DEFAULT NULL, created_by CHAR(36) DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, icon VARCHAR(255) DEFAULT NULL, action_name VARCHAR(255) DEFAULT NULL, order_c INT DEFAULT NULL, params_c TEXT DEFAULT NULL, key_c VARCHAR(255) DEFAULT NULL, conditionsenabled_c TEXT DEFAULT NULL, conditions_hide_c TEXT DEFAULT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE list_topbar_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, created_by CHAR(36) DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, selectall TINYINT(1) DEFAULT NULL, can_export_to_csv_c TINYINT(1) DEFAULT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE list_topbar_list_sorting_options_c_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', list_top_bar_id CHAR(36) NOT NULL, list_sorting_option_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, list_top_bar_id, list_sorting_option_id), PRIMARY KEY(audit_timestamp, list_top_bar_id, list_sorting_option_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE list_topbar_list_top_action_c_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', list_top_bar_id CHAR(36) NOT NULL, list_top_action_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, list_top_bar_id, list_top_action_id), PRIMARY KEY(audit_timestamp, list_top_bar_id, list_top_action_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE menu_mainmenu_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, created_by CHAR(36) DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, params_c TEXT DEFAULT NULL, display_order_c VARCHAR(255) DEFAULT NULL, link_c VARCHAR(255) DEFAULT NULL, icon_c VARCHAR(255) DEFAULT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE menu_mainmenu_dash_dashboard_c_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', menu_id CHAR(36) NOT NULL, dashboard_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, menu_id, dashboard_id), PRIMARY KEY(audit_timestamp, menu_id, dashboard_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE securitygroups_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, assigned_user_id CHAR(36) DEFAULT NULL, created_by CHAR(36) DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, external_c TINYINT(1) DEFAULT NULL, main_groups_c VARCHAR(255) DEFAULT NULL, reliable_c TINYINT(1) DEFAULT NULL, code VARCHAR(255) DEFAULT NULL, status VARCHAR(255) DEFAULT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE securitygroups_acl_roles_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', security_group_id CHAR(36) NOT NULL, acl_role_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, security_group_id, acl_role_id), PRIMARY KEY(audit_timestamp, security_group_id, acl_role_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE securitygroups_api_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, created_by CHAR(36) DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, http_method VARCHAR(255) DEFAULT NULL, route VARCHAR(255) DEFAULT NULL, allowed_usergroups TEXT DEFAULT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE securitygroups_records_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, securitygroup_id CHAR(36) DEFAULT NULL, record_id CHAR(36) DEFAULT NULL, module CHAR(36) DEFAULT NULL, date_modified DATETIME DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, created_by CHAR(36) DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE securitygroups_users_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, securitygroup_id CHAR(36) DEFAULT NULL, user_id CHAR(36) DEFAULT NULL, primary_group TINYINT(1) DEFAULT NULL, date_modified DATETIME DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fe_selectwithsearch_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, created_by CHAR(36) DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, base_object VARCHAR(255) DEFAULT NULL, filters TEXT DEFAULT NULL, items_on_page VARCHAR(255) DEFAULT NULL, order_by VARCHAR(255) DEFAULT NULL, option_label VARCHAR(255) DEFAULT NULL, filter_string VARCHAR(255) DEFAULT NULL, option_key VARCHAR(255) DEFAULT NULL, needs_query TINYINT(1) DEFAULT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trans_translation_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, created_by CHAR(36) DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, name VARBINARY(255) DEFAULT NULL, description VARBINARY(255) DEFAULT NULL, locale VARCHAR(255) DEFAULT NULL, translation TEXT DEFAULT NULL, domain VARCHAR(255) DEFAULT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, user_name VARCHAR(60) DEFAULT NULL, user_hash VARCHAR(255) DEFAULT NULL, system_generated_password TINYINT(1) DEFAULT NULL, pwd_last_changed DATETIME DEFAULT NULL, sugar_login TINYINT(1) DEFAULT NULL, first_name VARCHAR(30) DEFAULT NULL, last_name VARCHAR(30) DEFAULT NULL, external_auth_only TINYINT(1) DEFAULT NULL, description TEXT DEFAULT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, created_by CHAR(36) DEFAULT NULL, status VARCHAR(255) DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, portal_only TINYINT(1) DEFAULT NULL, employee_status VARCHAR(100) DEFAULT NULL, is_group TINYINT(1) DEFAULT NULL, selfcare_toc TINYINT(1) DEFAULT NULL, preferred_locale VARCHAR(255) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_guidance_recovery_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, recovery_data LONGTEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_login_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, last_login DATETIME DEFAULT NULL, jwt TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE flw_guidancefieldvalidators_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, created_by CHAR(36) DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, validator_value TEXT DEFAULT NULL, validator_type VARCHAR(255) DEFAULT NULL, validator_min INT DEFAULT NULL, validator_max INT DEFAULT NULL, validator VARCHAR(255) DEFAULT NULL, validator_field VARCHAR(255) DEFAULT NULL, validator_mode TINYINT(1) DEFAULT NULL, validation_group VARCHAR(255) DEFAULT NULL, validator_mutator VARCHAR(255) DEFAULT NULL, custom_error_message VARCHAR(255) DEFAULT NULL, validator_maxfilesize VARCHAR(255) DEFAULT NULL, show_on_top TINYINT(1) DEFAULT NULL, and_not_null TINYINT(1) DEFAULT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE flw_guidancefieldsvalidators_conditions_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', parent_id CHAR(36) NOT NULL, child_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, parent_id, child_id), PRIMARY KEY(audit_timestamp, parent_id, child_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE acl_roles_users ADD CONSTRAINT FK_500BDB06BD33296F FOREIGN KEY (acl_role_id) REFERENCES acl_roles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acl_roles_users ADD CONSTRAINT FK_500BDB06A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acl_roles_actions ADD CONSTRAINT FK_6B13067FBD33296F FOREIGN KEY (acl_role_id) REFERENCES acl_roles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acl_roles_actions ADD CONSTRAINT FK_6B13067FEA37C600 FOREIGN KEY (acl_action_id) REFERENCES acl_actions (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE conditionalmessage ADD CONSTRAINT FK_8024CC5ADF66B1A FOREIGN KEY (assigned_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE conditionalmessage ADD CONSTRAINT FK_8024CC5DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE conditionalmessage ADD CONSTRAINT FK_8024CC5BAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE conditional_message_validators ADD CONSTRAINT FK_74E7F6425091F067 FOREIGN KEY (conditional_message_id) REFERENCES conditionalmessage (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE conditional_message_validators ADD CONSTRAINT FK_74E7F642B0644AEC FOREIGN KEY (validator_id) REFERENCES flw_guidancefieldvalidators (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE conf_defaults ADD CONSTRAINT FK_FD98C5E2DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE conf_defaults ADD CONSTRAINT FK_FD98C5E2BAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE dash_dashboard ADD CONSTRAINT FK_DCEE68C4F76276F9 FOREIGN KEY (grid_gridtemplates_id_c) REFERENCES grid_gridtemplates (id)');
        $this->addSql('ALTER TABLE dash_dashboard ADD CONSTRAINT FK_DCEE68C47D333B3F FOREIGN KEY (dashboard_menu_id) REFERENCES dash_dashboardmenu (id)');
        $this->addSql('ALTER TABLE dash_dashboard ADD CONSTRAINT FK_DCEE68C4650760A9 FOREIGN KEY (search_id) REFERENCES find_search (id)');
        $this->addSql('ALTER TABLE dash_dashboard ADD CONSTRAINT FK_DCEE68C4D395B25E FOREIGN KEY (filter_id) REFERENCES fltrs_filters (id)');
        $this->addSql('ALTER TABLE dash_dashboard ADD CONSTRAINT FK_DCEE68C4DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE dash_dashboard ADD CONSTRAINT FK_DCEE68C4BAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE dash_dashboard_dash_dashboardproperties_c ADD CONSTRAINT FK_4B916157B9D04D2B FOREIGN KEY (dashboard_id) REFERENCES dash_dashboard (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dash_dashboard_dash_dashboardproperties_c ADD CONSTRAINT FK_4B916157EB330A3 FOREIGN KEY (dashboard_property_id) REFERENCES dash_dashboardproperties (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dash_dashboardmenu ADD CONSTRAINT FK_8D7699C7DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE dash_dashboardmenu ADD CONSTRAINT FK_8D7699C7BAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE dash_dashboardmenu_dash_dashboardmenuactiongroup_1_c ADD CONSTRAINT FK_52F8E72E7D333B3F FOREIGN KEY (dashboard_menu_id) REFERENCES dash_dashboardmenu (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dash_dashboardmenu_dash_dashboardmenuactiongroup_1_c ADD CONSTRAINT FK_52F8E72EA79437DF FOREIGN KEY (dashboard_menu_action_group_id) REFERENCES dash_dashboardmenuactiongroup (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dash_dashboardmenu_dash_menuactions_1_c ADD CONSTRAINT FK_622939B47D333B3F FOREIGN KEY (dashboard_menu_id) REFERENCES dash_dashboardmenu (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dash_dashboardmenu_dash_menuactions_1_c ADD CONSTRAINT FK_622939B4D743A99 FOREIGN KEY (dashboard_menu_action_id) REFERENCES dash_menuactions (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dash_menuactions ADD CONSTRAINT FK_32A4B1D650847265 FOREIGN KEY (flw_actions_id_c) REFERENCES flw_actions (id)');
        $this->addSql('ALTER TABLE dash_menuactions ADD CONSTRAINT FK_32A4B1D6DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE dash_menuactions ADD CONSTRAINT FK_32A4B1D6BAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE dash_dashboardmenuactiongroup ADD CONSTRAINT FK_B8AF3F54DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE dash_dashboardmenuactiongroup ADD CONSTRAINT FK_B8AF3F54BAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE dash_menuactiongroup_x_dash_menuactiongroup ADD CONSTRAINT FK_2B794B89727ACA70 FOREIGN KEY (parent_id) REFERENCES dash_dashboardmenuactiongroup (id)');
        $this->addSql('ALTER TABLE dash_menuactiongroup_x_dash_menuactiongroup ADD CONSTRAINT FK_2B794B89DD62C21B FOREIGN KEY (child_id) REFERENCES dash_dashboardmenuactiongroup (id)');
        $this->addSql('ALTER TABLE dash_dashboardmenuactiongroup_dash_menuactions ADD CONSTRAINT FK_54BE8525A79437DF FOREIGN KEY (dashboard_menu_action_group_id) REFERENCES dash_dashboardmenuactiongroup (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dash_dashboardmenuactiongroup_dash_menuactions ADD CONSTRAINT FK_54BE8525D743A99 FOREIGN KEY (dashboard_menu_action_id) REFERENCES dash_menuactions (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dash_dashboardproperties ADD CONSTRAINT FK_EB15E2C2DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE dash_dashboardproperties ADD CONSTRAINT FK_EB15E2C2BAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE list_external_object ADD CONSTRAINT FK_3F3469B0DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE list_external_object ADD CONSTRAINT FK_3F3469B0BAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE list_external_object_linkfields ADD CONSTRAINT FK_65642284776F93F FOREIGN KEY (external_object_id) REFERENCES list_external_object (id)');
        $this->addSql('ALTER TABLE list_external_object_linkfields ADD CONSTRAINT FK_65642284DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE list_external_object_linkfields ADD CONSTRAINT FK_65642284BAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE fltrs_filters ADD CONSTRAINT FK_904EB0D5DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE fltrs_filters ADD CONSTRAINT FK_904EB0D5BAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE fltrs_fields ADD CONSTRAINT FK_FFE688CFDE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE fltrs_fields ADD CONSTRAINT FK_FFE688CFBAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE fltrs_fieldsgroup ADD CONSTRAINT FK_1023F47BDE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE fltrs_fieldsgroup ADD CONSTRAINT FK_1023F47BBAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE fltrs_fieldsgroup_fltrs_fields_1_c ADD CONSTRAINT FK_4A6AE485B6665182 FOREIGN KEY (filter_field_group_id) REFERENCES fltrs_fieldsgroup (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fltrs_fieldsgroup_fltrs_fields_1_c ADD CONSTRAINT FK_4A6AE48579580210 FOREIGN KEY (filter_field_id) REFERENCES fltrs_fields (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fltrs_fieldsgroup_fltrs_filters_1_c ADD CONSTRAINT FK_1B855B10B6665182 FOREIGN KEY (filter_field_group_id) REFERENCES fltrs_fieldsgroup (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fltrs_fieldsgroup_fltrs_filters_1_c ADD CONSTRAINT FK_1B855B10D395B25E FOREIGN KEY (filter_id) REFERENCES fltrs_filters (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE find_search ADD CONSTRAINT FK_EEBBD500DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE find_search ADD CONSTRAINT FK_EEBBD500BAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE flw_flows ADD CONSTRAINT FK_1758840F9D32F035 FOREIGN KEY (action_id) REFERENCES flw_actions (id)');
        $this->addSql('ALTER TABLE flw_flows ADD CONSTRAINT FK_1758840FDE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE flw_flows ADD CONSTRAINT FK_1758840FBAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE flw_actions ADD CONSTRAINT FK_98DD688EDE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE flw_actions ADD CONSTRAINT FK_98DD688EBAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE flw_guidancefields ADD CONSTRAINT FK_69AB02108F1653AA FOREIGN KEY (flow_action_id) REFERENCES flw_actions (id)');
        $this->addSql('ALTER TABLE flw_guidancefields ADD CONSTRAINT FK_69AB0210DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE flw_guidancefields ADD CONSTRAINT FK_69AB0210BAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE flw_guidancefields_flw_guidancefieldvalidators_1_c ADD CONSTRAINT FK_1AF8E9C8D77DC8E9 FOREIGN KEY (flow_field_id) REFERENCES flw_guidancefields (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE flw_guidancefields_flw_guidancefieldvalidators_1_c ADD CONSTRAINT FK_1AF8E9C8B0644AEC FOREIGN KEY (validator_id) REFERENCES flw_guidancefieldvalidators (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE flw_guidancefields_flw_flowsteps_c ADD CONSTRAINT FK_BD603318D77DC8E9 FOREIGN KEY (flow_field_id) REFERENCES flw_guidancefields (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE flw_guidancefields_flw_flowsteps_c ADD CONSTRAINT FK_BD6033183082DA11 FOREIGN KEY (flow_step_id) REFERENCES flw_flowsteps (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE flw_flowsteps ADD CONSTRAINT FK_22D849FD4AAAEE12 FOREIGN KEY (grid_template_id) REFERENCES grid_gridtemplates (id)');
        $this->addSql('ALTER TABLE flw_flowsteps ADD CONSTRAINT FK_22D849FDDE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE flw_flowsteps ADD CONSTRAINT FK_22D849FDBAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE flw_flowsteps_flw_flowstepproperties_1_c ADD CONSTRAINT FK_1CE72E483082DA11 FOREIGN KEY (flow_step_id) REFERENCES flw_flowsteps (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE flw_flowsteps_flw_flowstepproperties_1_c ADD CONSTRAINT FK_1CE72E481D1C7A89 FOREIGN KEY (flow_step_property_id) REFERENCES flw_flowstepproperties (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE flw_flowstepslink ADD CONSTRAINT FK_A16AEF377EB60D1B FOREIGN KEY (flow_id) REFERENCES flw_flows (id)');
        $this->addSql('ALTER TABLE flw_flowstepslink ADD CONSTRAINT FK_A16AEF373082DA11 FOREIGN KEY (flow_step_id) REFERENCES flw_flowsteps (id)');
        $this->addSql('ALTER TABLE flw_flowstepslink ADD CONSTRAINT FK_A16AEF37DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE flw_flowstepslink ADD CONSTRAINT FK_A16AEF37BAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE flw_flowstepproperties ADD CONSTRAINT FK_1C1D6790DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE flw_flowstepproperties ADD CONSTRAINT FK_1C1D6790BAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE grid_panels ADD CONSTRAINT FK_ABF9B9F8DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE grid_panels ADD CONSTRAINT FK_ABF9B9F8BAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE grid_panels_flw_guidancefieldvalidators_1_c ADD CONSTRAINT FK_8215AEC6E0B613B0 FOREIGN KEY (grid_panel_id) REFERENCES grid_panels (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE grid_panels_flw_guidancefieldvalidators_1_c ADD CONSTRAINT FK_8215AEC6B0644AEC FOREIGN KEY (validator_id) REFERENCES flw_guidancefieldvalidators (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE grid_gridtemplates ADD CONSTRAINT FK_4DD42985DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE grid_gridtemplates ADD CONSTRAINT FK_4DD42985BAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE list_cell ADD CONSTRAINT FK_8E49580BDE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE list_cell ADD CONSTRAINT FK_8E49580BBAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE list_cells ADD CONSTRAINT FK_8C525FDB58BCF6E8 FOREIGN KEY (row_bar_id) REFERENCES list_row_bar (id)');
        $this->addSql('ALTER TABLE list_cells ADD CONSTRAINT FK_8C525FDB3DAE168B FOREIGN KEY (list_id) REFERENCES list_dynamic_list (id)');
        $this->addSql('ALTER TABLE list_cells ADD CONSTRAINT FK_8C525FDBCB39D93A FOREIGN KEY (cell_id) REFERENCES list_cell (id)');
        $this->addSql('ALTER TABLE list_cells ADD CONSTRAINT FK_8C525FDBDE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE list_cells ADD CONSTRAINT FK_8C525FDBBAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE list_dynamic_list ADD CONSTRAINT FK_2F5207E7D395B25E FOREIGN KEY (filter_id) REFERENCES fltrs_filters (id)');
        $this->addSql('ALTER TABLE list_dynamic_list ADD CONSTRAINT FK_2F5207E75F678FB6 FOREIGN KEY (top_bar_id) REFERENCES list_topbar (id)');
        $this->addSql('ALTER TABLE list_dynamic_list ADD CONSTRAINT FK_2F5207E7776F93F FOREIGN KEY (external_object_id) REFERENCES list_external_object (id)');
        $this->addSql('ALTER TABLE list_dynamic_list ADD CONSTRAINT FK_2F5207E7DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE list_dynamic_list ADD CONSTRAINT FK_2F5207E7BAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE list_row_action ADD CONSTRAINT FK_CDC72B9C8F1653AA FOREIGN KEY (flow_action_id) REFERENCES flw_actions (id)');
        $this->addSql('ALTER TABLE list_row_action ADD CONSTRAINT FK_CDC72B9C58BCF6E8 FOREIGN KEY (row_bar_id) REFERENCES list_row_bar (id)');
        $this->addSql('ALTER TABLE list_row_action ADD CONSTRAINT FK_CDC72B9CDE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE list_row_action ADD CONSTRAINT FK_CDC72B9CBAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE list_row_bar ADD CONSTRAINT FK_9254086DDE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE list_row_bar ADD CONSTRAINT FK_9254086DBAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE list_sorting_options ADD CONSTRAINT FK_19AF129CDE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE list_sorting_options ADD CONSTRAINT FK_19AF129CBAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE list_top_action ADD CONSTRAINT FK_CA1C52C250847265 FOREIGN KEY (flw_actions_id_c) REFERENCES flw_actions (id)');
        $this->addSql('ALTER TABLE list_top_action ADD CONSTRAINT FK_CA1C52C2DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE list_top_action ADD CONSTRAINT FK_CA1C52C2BAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE list_topbar ADD CONSTRAINT FK_414239E2DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE list_topbar ADD CONSTRAINT FK_414239E2BAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE list_topbar_list_sorting_options_c ADD CONSTRAINT FK_A9A698854AFB6A FOREIGN KEY (list_top_bar_id) REFERENCES list_topbar (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE list_topbar_list_sorting_options_c ADD CONSTRAINT FK_A9A698A0237525 FOREIGN KEY (list_sorting_option_id) REFERENCES list_sorting_options (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE list_topbar_list_top_action_c ADD CONSTRAINT FK_D6A3836A854AFB6A FOREIGN KEY (list_top_bar_id) REFERENCES list_topbar (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE list_topbar_list_top_action_c ADD CONSTRAINT FK_D6A3836A68E6E44D FOREIGN KEY (list_top_action_id) REFERENCES list_top_action (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE menu_mainmenu ADD CONSTRAINT FK_7AE41AF5DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE menu_mainmenu ADD CONSTRAINT FK_7AE41AF5BAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE menu_mainmenu_dash_dashboard_c ADD CONSTRAINT FK_6BE0E5BCCCD7E912 FOREIGN KEY (menu_id) REFERENCES menu_mainmenu (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE menu_mainmenu_dash_dashboard_c ADD CONSTRAINT FK_6BE0E5BCB9D04D2B FOREIGN KEY (dashboard_id) REFERENCES dash_dashboard (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE securitygroups ADD CONSTRAINT FK_96E93646ADF66B1A FOREIGN KEY (assigned_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE securitygroups ADD CONSTRAINT FK_96E93646DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE securitygroups ADD CONSTRAINT FK_96E93646BAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE securitygroups_acl_roles ADD CONSTRAINT FK_A13371969D3F5E95 FOREIGN KEY (security_group_id) REFERENCES securitygroups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE securitygroups_acl_roles ADD CONSTRAINT FK_A1337196BD33296F FOREIGN KEY (acl_role_id) REFERENCES acl_roles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE securitygroups_api ADD CONSTRAINT FK_E82721E2DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE securitygroups_api ADD CONSTRAINT FK_E82721E2BAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE securitygroups_records ADD CONSTRAINT FK_A3997AC3E7F73327 FOREIGN KEY (securitygroup_id) REFERENCES securitygroups (id)');
        $this->addSql('ALTER TABLE securitygroups_users ADD CONSTRAINT FK_3B3227D2E7F73327 FOREIGN KEY (securitygroup_id) REFERENCES securitygroups (id)');
        $this->addSql('ALTER TABLE securitygroups_users ADD CONSTRAINT FK_3B3227D2A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE fe_selectwithsearch ADD CONSTRAINT FK_F10C641ADE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE fe_selectwithsearch ADD CONSTRAINT FK_F10C641ABAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE trans_translation ADD CONSTRAINT FK_AC29EB6DDE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE trans_translation ADD CONSTRAINT FK_AC29EB6DBAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE user_guidance_recovery ADD CONSTRAINT FK_B1F11CA5BF396750 FOREIGN KEY (id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE user_login ADD CONSTRAINT FK_48CA3048BF396750 FOREIGN KEY (id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE flw_guidancefieldvalidators ADD CONSTRAINT FK_1204F950DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE flw_guidancefieldvalidators ADD CONSTRAINT FK_1204F950BAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE flw_guidancefieldsvalidators_conditions ADD CONSTRAINT FK_7DE190B1727ACA70 FOREIGN KEY (parent_id) REFERENCES flw_guidancefieldvalidators (id)');
        $this->addSql('ALTER TABLE flw_guidancefieldsvalidators_conditions ADD CONSTRAINT FK_7DE190B1DD62C21B FOREIGN KEY (child_id) REFERENCES flw_guidancefieldvalidators (id)');
        $this->addSql('CREATE TRIGGER acl_actions_audit_insert
          AFTER INSERT ON acl_actions
          FOR EACH ROW BEGIN
          INSERT INTO acl_actions_aud
            SELECT now(6), \'INSERT\', acl_actions.* 
            FROM acl_actions 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER acl_actions_audit_update
          AFTER UPDATE ON acl_actions
          FOR EACH ROW BEGIN
          INSERT INTO acl_actions_aud
            SELECT now(6), \'UPDATE\', acl_actions.* 
            FROM acl_actions 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER acl_actions_audit_delete
          BEFORE DELETE ON acl_actions
          FOR EACH ROW BEGIN
          INSERT INTO acl_actions_aud
            SELECT now(6), \'DELETE\', acl_actions.* 
            FROM acl_actions 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER acl_roles_audit_insert
          AFTER INSERT ON acl_roles
          FOR EACH ROW BEGIN
          INSERT INTO acl_roles_aud
            SELECT now(6), \'INSERT\', acl_roles.* 
            FROM acl_roles 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER acl_roles_audit_update
          AFTER UPDATE ON acl_roles
          FOR EACH ROW BEGIN
          INSERT INTO acl_roles_aud
            SELECT now(6), \'UPDATE\', acl_roles.* 
            FROM acl_roles 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER acl_roles_audit_delete
          BEFORE DELETE ON acl_roles
          FOR EACH ROW BEGIN
          INSERT INTO acl_roles_aud
            SELECT now(6), \'DELETE\', acl_roles.* 
            FROM acl_roles 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER acl_roles_users_audit_insert
          AFTER INSERT ON acl_roles_users
          FOR EACH ROW BEGIN
          INSERT INTO acl_roles_users_aud
            SELECT now(6), \'INSERT\', acl_roles_users.* 
            FROM acl_roles_users 
            WHERE acl_role_id = NEW.acl_role_id AND user_id = NEW.user_id;
        END;');
        $this->addSql('CREATE TRIGGER acl_roles_users_audit_update
          AFTER UPDATE ON acl_roles_users
          FOR EACH ROW BEGIN
          INSERT INTO acl_roles_users_aud
            SELECT now(6), \'UPDATE\', acl_roles_users.* 
            FROM acl_roles_users 
            WHERE acl_role_id = NEW.acl_role_id AND user_id = NEW.user_id;
        END;');
        $this->addSql('CREATE TRIGGER acl_roles_users_audit_delete
          BEFORE DELETE ON acl_roles_users
          FOR EACH ROW BEGIN
          INSERT INTO acl_roles_users_aud
            SELECT now(6), \'DELETE\', acl_roles_users.* 
            FROM acl_roles_users 
            WHERE acl_role_id = OLD.acl_role_id AND user_id = OLD.user_id;
        END;');
        $this->addSql('CREATE TRIGGER acl_roles_actions_audit_insert
          AFTER INSERT ON acl_roles_actions
          FOR EACH ROW BEGIN
          INSERT INTO acl_roles_actions_aud
            SELECT now(6), \'INSERT\', acl_roles_actions.* 
            FROM acl_roles_actions 
            WHERE acl_role_id = NEW.acl_role_id AND acl_action_id = NEW.acl_action_id;
        END;');
        $this->addSql('CREATE TRIGGER acl_roles_actions_audit_update
          AFTER UPDATE ON acl_roles_actions
          FOR EACH ROW BEGIN
          INSERT INTO acl_roles_actions_aud
            SELECT now(6), \'UPDATE\', acl_roles_actions.* 
            FROM acl_roles_actions 
            WHERE acl_role_id = NEW.acl_role_id AND acl_action_id = NEW.acl_action_id;
        END;');
        $this->addSql('CREATE TRIGGER acl_roles_actions_audit_delete
          BEFORE DELETE ON acl_roles_actions
          FOR EACH ROW BEGIN
          INSERT INTO acl_roles_actions_aud
            SELECT now(6), \'DELETE\', acl_roles_actions.* 
            FROM acl_roles_actions 
            WHERE acl_role_id = OLD.acl_role_id AND acl_action_id = OLD.acl_action_id;
        END;');
        $this->addSql('CREATE TRIGGER conditionalmessage_audit_insert
          AFTER INSERT ON conditionalmessage
          FOR EACH ROW BEGIN
          INSERT INTO conditionalmessage_aud
            SELECT now(6), \'INSERT\', conditionalmessage.* 
            FROM conditionalmessage 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER conditionalmessage_audit_update
          AFTER UPDATE ON conditionalmessage
          FOR EACH ROW BEGIN
          INSERT INTO conditionalmessage_aud
            SELECT now(6), \'UPDATE\', conditionalmessage.* 
            FROM conditionalmessage 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER conditionalmessage_audit_delete
          BEFORE DELETE ON conditionalmessage
          FOR EACH ROW BEGIN
          INSERT INTO conditionalmessage_aud
            SELECT now(6), \'DELETE\', conditionalmessage.* 
            FROM conditionalmessage 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER conditional_message_validators_audit_insert
          AFTER INSERT ON conditional_message_validators
          FOR EACH ROW BEGIN
          INSERT INTO conditional_message_validators_aud
            SELECT now(6), \'INSERT\', conditional_message_validators.* 
            FROM conditional_message_validators 
            WHERE conditional_message_id = NEW.conditional_message_id AND validator_id = NEW.validator_id;
        END;');
        $this->addSql('CREATE TRIGGER conditional_message_validators_audit_update
          AFTER UPDATE ON conditional_message_validators
          FOR EACH ROW BEGIN
          INSERT INTO conditional_message_validators_aud
            SELECT now(6), \'UPDATE\', conditional_message_validators.* 
            FROM conditional_message_validators 
            WHERE conditional_message_id = NEW.conditional_message_id AND validator_id = NEW.validator_id;
        END;');
        $this->addSql('CREATE TRIGGER conditional_message_validators_audit_delete
          BEFORE DELETE ON conditional_message_validators
          FOR EACH ROW BEGIN
          INSERT INTO conditional_message_validators_aud
            SELECT now(6), \'DELETE\', conditional_message_validators.* 
            FROM conditional_message_validators 
            WHERE conditional_message_id = OLD.conditional_message_id AND validator_id = OLD.validator_id;
        END;');
        $this->addSql('CREATE TRIGGER conf_defaults_audit_insert
          AFTER INSERT ON conf_defaults
          FOR EACH ROW BEGIN
          INSERT INTO conf_defaults_aud
            SELECT now(6), \'INSERT\', conf_defaults.* 
            FROM conf_defaults 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER conf_defaults_audit_update
          AFTER UPDATE ON conf_defaults
          FOR EACH ROW BEGIN
          INSERT INTO conf_defaults_aud
            SELECT now(6), \'UPDATE\', conf_defaults.* 
            FROM conf_defaults 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER conf_defaults_audit_delete
          BEFORE DELETE ON conf_defaults
          FOR EACH ROW BEGIN
          INSERT INTO conf_defaults_aud
            SELECT now(6), \'DELETE\', conf_defaults.* 
            FROM conf_defaults 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER dash_dashboard_audit_insert
          AFTER INSERT ON dash_dashboard
          FOR EACH ROW BEGIN
          INSERT INTO dash_dashboard_aud
            SELECT now(6), \'INSERT\', dash_dashboard.* 
            FROM dash_dashboard 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER dash_dashboard_audit_update
          AFTER UPDATE ON dash_dashboard
          FOR EACH ROW BEGIN
          INSERT INTO dash_dashboard_aud
            SELECT now(6), \'UPDATE\', dash_dashboard.* 
            FROM dash_dashboard 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER dash_dashboard_audit_delete
          BEFORE DELETE ON dash_dashboard
          FOR EACH ROW BEGIN
          INSERT INTO dash_dashboard_aud
            SELECT now(6), \'DELETE\', dash_dashboard.* 
            FROM dash_dashboard 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER dash_dashboard_dash_dashboardproperties_c_audit_insert
          AFTER INSERT ON dash_dashboard_dash_dashboardproperties_c
          FOR EACH ROW BEGIN
          INSERT INTO dash_dashboard_dash_dashboardproperties_c_aud
            SELECT now(6), \'INSERT\', dash_dashboard_dash_dashboardproperties_c.* 
            FROM dash_dashboard_dash_dashboardproperties_c 
            WHERE dashboard_id = NEW.dashboard_id AND dashboard_property_id = NEW.dashboard_property_id;
        END;');
        $this->addSql('CREATE TRIGGER dash_dashboard_dash_dashboardproperties_c_audit_update
          AFTER UPDATE ON dash_dashboard_dash_dashboardproperties_c
          FOR EACH ROW BEGIN
          INSERT INTO dash_dashboard_dash_dashboardproperties_c_aud
            SELECT now(6), \'UPDATE\', dash_dashboard_dash_dashboardproperties_c.* 
            FROM dash_dashboard_dash_dashboardproperties_c 
            WHERE dashboard_id = NEW.dashboard_id AND dashboard_property_id = NEW.dashboard_property_id;
        END;');
        $this->addSql('CREATE TRIGGER dash_dashboard_dash_dashboardproperties_c_audit_delete
          BEFORE DELETE ON dash_dashboard_dash_dashboardproperties_c
          FOR EACH ROW BEGIN
          INSERT INTO dash_dashboard_dash_dashboardproperties_c_aud
            SELECT now(6), \'DELETE\', dash_dashboard_dash_dashboardproperties_c.* 
            FROM dash_dashboard_dash_dashboardproperties_c 
            WHERE dashboard_id = OLD.dashboard_id AND dashboard_property_id = OLD.dashboard_property_id;
        END;');
        $this->addSql('CREATE TRIGGER dash_dashboardmenu_audit_insert
          AFTER INSERT ON dash_dashboardmenu
          FOR EACH ROW BEGIN
          INSERT INTO dash_dashboardmenu_aud
            SELECT now(6), \'INSERT\', dash_dashboardmenu.* 
            FROM dash_dashboardmenu 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER dash_dashboardmenu_audit_update
          AFTER UPDATE ON dash_dashboardmenu
          FOR EACH ROW BEGIN
          INSERT INTO dash_dashboardmenu_aud
            SELECT now(6), \'UPDATE\', dash_dashboardmenu.* 
            FROM dash_dashboardmenu 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER dash_dashboardmenu_audit_delete
          BEFORE DELETE ON dash_dashboardmenu
          FOR EACH ROW BEGIN
          INSERT INTO dash_dashboardmenu_aud
            SELECT now(6), \'DELETE\', dash_dashboardmenu.* 
            FROM dash_dashboardmenu 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER dash_dashboardmenu_dash_dashboardmenuactio_8941b64a_audit_insert
          AFTER INSERT ON dash_dashboardmenu_dash_dashboardmenuactiongroup_1_c
          FOR EACH ROW BEGIN
          INSERT INTO dash_dashboardmenu_dash_dashboardmenuactiongroup_1_c_aud
            SELECT now(6), \'INSERT\', dash_dashboardmenu_dash_dashboardmenuactiongroup_1_c.* 
            FROM dash_dashboardmenu_dash_dashboardmenuactiongroup_1_c 
            WHERE dashboard_menu_id = NEW.dashboard_menu_id AND dashboard_menu_action_group_id = NEW.dashboard_menu_action_group_id;
        END;');
        $this->addSql('CREATE TRIGGER dash_dashboardmenu_dash_dashboardmenuactio_d8888146_audit_update
          AFTER UPDATE ON dash_dashboardmenu_dash_dashboardmenuactiongroup_1_c
          FOR EACH ROW BEGIN
          INSERT INTO dash_dashboardmenu_dash_dashboardmenuactiongroup_1_c_aud
            SELECT now(6), \'UPDATE\', dash_dashboardmenu_dash_dashboardmenuactiongroup_1_c.* 
            FROM dash_dashboardmenu_dash_dashboardmenuactiongroup_1_c 
            WHERE dashboard_menu_id = NEW.dashboard_menu_id AND dashboard_menu_action_group_id = NEW.dashboard_menu_action_group_id;
        END;');
        $this->addSql('CREATE TRIGGER dash_dashboardmenu_dash_dashboardmenuactio_0b47e8bd_audit_delete
          BEFORE DELETE ON dash_dashboardmenu_dash_dashboardmenuactiongroup_1_c
          FOR EACH ROW BEGIN
          INSERT INTO dash_dashboardmenu_dash_dashboardmenuactiongroup_1_c_aud
            SELECT now(6), \'DELETE\', dash_dashboardmenu_dash_dashboardmenuactiongroup_1_c.* 
            FROM dash_dashboardmenu_dash_dashboardmenuactiongroup_1_c 
            WHERE dashboard_menu_id = OLD.dashboard_menu_id AND dashboard_menu_action_group_id = OLD.dashboard_menu_action_group_id;
        END;');
        $this->addSql('CREATE TRIGGER dash_dashboardmenu_dash_menuactions_1_c_audit_insert
          AFTER INSERT ON dash_dashboardmenu_dash_menuactions_1_c
          FOR EACH ROW BEGIN
          INSERT INTO dash_dashboardmenu_dash_menuactions_1_c_aud
            SELECT now(6), \'INSERT\', dash_dashboardmenu_dash_menuactions_1_c.* 
            FROM dash_dashboardmenu_dash_menuactions_1_c 
            WHERE dashboard_menu_id = NEW.dashboard_menu_id AND dashboard_menu_action_id = NEW.dashboard_menu_action_id;
        END;');
        $this->addSql('CREATE TRIGGER dash_dashboardmenu_dash_menuactions_1_c_audit_update
          AFTER UPDATE ON dash_dashboardmenu_dash_menuactions_1_c
          FOR EACH ROW BEGIN
          INSERT INTO dash_dashboardmenu_dash_menuactions_1_c_aud
            SELECT now(6), \'UPDATE\', dash_dashboardmenu_dash_menuactions_1_c.* 
            FROM dash_dashboardmenu_dash_menuactions_1_c 
            WHERE dashboard_menu_id = NEW.dashboard_menu_id AND dashboard_menu_action_id = NEW.dashboard_menu_action_id;
        END;');
        $this->addSql('CREATE TRIGGER dash_dashboardmenu_dash_menuactions_1_c_audit_delete
          BEFORE DELETE ON dash_dashboardmenu_dash_menuactions_1_c
          FOR EACH ROW BEGIN
          INSERT INTO dash_dashboardmenu_dash_menuactions_1_c_aud
            SELECT now(6), \'DELETE\', dash_dashboardmenu_dash_menuactions_1_c.* 
            FROM dash_dashboardmenu_dash_menuactions_1_c 
            WHERE dashboard_menu_id = OLD.dashboard_menu_id AND dashboard_menu_action_id = OLD.dashboard_menu_action_id;
        END;');
        $this->addSql('CREATE TRIGGER dash_menuactions_audit_insert
          AFTER INSERT ON dash_menuactions
          FOR EACH ROW BEGIN
          INSERT INTO dash_menuactions_aud
            SELECT now(6), \'INSERT\', dash_menuactions.* 
            FROM dash_menuactions 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER dash_menuactions_audit_update
          AFTER UPDATE ON dash_menuactions
          FOR EACH ROW BEGIN
          INSERT INTO dash_menuactions_aud
            SELECT now(6), \'UPDATE\', dash_menuactions.* 
            FROM dash_menuactions 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER dash_menuactions_audit_delete
          BEFORE DELETE ON dash_menuactions
          FOR EACH ROW BEGIN
          INSERT INTO dash_menuactions_aud
            SELECT now(6), \'DELETE\', dash_menuactions.* 
            FROM dash_menuactions 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER dash_dashboardmenuactiongroup_audit_insert
          AFTER INSERT ON dash_dashboardmenuactiongroup
          FOR EACH ROW BEGIN
          INSERT INTO dash_dashboardmenuactiongroup_aud
            SELECT now(6), \'INSERT\', dash_dashboardmenuactiongroup.* 
            FROM dash_dashboardmenuactiongroup 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER dash_dashboardmenuactiongroup_audit_update
          AFTER UPDATE ON dash_dashboardmenuactiongroup
          FOR EACH ROW BEGIN
          INSERT INTO dash_dashboardmenuactiongroup_aud
            SELECT now(6), \'UPDATE\', dash_dashboardmenuactiongroup.* 
            FROM dash_dashboardmenuactiongroup 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER dash_dashboardmenuactiongroup_audit_delete
          BEFORE DELETE ON dash_dashboardmenuactiongroup
          FOR EACH ROW BEGIN
          INSERT INTO dash_dashboardmenuactiongroup_aud
            SELECT now(6), \'DELETE\', dash_dashboardmenuactiongroup.* 
            FROM dash_dashboardmenuactiongroup 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER dash_menuactiongroup_x_dash_menuactiongroup_audit_insert
          AFTER INSERT ON dash_menuactiongroup_x_dash_menuactiongroup
          FOR EACH ROW BEGIN
          INSERT INTO dash_menuactiongroup_x_dash_menuactiongroup_aud
            SELECT now(6), \'INSERT\', dash_menuactiongroup_x_dash_menuactiongroup.* 
            FROM dash_menuactiongroup_x_dash_menuactiongroup 
            WHERE parent_id = NEW.parent_id AND child_id = NEW.child_id;
        END;');
        $this->addSql('CREATE TRIGGER dash_menuactiongroup_x_dash_menuactiongroup_audit_update
          AFTER UPDATE ON dash_menuactiongroup_x_dash_menuactiongroup
          FOR EACH ROW BEGIN
          INSERT INTO dash_menuactiongroup_x_dash_menuactiongroup_aud
            SELECT now(6), \'UPDATE\', dash_menuactiongroup_x_dash_menuactiongroup.* 
            FROM dash_menuactiongroup_x_dash_menuactiongroup 
            WHERE parent_id = NEW.parent_id AND child_id = NEW.child_id;
        END;');
        $this->addSql('CREATE TRIGGER dash_menuactiongroup_x_dash_menuactiongroup_audit_delete
          BEFORE DELETE ON dash_menuactiongroup_x_dash_menuactiongroup
          FOR EACH ROW BEGIN
          INSERT INTO dash_menuactiongroup_x_dash_menuactiongroup_aud
            SELECT now(6), \'DELETE\', dash_menuactiongroup_x_dash_menuactiongroup.* 
            FROM dash_menuactiongroup_x_dash_menuactiongroup 
            WHERE parent_id = OLD.parent_id AND child_id = OLD.child_id;
        END;');
        $this->addSql('CREATE TRIGGER dash_dashboardmenuactiongroup_dash_menuactions_audit_insert
          AFTER INSERT ON dash_dashboardmenuactiongroup_dash_menuactions
          FOR EACH ROW BEGIN
          INSERT INTO dash_dashboardmenuactiongroup_dash_menuactions_aud
            SELECT now(6), \'INSERT\', dash_dashboardmenuactiongroup_dash_menuactions.* 
            FROM dash_dashboardmenuactiongroup_dash_menuactions 
            WHERE dashboard_menu_action_group_id = NEW.dashboard_menu_action_group_id AND dashboard_menu_action_id = NEW.dashboard_menu_action_id;
        END;');
        $this->addSql('CREATE TRIGGER dash_dashboardmenuactiongroup_dash_menuactions_audit_update
          AFTER UPDATE ON dash_dashboardmenuactiongroup_dash_menuactions
          FOR EACH ROW BEGIN
          INSERT INTO dash_dashboardmenuactiongroup_dash_menuactions_aud
            SELECT now(6), \'UPDATE\', dash_dashboardmenuactiongroup_dash_menuactions.* 
            FROM dash_dashboardmenuactiongroup_dash_menuactions 
            WHERE dashboard_menu_action_group_id = NEW.dashboard_menu_action_group_id AND dashboard_menu_action_id = NEW.dashboard_menu_action_id;
        END;');
        $this->addSql('CREATE TRIGGER dash_dashboardmenuactiongroup_dash_menuactions_audit_delete
          BEFORE DELETE ON dash_dashboardmenuactiongroup_dash_menuactions
          FOR EACH ROW BEGIN
          INSERT INTO dash_dashboardmenuactiongroup_dash_menuactions_aud
            SELECT now(6), \'DELETE\', dash_dashboardmenuactiongroup_dash_menuactions.* 
            FROM dash_dashboardmenuactiongroup_dash_menuactions 
            WHERE dashboard_menu_action_group_id = OLD.dashboard_menu_action_group_id AND dashboard_menu_action_id = OLD.dashboard_menu_action_id;
        END;');
        $this->addSql('CREATE TRIGGER dash_dashboardproperties_audit_insert
          AFTER INSERT ON dash_dashboardproperties
          FOR EACH ROW BEGIN
          INSERT INTO dash_dashboardproperties_aud
            SELECT now(6), \'INSERT\', dash_dashboardproperties.* 
            FROM dash_dashboardproperties 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER dash_dashboardproperties_audit_update
          AFTER UPDATE ON dash_dashboardproperties
          FOR EACH ROW BEGIN
          INSERT INTO dash_dashboardproperties_aud
            SELECT now(6), \'UPDATE\', dash_dashboardproperties.* 
            FROM dash_dashboardproperties 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER dash_dashboardproperties_audit_delete
          BEFORE DELETE ON dash_dashboardproperties
          FOR EACH ROW BEGIN
          INSERT INTO dash_dashboardproperties_aud
            SELECT now(6), \'DELETE\', dash_dashboardproperties.* 
            FROM dash_dashboardproperties 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER list_external_object_audit_insert
          AFTER INSERT ON list_external_object
          FOR EACH ROW BEGIN
          INSERT INTO list_external_object_aud
            SELECT now(6), \'INSERT\', list_external_object.* 
            FROM list_external_object 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER list_external_object_audit_update
          AFTER UPDATE ON list_external_object
          FOR EACH ROW BEGIN
          INSERT INTO list_external_object_aud
            SELECT now(6), \'UPDATE\', list_external_object.* 
            FROM list_external_object 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER list_external_object_audit_delete
          BEFORE DELETE ON list_external_object
          FOR EACH ROW BEGIN
          INSERT INTO list_external_object_aud
            SELECT now(6), \'DELETE\', list_external_object.* 
            FROM list_external_object 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER list_external_object_linkfields_audit_insert
          AFTER INSERT ON list_external_object_linkfields
          FOR EACH ROW BEGIN
          INSERT INTO list_external_object_linkfields_aud
            SELECT now(6), \'INSERT\', list_external_object_linkfields.* 
            FROM list_external_object_linkfields 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER list_external_object_linkfields_audit_update
          AFTER UPDATE ON list_external_object_linkfields
          FOR EACH ROW BEGIN
          INSERT INTO list_external_object_linkfields_aud
            SELECT now(6), \'UPDATE\', list_external_object_linkfields.* 
            FROM list_external_object_linkfields 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER list_external_object_linkfields_audit_delete
          BEFORE DELETE ON list_external_object_linkfields
          FOR EACH ROW BEGIN
          INSERT INTO list_external_object_linkfields_aud
            SELECT now(6), \'DELETE\', list_external_object_linkfields.* 
            FROM list_external_object_linkfields 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER fltrs_filters_audit_insert
          AFTER INSERT ON fltrs_filters
          FOR EACH ROW BEGIN
          INSERT INTO fltrs_filters_aud
            SELECT now(6), \'INSERT\', fltrs_filters.* 
            FROM fltrs_filters 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER fltrs_filters_audit_update
          AFTER UPDATE ON fltrs_filters
          FOR EACH ROW BEGIN
          INSERT INTO fltrs_filters_aud
            SELECT now(6), \'UPDATE\', fltrs_filters.* 
            FROM fltrs_filters 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER fltrs_filters_audit_delete
          BEFORE DELETE ON fltrs_filters
          FOR EACH ROW BEGIN
          INSERT INTO fltrs_filters_aud
            SELECT now(6), \'DELETE\', fltrs_filters.* 
            FROM fltrs_filters 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER fltrs_fields_audit_insert
          AFTER INSERT ON fltrs_fields
          FOR EACH ROW BEGIN
          INSERT INTO fltrs_fields_aud
            SELECT now(6), \'INSERT\', fltrs_fields.* 
            FROM fltrs_fields 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER fltrs_fields_audit_update
          AFTER UPDATE ON fltrs_fields
          FOR EACH ROW BEGIN
          INSERT INTO fltrs_fields_aud
            SELECT now(6), \'UPDATE\', fltrs_fields.* 
            FROM fltrs_fields 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER fltrs_fields_audit_delete
          BEFORE DELETE ON fltrs_fields
          FOR EACH ROW BEGIN
          INSERT INTO fltrs_fields_aud
            SELECT now(6), \'DELETE\', fltrs_fields.* 
            FROM fltrs_fields 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER fltrs_fieldsgroup_audit_insert
          AFTER INSERT ON fltrs_fieldsgroup
          FOR EACH ROW BEGIN
          INSERT INTO fltrs_fieldsgroup_aud
            SELECT now(6), \'INSERT\', fltrs_fieldsgroup.* 
            FROM fltrs_fieldsgroup 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER fltrs_fieldsgroup_audit_update
          AFTER UPDATE ON fltrs_fieldsgroup
          FOR EACH ROW BEGIN
          INSERT INTO fltrs_fieldsgroup_aud
            SELECT now(6), \'UPDATE\', fltrs_fieldsgroup.* 
            FROM fltrs_fieldsgroup 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER fltrs_fieldsgroup_audit_delete
          BEFORE DELETE ON fltrs_fieldsgroup
          FOR EACH ROW BEGIN
          INSERT INTO fltrs_fieldsgroup_aud
            SELECT now(6), \'DELETE\', fltrs_fieldsgroup.* 
            FROM fltrs_fieldsgroup 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER fltrs_fieldsgroup_fltrs_fields_1_c_audit_insert
          AFTER INSERT ON fltrs_fieldsgroup_fltrs_fields_1_c
          FOR EACH ROW BEGIN
          INSERT INTO fltrs_fieldsgroup_fltrs_fields_1_c_aud
            SELECT now(6), \'INSERT\', fltrs_fieldsgroup_fltrs_fields_1_c.* 
            FROM fltrs_fieldsgroup_fltrs_fields_1_c 
            WHERE filter_field_group_id = NEW.filter_field_group_id AND filter_field_id = NEW.filter_field_id;
        END;');
        $this->addSql('CREATE TRIGGER fltrs_fieldsgroup_fltrs_fields_1_c_audit_update
          AFTER UPDATE ON fltrs_fieldsgroup_fltrs_fields_1_c
          FOR EACH ROW BEGIN
          INSERT INTO fltrs_fieldsgroup_fltrs_fields_1_c_aud
            SELECT now(6), \'UPDATE\', fltrs_fieldsgroup_fltrs_fields_1_c.* 
            FROM fltrs_fieldsgroup_fltrs_fields_1_c 
            WHERE filter_field_group_id = NEW.filter_field_group_id AND filter_field_id = NEW.filter_field_id;
        END;');
        $this->addSql('CREATE TRIGGER fltrs_fieldsgroup_fltrs_fields_1_c_audit_delete
          BEFORE DELETE ON fltrs_fieldsgroup_fltrs_fields_1_c
          FOR EACH ROW BEGIN
          INSERT INTO fltrs_fieldsgroup_fltrs_fields_1_c_aud
            SELECT now(6), \'DELETE\', fltrs_fieldsgroup_fltrs_fields_1_c.* 
            FROM fltrs_fieldsgroup_fltrs_fields_1_c 
            WHERE filter_field_group_id = OLD.filter_field_group_id AND filter_field_id = OLD.filter_field_id;
        END;');
        $this->addSql('CREATE TRIGGER fltrs_fieldsgroup_fltrs_filters_1_c_audit_insert
          AFTER INSERT ON fltrs_fieldsgroup_fltrs_filters_1_c
          FOR EACH ROW BEGIN
          INSERT INTO fltrs_fieldsgroup_fltrs_filters_1_c_aud
            SELECT now(6), \'INSERT\', fltrs_fieldsgroup_fltrs_filters_1_c.* 
            FROM fltrs_fieldsgroup_fltrs_filters_1_c 
            WHERE filter_field_group_id = NEW.filter_field_group_id AND filter_id = NEW.filter_id;
        END;');
        $this->addSql('CREATE TRIGGER fltrs_fieldsgroup_fltrs_filters_1_c_audit_update
          AFTER UPDATE ON fltrs_fieldsgroup_fltrs_filters_1_c
          FOR EACH ROW BEGIN
          INSERT INTO fltrs_fieldsgroup_fltrs_filters_1_c_aud
            SELECT now(6), \'UPDATE\', fltrs_fieldsgroup_fltrs_filters_1_c.* 
            FROM fltrs_fieldsgroup_fltrs_filters_1_c 
            WHERE filter_field_group_id = NEW.filter_field_group_id AND filter_id = NEW.filter_id;
        END;');
        $this->addSql('CREATE TRIGGER fltrs_fieldsgroup_fltrs_filters_1_c_audit_delete
          BEFORE DELETE ON fltrs_fieldsgroup_fltrs_filters_1_c
          FOR EACH ROW BEGIN
          INSERT INTO fltrs_fieldsgroup_fltrs_filters_1_c_aud
            SELECT now(6), \'DELETE\', fltrs_fieldsgroup_fltrs_filters_1_c.* 
            FROM fltrs_fieldsgroup_fltrs_filters_1_c 
            WHERE filter_field_group_id = OLD.filter_field_group_id AND filter_id = OLD.filter_id;
        END;');
        $this->addSql('CREATE TRIGGER find_search_audit_insert
          AFTER INSERT ON find_search
          FOR EACH ROW BEGIN
          INSERT INTO find_search_aud
            SELECT now(6), \'INSERT\', find_search.* 
            FROM find_search 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER find_search_audit_update
          AFTER UPDATE ON find_search
          FOR EACH ROW BEGIN
          INSERT INTO find_search_aud
            SELECT now(6), \'UPDATE\', find_search.* 
            FROM find_search 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER find_search_audit_delete
          BEFORE DELETE ON find_search
          FOR EACH ROW BEGIN
          INSERT INTO find_search_aud
            SELECT now(6), \'DELETE\', find_search.* 
            FROM find_search 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER flw_flows_audit_insert
          AFTER INSERT ON flw_flows
          FOR EACH ROW BEGIN
          INSERT INTO flw_flows_aud
            SELECT now(6), \'INSERT\', flw_flows.* 
            FROM flw_flows 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER flw_flows_audit_update
          AFTER UPDATE ON flw_flows
          FOR EACH ROW BEGIN
          INSERT INTO flw_flows_aud
            SELECT now(6), \'UPDATE\', flw_flows.* 
            FROM flw_flows 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER flw_flows_audit_delete
          BEFORE DELETE ON flw_flows
          FOR EACH ROW BEGIN
          INSERT INTO flw_flows_aud
            SELECT now(6), \'DELETE\', flw_flows.* 
            FROM flw_flows 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER flw_actions_audit_insert
          AFTER INSERT ON flw_actions
          FOR EACH ROW BEGIN
          INSERT INTO flw_actions_aud
            SELECT now(6), \'INSERT\', flw_actions.* 
            FROM flw_actions 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER flw_actions_audit_update
          AFTER UPDATE ON flw_actions
          FOR EACH ROW BEGIN
          INSERT INTO flw_actions_aud
            SELECT now(6), \'UPDATE\', flw_actions.* 
            FROM flw_actions 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER flw_actions_audit_delete
          BEFORE DELETE ON flw_actions
          FOR EACH ROW BEGIN
          INSERT INTO flw_actions_aud
            SELECT now(6), \'DELETE\', flw_actions.* 
            FROM flw_actions 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER flw_guidancefields_audit_insert
          AFTER INSERT ON flw_guidancefields
          FOR EACH ROW BEGIN
          INSERT INTO flw_guidancefields_aud
            SELECT now(6), \'INSERT\', flw_guidancefields.* 
            FROM flw_guidancefields 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER flw_guidancefields_audit_update
          AFTER UPDATE ON flw_guidancefields
          FOR EACH ROW BEGIN
          INSERT INTO flw_guidancefields_aud
            SELECT now(6), \'UPDATE\', flw_guidancefields.* 
            FROM flw_guidancefields 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER flw_guidancefields_audit_delete
          BEFORE DELETE ON flw_guidancefields
          FOR EACH ROW BEGIN
          INSERT INTO flw_guidancefields_aud
            SELECT now(6), \'DELETE\', flw_guidancefields.* 
            FROM flw_guidancefields 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER flw_guidancefields_flw_guidancefieldvalidators_1_c_audit_insert
          AFTER INSERT ON flw_guidancefields_flw_guidancefieldvalidators_1_c
          FOR EACH ROW BEGIN
          INSERT INTO flw_guidancefields_flw_guidancefieldvalidators_1_c_aud
            SELECT now(6), \'INSERT\', flw_guidancefields_flw_guidancefieldvalidators_1_c.* 
            FROM flw_guidancefields_flw_guidancefieldvalidators_1_c 
            WHERE flow_field_id = NEW.flow_field_id AND validator_id = NEW.validator_id;
        END;');
        $this->addSql('CREATE TRIGGER flw_guidancefields_flw_guidancefieldvalidators_1_c_audit_update
          AFTER UPDATE ON flw_guidancefields_flw_guidancefieldvalidators_1_c
          FOR EACH ROW BEGIN
          INSERT INTO flw_guidancefields_flw_guidancefieldvalidators_1_c_aud
            SELECT now(6), \'UPDATE\', flw_guidancefields_flw_guidancefieldvalidators_1_c.* 
            FROM flw_guidancefields_flw_guidancefieldvalidators_1_c 
            WHERE flow_field_id = NEW.flow_field_id AND validator_id = NEW.validator_id;
        END;');
        $this->addSql('CREATE TRIGGER flw_guidancefields_flw_guidancefieldvalidators_1_c_audit_delete
          BEFORE DELETE ON flw_guidancefields_flw_guidancefieldvalidators_1_c
          FOR EACH ROW BEGIN
          INSERT INTO flw_guidancefields_flw_guidancefieldvalidators_1_c_aud
            SELECT now(6), \'DELETE\', flw_guidancefields_flw_guidancefieldvalidators_1_c.* 
            FROM flw_guidancefields_flw_guidancefieldvalidators_1_c 
            WHERE flow_field_id = OLD.flow_field_id AND validator_id = OLD.validator_id;
        END;');
        $this->addSql('CREATE TRIGGER flw_guidancefields_flw_flowsteps_c_audit_insert
          AFTER INSERT ON flw_guidancefields_flw_flowsteps_c
          FOR EACH ROW BEGIN
          INSERT INTO flw_guidancefields_flw_flowsteps_c_aud
            SELECT now(6), \'INSERT\', flw_guidancefields_flw_flowsteps_c.* 
            FROM flw_guidancefields_flw_flowsteps_c 
            WHERE flow_field_id = NEW.flow_field_id AND flow_step_id = NEW.flow_step_id;
        END;');
        $this->addSql('CREATE TRIGGER flw_guidancefields_flw_flowsteps_c_audit_update
          AFTER UPDATE ON flw_guidancefields_flw_flowsteps_c
          FOR EACH ROW BEGIN
          INSERT INTO flw_guidancefields_flw_flowsteps_c_aud
            SELECT now(6), \'UPDATE\', flw_guidancefields_flw_flowsteps_c.* 
            FROM flw_guidancefields_flw_flowsteps_c 
            WHERE flow_field_id = NEW.flow_field_id AND flow_step_id = NEW.flow_step_id;
        END;');
        $this->addSql('CREATE TRIGGER flw_guidancefields_flw_flowsteps_c_audit_delete
          BEFORE DELETE ON flw_guidancefields_flw_flowsteps_c
          FOR EACH ROW BEGIN
          INSERT INTO flw_guidancefields_flw_flowsteps_c_aud
            SELECT now(6), \'DELETE\', flw_guidancefields_flw_flowsteps_c.* 
            FROM flw_guidancefields_flw_flowsteps_c 
            WHERE flow_field_id = OLD.flow_field_id AND flow_step_id = OLD.flow_step_id;
        END;');
        $this->addSql('CREATE TRIGGER flw_flowsteps_audit_insert
          AFTER INSERT ON flw_flowsteps
          FOR EACH ROW BEGIN
          INSERT INTO flw_flowsteps_aud
            SELECT now(6), \'INSERT\', flw_flowsteps.* 
            FROM flw_flowsteps 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER flw_flowsteps_audit_update
          AFTER UPDATE ON flw_flowsteps
          FOR EACH ROW BEGIN
          INSERT INTO flw_flowsteps_aud
            SELECT now(6), \'UPDATE\', flw_flowsteps.* 
            FROM flw_flowsteps 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER flw_flowsteps_audit_delete
          BEFORE DELETE ON flw_flowsteps
          FOR EACH ROW BEGIN
          INSERT INTO flw_flowsteps_aud
            SELECT now(6), \'DELETE\', flw_flowsteps.* 
            FROM flw_flowsteps 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER flw_flowsteps_flw_flowstepproperties_1_c_audit_insert
          AFTER INSERT ON flw_flowsteps_flw_flowstepproperties_1_c
          FOR EACH ROW BEGIN
          INSERT INTO flw_flowsteps_flw_flowstepproperties_1_c_aud
            SELECT now(6), \'INSERT\', flw_flowsteps_flw_flowstepproperties_1_c.* 
            FROM flw_flowsteps_flw_flowstepproperties_1_c 
            WHERE flow_step_id = NEW.flow_step_id AND flow_step_property_id = NEW.flow_step_property_id;
        END;');
        $this->addSql('CREATE TRIGGER flw_flowsteps_flw_flowstepproperties_1_c_audit_update
          AFTER UPDATE ON flw_flowsteps_flw_flowstepproperties_1_c
          FOR EACH ROW BEGIN
          INSERT INTO flw_flowsteps_flw_flowstepproperties_1_c_aud
            SELECT now(6), \'UPDATE\', flw_flowsteps_flw_flowstepproperties_1_c.* 
            FROM flw_flowsteps_flw_flowstepproperties_1_c 
            WHERE flow_step_id = NEW.flow_step_id AND flow_step_property_id = NEW.flow_step_property_id;
        END;');
        $this->addSql('CREATE TRIGGER flw_flowsteps_flw_flowstepproperties_1_c_audit_delete
          BEFORE DELETE ON flw_flowsteps_flw_flowstepproperties_1_c
          FOR EACH ROW BEGIN
          INSERT INTO flw_flowsteps_flw_flowstepproperties_1_c_aud
            SELECT now(6), \'DELETE\', flw_flowsteps_flw_flowstepproperties_1_c.* 
            FROM flw_flowsteps_flw_flowstepproperties_1_c 
            WHERE flow_step_id = OLD.flow_step_id AND flow_step_property_id = OLD.flow_step_property_id;
        END;');
        $this->addSql('CREATE TRIGGER flw_flowstepslink_audit_insert
          AFTER INSERT ON flw_flowstepslink
          FOR EACH ROW BEGIN
          INSERT INTO flw_flowstepslink_aud
            SELECT now(6), \'INSERT\', flw_flowstepslink.* 
            FROM flw_flowstepslink 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER flw_flowstepslink_audit_update
          AFTER UPDATE ON flw_flowstepslink
          FOR EACH ROW BEGIN
          INSERT INTO flw_flowstepslink_aud
            SELECT now(6), \'UPDATE\', flw_flowstepslink.* 
            FROM flw_flowstepslink 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER flw_flowstepslink_audit_delete
          BEFORE DELETE ON flw_flowstepslink
          FOR EACH ROW BEGIN
          INSERT INTO flw_flowstepslink_aud
            SELECT now(6), \'DELETE\', flw_flowstepslink.* 
            FROM flw_flowstepslink 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER flw_flowstepproperties_audit_insert
          AFTER INSERT ON flw_flowstepproperties
          FOR EACH ROW BEGIN
          INSERT INTO flw_flowstepproperties_aud
            SELECT now(6), \'INSERT\', flw_flowstepproperties.* 
            FROM flw_flowstepproperties 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER flw_flowstepproperties_audit_update
          AFTER UPDATE ON flw_flowstepproperties
          FOR EACH ROW BEGIN
          INSERT INTO flw_flowstepproperties_aud
            SELECT now(6), \'UPDATE\', flw_flowstepproperties.* 
            FROM flw_flowstepproperties 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER flw_flowstepproperties_audit_delete
          BEFORE DELETE ON flw_flowstepproperties
          FOR EACH ROW BEGIN
          INSERT INTO flw_flowstepproperties_aud
            SELECT now(6), \'DELETE\', flw_flowstepproperties.* 
            FROM flw_flowstepproperties 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER grid_panels_audit_insert
          AFTER INSERT ON grid_panels
          FOR EACH ROW BEGIN
          INSERT INTO grid_panels_aud
            SELECT now(6), \'INSERT\', grid_panels.* 
            FROM grid_panels 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER grid_panels_audit_update
          AFTER UPDATE ON grid_panels
          FOR EACH ROW BEGIN
          INSERT INTO grid_panels_aud
            SELECT now(6), \'UPDATE\', grid_panels.* 
            FROM grid_panels 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER grid_panels_audit_delete
          BEFORE DELETE ON grid_panels
          FOR EACH ROW BEGIN
          INSERT INTO grid_panels_aud
            SELECT now(6), \'DELETE\', grid_panels.* 
            FROM grid_panels 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER grid_panels_flw_guidancefieldvalidators_1_c_audit_insert
          AFTER INSERT ON grid_panels_flw_guidancefieldvalidators_1_c
          FOR EACH ROW BEGIN
          INSERT INTO grid_panels_flw_guidancefieldvalidators_1_c_aud
            SELECT now(6), \'INSERT\', grid_panels_flw_guidancefieldvalidators_1_c.* 
            FROM grid_panels_flw_guidancefieldvalidators_1_c 
            WHERE grid_panel_id = NEW.grid_panel_id AND validator_id = NEW.validator_id;
        END;');
        $this->addSql('CREATE TRIGGER grid_panels_flw_guidancefieldvalidators_1_c_audit_update
          AFTER UPDATE ON grid_panels_flw_guidancefieldvalidators_1_c
          FOR EACH ROW BEGIN
          INSERT INTO grid_panels_flw_guidancefieldvalidators_1_c_aud
            SELECT now(6), \'UPDATE\', grid_panels_flw_guidancefieldvalidators_1_c.* 
            FROM grid_panels_flw_guidancefieldvalidators_1_c 
            WHERE grid_panel_id = NEW.grid_panel_id AND validator_id = NEW.validator_id;
        END;');
        $this->addSql('CREATE TRIGGER grid_panels_flw_guidancefieldvalidators_1_c_audit_delete
          BEFORE DELETE ON grid_panels_flw_guidancefieldvalidators_1_c
          FOR EACH ROW BEGIN
          INSERT INTO grid_panels_flw_guidancefieldvalidators_1_c_aud
            SELECT now(6), \'DELETE\', grid_panels_flw_guidancefieldvalidators_1_c.* 
            FROM grid_panels_flw_guidancefieldvalidators_1_c 
            WHERE grid_panel_id = OLD.grid_panel_id AND validator_id = OLD.validator_id;
        END;');
        $this->addSql('CREATE TRIGGER grid_gridtemplates_audit_insert
          AFTER INSERT ON grid_gridtemplates
          FOR EACH ROW BEGIN
          INSERT INTO grid_gridtemplates_aud
            SELECT now(6), \'INSERT\', grid_gridtemplates.* 
            FROM grid_gridtemplates 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER grid_gridtemplates_audit_update
          AFTER UPDATE ON grid_gridtemplates
          FOR EACH ROW BEGIN
          INSERT INTO grid_gridtemplates_aud
            SELECT now(6), \'UPDATE\', grid_gridtemplates.* 
            FROM grid_gridtemplates 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER grid_gridtemplates_audit_delete
          BEFORE DELETE ON grid_gridtemplates
          FOR EACH ROW BEGIN
          INSERT INTO grid_gridtemplates_aud
            SELECT now(6), \'DELETE\', grid_gridtemplates.* 
            FROM grid_gridtemplates 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER list_cell_audit_insert
          AFTER INSERT ON list_cell
          FOR EACH ROW BEGIN
          INSERT INTO list_cell_aud
            SELECT now(6), \'INSERT\', list_cell.* 
            FROM list_cell 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER list_cell_audit_update
          AFTER UPDATE ON list_cell
          FOR EACH ROW BEGIN
          INSERT INTO list_cell_aud
            SELECT now(6), \'UPDATE\', list_cell.* 
            FROM list_cell 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER list_cell_audit_delete
          BEFORE DELETE ON list_cell
          FOR EACH ROW BEGIN
          INSERT INTO list_cell_aud
            SELECT now(6), \'DELETE\', list_cell.* 
            FROM list_cell 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER list_cells_audit_insert
          AFTER INSERT ON list_cells
          FOR EACH ROW BEGIN
          INSERT INTO list_cells_aud
            SELECT now(6), \'INSERT\', list_cells.* 
            FROM list_cells 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER list_cells_audit_update
          AFTER UPDATE ON list_cells
          FOR EACH ROW BEGIN
          INSERT INTO list_cells_aud
            SELECT now(6), \'UPDATE\', list_cells.* 
            FROM list_cells 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER list_cells_audit_delete
          BEFORE DELETE ON list_cells
          FOR EACH ROW BEGIN
          INSERT INTO list_cells_aud
            SELECT now(6), \'DELETE\', list_cells.* 
            FROM list_cells 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER list_dynamic_list_audit_insert
          AFTER INSERT ON list_dynamic_list
          FOR EACH ROW BEGIN
          INSERT INTO list_dynamic_list_aud
            SELECT now(6), \'INSERT\', list_dynamic_list.* 
            FROM list_dynamic_list 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER list_dynamic_list_audit_update
          AFTER UPDATE ON list_dynamic_list
          FOR EACH ROW BEGIN
          INSERT INTO list_dynamic_list_aud
            SELECT now(6), \'UPDATE\', list_dynamic_list.* 
            FROM list_dynamic_list 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER list_dynamic_list_audit_delete
          BEFORE DELETE ON list_dynamic_list
          FOR EACH ROW BEGIN
          INSERT INTO list_dynamic_list_aud
            SELECT now(6), \'DELETE\', list_dynamic_list.* 
            FROM list_dynamic_list 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER list_row_action_audit_insert
          AFTER INSERT ON list_row_action
          FOR EACH ROW BEGIN
          INSERT INTO list_row_action_aud
            SELECT now(6), \'INSERT\', list_row_action.* 
            FROM list_row_action 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER list_row_action_audit_update
          AFTER UPDATE ON list_row_action
          FOR EACH ROW BEGIN
          INSERT INTO list_row_action_aud
            SELECT now(6), \'UPDATE\', list_row_action.* 
            FROM list_row_action 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER list_row_action_audit_delete
          BEFORE DELETE ON list_row_action
          FOR EACH ROW BEGIN
          INSERT INTO list_row_action_aud
            SELECT now(6), \'DELETE\', list_row_action.* 
            FROM list_row_action 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER list_row_bar_audit_insert
          AFTER INSERT ON list_row_bar
          FOR EACH ROW BEGIN
          INSERT INTO list_row_bar_aud
            SELECT now(6), \'INSERT\', list_row_bar.* 
            FROM list_row_bar 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER list_row_bar_audit_update
          AFTER UPDATE ON list_row_bar
          FOR EACH ROW BEGIN
          INSERT INTO list_row_bar_aud
            SELECT now(6), \'UPDATE\', list_row_bar.* 
            FROM list_row_bar 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER list_row_bar_audit_delete
          BEFORE DELETE ON list_row_bar
          FOR EACH ROW BEGIN
          INSERT INTO list_row_bar_aud
            SELECT now(6), \'DELETE\', list_row_bar.* 
            FROM list_row_bar 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER list_sorting_options_audit_insert
          AFTER INSERT ON list_sorting_options
          FOR EACH ROW BEGIN
          INSERT INTO list_sorting_options_aud
            SELECT now(6), \'INSERT\', list_sorting_options.* 
            FROM list_sorting_options 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER list_sorting_options_audit_update
          AFTER UPDATE ON list_sorting_options
          FOR EACH ROW BEGIN
          INSERT INTO list_sorting_options_aud
            SELECT now(6), \'UPDATE\', list_sorting_options.* 
            FROM list_sorting_options 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER list_sorting_options_audit_delete
          BEFORE DELETE ON list_sorting_options
          FOR EACH ROW BEGIN
          INSERT INTO list_sorting_options_aud
            SELECT now(6), \'DELETE\', list_sorting_options.* 
            FROM list_sorting_options 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER list_top_action_audit_insert
          AFTER INSERT ON list_top_action
          FOR EACH ROW BEGIN
          INSERT INTO list_top_action_aud
            SELECT now(6), \'INSERT\', list_top_action.* 
            FROM list_top_action 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER list_top_action_audit_update
          AFTER UPDATE ON list_top_action
          FOR EACH ROW BEGIN
          INSERT INTO list_top_action_aud
            SELECT now(6), \'UPDATE\', list_top_action.* 
            FROM list_top_action 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER list_top_action_audit_delete
          BEFORE DELETE ON list_top_action
          FOR EACH ROW BEGIN
          INSERT INTO list_top_action_aud
            SELECT now(6), \'DELETE\', list_top_action.* 
            FROM list_top_action 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER list_topbar_audit_insert
          AFTER INSERT ON list_topbar
          FOR EACH ROW BEGIN
          INSERT INTO list_topbar_aud
            SELECT now(6), \'INSERT\', list_topbar.* 
            FROM list_topbar 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER list_topbar_audit_update
          AFTER UPDATE ON list_topbar
          FOR EACH ROW BEGIN
          INSERT INTO list_topbar_aud
            SELECT now(6), \'UPDATE\', list_topbar.* 
            FROM list_topbar 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER list_topbar_audit_delete
          BEFORE DELETE ON list_topbar
          FOR EACH ROW BEGIN
          INSERT INTO list_topbar_aud
            SELECT now(6), \'DELETE\', list_topbar.* 
            FROM list_topbar 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER list_topbar_list_sorting_options_c_audit_insert
          AFTER INSERT ON list_topbar_list_sorting_options_c
          FOR EACH ROW BEGIN
          INSERT INTO list_topbar_list_sorting_options_c_aud
            SELECT now(6), \'INSERT\', list_topbar_list_sorting_options_c.* 
            FROM list_topbar_list_sorting_options_c 
            WHERE list_top_bar_id = NEW.list_top_bar_id AND list_sorting_option_id = NEW.list_sorting_option_id;
        END;');
        $this->addSql('CREATE TRIGGER list_topbar_list_sorting_options_c_audit_update
          AFTER UPDATE ON list_topbar_list_sorting_options_c
          FOR EACH ROW BEGIN
          INSERT INTO list_topbar_list_sorting_options_c_aud
            SELECT now(6), \'UPDATE\', list_topbar_list_sorting_options_c.* 
            FROM list_topbar_list_sorting_options_c 
            WHERE list_top_bar_id = NEW.list_top_bar_id AND list_sorting_option_id = NEW.list_sorting_option_id;
        END;');
        $this->addSql('CREATE TRIGGER list_topbar_list_sorting_options_c_audit_delete
          BEFORE DELETE ON list_topbar_list_sorting_options_c
          FOR EACH ROW BEGIN
          INSERT INTO list_topbar_list_sorting_options_c_aud
            SELECT now(6), \'DELETE\', list_topbar_list_sorting_options_c.* 
            FROM list_topbar_list_sorting_options_c 
            WHERE list_top_bar_id = OLD.list_top_bar_id AND list_sorting_option_id = OLD.list_sorting_option_id;
        END;');
        $this->addSql('CREATE TRIGGER list_topbar_list_top_action_c_audit_insert
          AFTER INSERT ON list_topbar_list_top_action_c
          FOR EACH ROW BEGIN
          INSERT INTO list_topbar_list_top_action_c_aud
            SELECT now(6), \'INSERT\', list_topbar_list_top_action_c.* 
            FROM list_topbar_list_top_action_c 
            WHERE list_top_bar_id = NEW.list_top_bar_id AND list_top_action_id = NEW.list_top_action_id;
        END;');
        $this->addSql('CREATE TRIGGER list_topbar_list_top_action_c_audit_update
          AFTER UPDATE ON list_topbar_list_top_action_c
          FOR EACH ROW BEGIN
          INSERT INTO list_topbar_list_top_action_c_aud
            SELECT now(6), \'UPDATE\', list_topbar_list_top_action_c.* 
            FROM list_topbar_list_top_action_c 
            WHERE list_top_bar_id = NEW.list_top_bar_id AND list_top_action_id = NEW.list_top_action_id;
        END;');
        $this->addSql('CREATE TRIGGER list_topbar_list_top_action_c_audit_delete
          BEFORE DELETE ON list_topbar_list_top_action_c
          FOR EACH ROW BEGIN
          INSERT INTO list_topbar_list_top_action_c_aud
            SELECT now(6), \'DELETE\', list_topbar_list_top_action_c.* 
            FROM list_topbar_list_top_action_c 
            WHERE list_top_bar_id = OLD.list_top_bar_id AND list_top_action_id = OLD.list_top_action_id;
        END;');
        $this->addSql('CREATE TRIGGER menu_mainmenu_audit_insert
          AFTER INSERT ON menu_mainmenu
          FOR EACH ROW BEGIN
          INSERT INTO menu_mainmenu_aud
            SELECT now(6), \'INSERT\', menu_mainmenu.* 
            FROM menu_mainmenu 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER menu_mainmenu_audit_update
          AFTER UPDATE ON menu_mainmenu
          FOR EACH ROW BEGIN
          INSERT INTO menu_mainmenu_aud
            SELECT now(6), \'UPDATE\', menu_mainmenu.* 
            FROM menu_mainmenu 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER menu_mainmenu_audit_delete
          BEFORE DELETE ON menu_mainmenu
          FOR EACH ROW BEGIN
          INSERT INTO menu_mainmenu_aud
            SELECT now(6), \'DELETE\', menu_mainmenu.* 
            FROM menu_mainmenu 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER menu_mainmenu_dash_dashboard_c_audit_insert
          AFTER INSERT ON menu_mainmenu_dash_dashboard_c
          FOR EACH ROW BEGIN
          INSERT INTO menu_mainmenu_dash_dashboard_c_aud
            SELECT now(6), \'INSERT\', menu_mainmenu_dash_dashboard_c.* 
            FROM menu_mainmenu_dash_dashboard_c 
            WHERE menu_id = NEW.menu_id AND dashboard_id = NEW.dashboard_id;
        END;');
        $this->addSql('CREATE TRIGGER menu_mainmenu_dash_dashboard_c_audit_update
          AFTER UPDATE ON menu_mainmenu_dash_dashboard_c
          FOR EACH ROW BEGIN
          INSERT INTO menu_mainmenu_dash_dashboard_c_aud
            SELECT now(6), \'UPDATE\', menu_mainmenu_dash_dashboard_c.* 
            FROM menu_mainmenu_dash_dashboard_c 
            WHERE menu_id = NEW.menu_id AND dashboard_id = NEW.dashboard_id;
        END;');
        $this->addSql('CREATE TRIGGER menu_mainmenu_dash_dashboard_c_audit_delete
          BEFORE DELETE ON menu_mainmenu_dash_dashboard_c
          FOR EACH ROW BEGIN
          INSERT INTO menu_mainmenu_dash_dashboard_c_aud
            SELECT now(6), \'DELETE\', menu_mainmenu_dash_dashboard_c.* 
            FROM menu_mainmenu_dash_dashboard_c 
            WHERE menu_id = OLD.menu_id AND dashboard_id = OLD.dashboard_id;
        END;');
        $this->addSql('CREATE TRIGGER securitygroups_audit_insert
          AFTER INSERT ON securitygroups
          FOR EACH ROW BEGIN
          INSERT INTO securitygroups_aud
            SELECT now(6), \'INSERT\', securitygroups.* 
            FROM securitygroups 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER securitygroups_audit_update
          AFTER UPDATE ON securitygroups
          FOR EACH ROW BEGIN
          INSERT INTO securitygroups_aud
            SELECT now(6), \'UPDATE\', securitygroups.* 
            FROM securitygroups 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER securitygroups_audit_delete
          BEFORE DELETE ON securitygroups
          FOR EACH ROW BEGIN
          INSERT INTO securitygroups_aud
            SELECT now(6), \'DELETE\', securitygroups.* 
            FROM securitygroups 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER securitygroups_acl_roles_audit_insert
          AFTER INSERT ON securitygroups_acl_roles
          FOR EACH ROW BEGIN
          INSERT INTO securitygroups_acl_roles_aud
            SELECT now(6), \'INSERT\', securitygroups_acl_roles.* 
            FROM securitygroups_acl_roles 
            WHERE security_group_id = NEW.security_group_id AND acl_role_id = NEW.acl_role_id;
        END;');
        $this->addSql('CREATE TRIGGER securitygroups_acl_roles_audit_update
          AFTER UPDATE ON securitygroups_acl_roles
          FOR EACH ROW BEGIN
          INSERT INTO securitygroups_acl_roles_aud
            SELECT now(6), \'UPDATE\', securitygroups_acl_roles.* 
            FROM securitygroups_acl_roles 
            WHERE security_group_id = NEW.security_group_id AND acl_role_id = NEW.acl_role_id;
        END;');
        $this->addSql('CREATE TRIGGER securitygroups_acl_roles_audit_delete
          BEFORE DELETE ON securitygroups_acl_roles
          FOR EACH ROW BEGIN
          INSERT INTO securitygroups_acl_roles_aud
            SELECT now(6), \'DELETE\', securitygroups_acl_roles.* 
            FROM securitygroups_acl_roles 
            WHERE security_group_id = OLD.security_group_id AND acl_role_id = OLD.acl_role_id;
        END;');
        $this->addSql('CREATE TRIGGER securitygroups_api_audit_insert
          AFTER INSERT ON securitygroups_api
          FOR EACH ROW BEGIN
          INSERT INTO securitygroups_api_aud
            SELECT now(6), \'INSERT\', securitygroups_api.* 
            FROM securitygroups_api 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER securitygroups_api_audit_update
          AFTER UPDATE ON securitygroups_api
          FOR EACH ROW BEGIN
          INSERT INTO securitygroups_api_aud
            SELECT now(6), \'UPDATE\', securitygroups_api.* 
            FROM securitygroups_api 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER securitygroups_api_audit_delete
          BEFORE DELETE ON securitygroups_api
          FOR EACH ROW BEGIN
          INSERT INTO securitygroups_api_aud
            SELECT now(6), \'DELETE\', securitygroups_api.* 
            FROM securitygroups_api 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER securitygroups_records_audit_insert
          AFTER INSERT ON securitygroups_records
          FOR EACH ROW BEGIN
          INSERT INTO securitygroups_records_aud
            SELECT now(6), \'INSERT\', securitygroups_records.* 
            FROM securitygroups_records 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER securitygroups_records_audit_update
          AFTER UPDATE ON securitygroups_records
          FOR EACH ROW BEGIN
          INSERT INTO securitygroups_records_aud
            SELECT now(6), \'UPDATE\', securitygroups_records.* 
            FROM securitygroups_records 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER securitygroups_records_audit_delete
          BEFORE DELETE ON securitygroups_records
          FOR EACH ROW BEGIN
          INSERT INTO securitygroups_records_aud
            SELECT now(6), \'DELETE\', securitygroups_records.* 
            FROM securitygroups_records 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER securitygroups_users_audit_insert
          AFTER INSERT ON securitygroups_users
          FOR EACH ROW BEGIN
          INSERT INTO securitygroups_users_aud
            SELECT now(6), \'INSERT\', securitygroups_users.* 
            FROM securitygroups_users 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER securitygroups_users_audit_update
          AFTER UPDATE ON securitygroups_users
          FOR EACH ROW BEGIN
          INSERT INTO securitygroups_users_aud
            SELECT now(6), \'UPDATE\', securitygroups_users.* 
            FROM securitygroups_users 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER securitygroups_users_audit_delete
          BEFORE DELETE ON securitygroups_users
          FOR EACH ROW BEGIN
          INSERT INTO securitygroups_users_aud
            SELECT now(6), \'DELETE\', securitygroups_users.* 
            FROM securitygroups_users 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER fe_selectwithsearch_audit_insert
          AFTER INSERT ON fe_selectwithsearch
          FOR EACH ROW BEGIN
          INSERT INTO fe_selectwithsearch_aud
            SELECT now(6), \'INSERT\', fe_selectwithsearch.* 
            FROM fe_selectwithsearch 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER fe_selectwithsearch_audit_update
          AFTER UPDATE ON fe_selectwithsearch
          FOR EACH ROW BEGIN
          INSERT INTO fe_selectwithsearch_aud
            SELECT now(6), \'UPDATE\', fe_selectwithsearch.* 
            FROM fe_selectwithsearch 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER fe_selectwithsearch_audit_delete
          BEFORE DELETE ON fe_selectwithsearch
          FOR EACH ROW BEGIN
          INSERT INTO fe_selectwithsearch_aud
            SELECT now(6), \'DELETE\', fe_selectwithsearch.* 
            FROM fe_selectwithsearch 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER trans_translation_audit_insert
          AFTER INSERT ON trans_translation
          FOR EACH ROW BEGIN
          INSERT INTO trans_translation_aud
            SELECT now(6), \'INSERT\', trans_translation.* 
            FROM trans_translation 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER trans_translation_audit_update
          AFTER UPDATE ON trans_translation
          FOR EACH ROW BEGIN
          INSERT INTO trans_translation_aud
            SELECT now(6), \'UPDATE\', trans_translation.* 
            FROM trans_translation 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER trans_translation_audit_delete
          BEFORE DELETE ON trans_translation
          FOR EACH ROW BEGIN
          INSERT INTO trans_translation_aud
            SELECT now(6), \'DELETE\', trans_translation.* 
            FROM trans_translation 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER users_audit_insert
          AFTER INSERT ON users
          FOR EACH ROW BEGIN
          INSERT INTO users_aud
            SELECT now(6), \'INSERT\', users.* 
            FROM users 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER users_audit_update
          AFTER UPDATE ON users
          FOR EACH ROW BEGIN
          INSERT INTO users_aud
            SELECT now(6), \'UPDATE\', users.* 
            FROM users 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER users_audit_delete
          BEFORE DELETE ON users
          FOR EACH ROW BEGIN
          INSERT INTO users_aud
            SELECT now(6), \'DELETE\', users.* 
            FROM users 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER user_guidance_recovery_audit_insert
          AFTER INSERT ON user_guidance_recovery
          FOR EACH ROW BEGIN
          INSERT INTO user_guidance_recovery_aud
            SELECT now(6), \'INSERT\', user_guidance_recovery.* 
            FROM user_guidance_recovery 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER user_guidance_recovery_audit_update
          AFTER UPDATE ON user_guidance_recovery
          FOR EACH ROW BEGIN
          INSERT INTO user_guidance_recovery_aud
            SELECT now(6), \'UPDATE\', user_guidance_recovery.* 
            FROM user_guidance_recovery 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER user_guidance_recovery_audit_delete
          BEFORE DELETE ON user_guidance_recovery
          FOR EACH ROW BEGIN
          INSERT INTO user_guidance_recovery_aud
            SELECT now(6), \'DELETE\', user_guidance_recovery.* 
            FROM user_guidance_recovery 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER user_login_audit_insert
          AFTER INSERT ON user_login
          FOR EACH ROW BEGIN
          INSERT INTO user_login_aud
            SELECT now(6), \'INSERT\', user_login.* 
            FROM user_login 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER user_login_audit_update
          AFTER UPDATE ON user_login
          FOR EACH ROW BEGIN
          INSERT INTO user_login_aud
            SELECT now(6), \'UPDATE\', user_login.* 
            FROM user_login 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER user_login_audit_delete
          BEFORE DELETE ON user_login
          FOR EACH ROW BEGIN
          INSERT INTO user_login_aud
            SELECT now(6), \'DELETE\', user_login.* 
            FROM user_login 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER flw_guidancefieldvalidators_audit_insert
          AFTER INSERT ON flw_guidancefieldvalidators
          FOR EACH ROW BEGIN
          INSERT INTO flw_guidancefieldvalidators_aud
            SELECT now(6), \'INSERT\', flw_guidancefieldvalidators.* 
            FROM flw_guidancefieldvalidators 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER flw_guidancefieldvalidators_audit_update
          AFTER UPDATE ON flw_guidancefieldvalidators
          FOR EACH ROW BEGIN
          INSERT INTO flw_guidancefieldvalidators_aud
            SELECT now(6), \'UPDATE\', flw_guidancefieldvalidators.* 
            FROM flw_guidancefieldvalidators 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER flw_guidancefieldvalidators_audit_delete
          BEFORE DELETE ON flw_guidancefieldvalidators
          FOR EACH ROW BEGIN
          INSERT INTO flw_guidancefieldvalidators_aud
            SELECT now(6), \'DELETE\', flw_guidancefieldvalidators.* 
            FROM flw_guidancefieldvalidators 
            WHERE id = OLD.id;
        END;');
        $this->addSql('CREATE TRIGGER flw_guidancefieldsvalidators_conditions_audit_insert
          AFTER INSERT ON flw_guidancefieldsvalidators_conditions
          FOR EACH ROW BEGIN
          INSERT INTO flw_guidancefieldsvalidators_conditions_aud
            SELECT now(6), \'INSERT\', flw_guidancefieldsvalidators_conditions.* 
            FROM flw_guidancefieldsvalidators_conditions 
            WHERE parent_id = NEW.parent_id AND child_id = NEW.child_id;
        END;');
        $this->addSql('CREATE TRIGGER flw_guidancefieldsvalidators_conditions_audit_update
          AFTER UPDATE ON flw_guidancefieldsvalidators_conditions
          FOR EACH ROW BEGIN
          INSERT INTO flw_guidancefieldsvalidators_conditions_aud
            SELECT now(6), \'UPDATE\', flw_guidancefieldsvalidators_conditions.* 
            FROM flw_guidancefieldsvalidators_conditions 
            WHERE parent_id = NEW.parent_id AND child_id = NEW.child_id;
        END;');
        $this->addSql('CREATE TRIGGER flw_guidancefieldsvalidators_conditions_audit_delete
          BEFORE DELETE ON flw_guidancefieldsvalidators_conditions
          FOR EACH ROW BEGIN
          INSERT INTO flw_guidancefieldsvalidators_conditions_aud
            SELECT now(6), \'DELETE\', flw_guidancefieldsvalidators_conditions.* 
            FROM flw_guidancefieldsvalidators_conditions 
            WHERE parent_id = OLD.parent_id AND child_id = OLD.child_id;
        END;');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE acl_actions');
        $this->addSql('DROP TABLE acl_roles');
        $this->addSql('DROP TABLE acl_roles_users');
        $this->addSql('DROP TABLE acl_roles_actions');
        $this->addSql('DROP TABLE conditionalmessage');
        $this->addSql('DROP TABLE conditional_message_validators');
        $this->addSql('DROP TABLE conf_defaults');
        $this->addSql('DROP TABLE dash_dashboard');
        $this->addSql('DROP TABLE dash_dashboard_dash_dashboardproperties_c');
        $this->addSql('DROP TABLE dash_dashboardmenu');
        $this->addSql('DROP TABLE dash_dashboardmenu_dash_dashboardmenuactiongroup_1_c');
        $this->addSql('DROP TABLE dash_dashboardmenu_dash_menuactions_1_c');
        $this->addSql('DROP TABLE dash_menuactions');
        $this->addSql('DROP TABLE dash_dashboardmenuactiongroup');
        $this->addSql('DROP TABLE dash_menuactiongroup_x_dash_menuactiongroup');
        $this->addSql('DROP TABLE dash_dashboardmenuactiongroup_dash_menuactions');
        $this->addSql('DROP TABLE dash_dashboardproperties');
        $this->addSql('DROP TABLE list_external_object');
        $this->addSql('DROP TABLE list_external_object_linkfields');
        $this->addSql('DROP TABLE fltrs_filters');
        $this->addSql('DROP TABLE fltrs_fields');
        $this->addSql('DROP TABLE fltrs_fieldsgroup');
        $this->addSql('DROP TABLE fltrs_fieldsgroup_fltrs_fields_1_c');
        $this->addSql('DROP TABLE fltrs_fieldsgroup_fltrs_filters_1_c');
        $this->addSql('DROP TABLE find_search');
        $this->addSql('DROP TABLE flw_flows');
        $this->addSql('DROP TABLE flw_actions');
        $this->addSql('DROP TABLE flw_guidancefields');
        $this->addSql('DROP TABLE flw_guidancefields_flw_guidancefieldvalidators_1_c');
        $this->addSql('DROP TABLE flw_guidancefields_flw_flowsteps_c');
        $this->addSql('DROP TABLE flw_flowsteps');
        $this->addSql('DROP TABLE flw_flowsteps_flw_flowstepproperties_1_c');
        $this->addSql('DROP TABLE flw_flowstepslink');
        $this->addSql('DROP TABLE flw_flowstepproperties');
        $this->addSql('DROP TABLE grid_panels');
        $this->addSql('DROP TABLE grid_panels_flw_guidancefieldvalidators_1_c');
        $this->addSql('DROP TABLE grid_gridtemplates');
        $this->addSql('DROP TABLE list_cell');
        $this->addSql('DROP TABLE list_cells');
        $this->addSql('DROP TABLE list_dynamic_list');
        $this->addSql('DROP TABLE list_row_action');
        $this->addSql('DROP TABLE list_row_bar');
        $this->addSql('DROP TABLE list_sorting_options');
        $this->addSql('DROP TABLE list_top_action');
        $this->addSql('DROP TABLE list_topbar');
        $this->addSql('DROP TABLE list_topbar_list_sorting_options_c');
        $this->addSql('DROP TABLE list_topbar_list_top_action_c');
        $this->addSql('DROP TABLE menu_mainmenu');
        $this->addSql('DROP TABLE menu_mainmenu_dash_dashboard_c');
        $this->addSql('DROP TABLE phinxlog');
        $this->addSql('DROP TABLE securitygroups');
        $this->addSql('DROP TABLE securitygroups_acl_roles');
        $this->addSql('DROP TABLE securitygroups_api');
        $this->addSql('DROP TABLE securitygroups_records');
        $this->addSql('DROP TABLE securitygroups_users');
        $this->addSql('DROP TABLE fe_selectwithsearch');
        $this->addSql('DROP TABLE trans_translation');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE user_guidance_recovery');
        $this->addSql('DROP TABLE user_login');
        $this->addSql('DROP TABLE flw_guidancefieldvalidators');
        $this->addSql('DROP TABLE flw_guidancefieldsvalidators_conditions');
        $this->addSql('DROP TABLE acl_actions_aud');
        $this->addSql('DROP TABLE acl_roles_aud');
        $this->addSql('DROP TABLE acl_roles_users_aud');
        $this->addSql('DROP TABLE acl_roles_actions_aud');
        $this->addSql('DROP TABLE conditionalmessage_aud');
        $this->addSql('DROP TABLE conditional_message_validators_aud');
        $this->addSql('DROP TABLE conf_defaults_aud');
        $this->addSql('DROP TABLE dash_dashboard_aud');
        $this->addSql('DROP TABLE dash_dashboard_dash_dashboardproperties_c_aud');
        $this->addSql('DROP TABLE dash_dashboardmenu_aud');
        $this->addSql('DROP TABLE dash_dashboardmenu_dash_dashboardmenuactiongroup_1_c_aud');
        $this->addSql('DROP TABLE dash_dashboardmenu_dash_menuactions_1_c_aud');
        $this->addSql('DROP TABLE dash_menuactions_aud');
        $this->addSql('DROP TABLE dash_dashboardmenuactiongroup_aud');
        $this->addSql('DROP TABLE dash_menuactiongroup_x_dash_menuactiongroup_aud');
        $this->addSql('DROP TABLE dash_dashboardmenuactiongroup_dash_menuactions_aud');
        $this->addSql('DROP TABLE dash_dashboardproperties_aud');
        $this->addSql('DROP TABLE list_external_object_aud');
        $this->addSql('DROP TABLE list_external_object_linkfields_aud');
        $this->addSql('DROP TABLE fltrs_filters_aud');
        $this->addSql('DROP TABLE fltrs_fields_aud');
        $this->addSql('DROP TABLE fltrs_fieldsgroup_aud');
        $this->addSql('DROP TABLE fltrs_fieldsgroup_fltrs_fields_1_c_aud');
        $this->addSql('DROP TABLE fltrs_fieldsgroup_fltrs_filters_1_c_aud');
        $this->addSql('DROP TABLE find_search_aud');
        $this->addSql('DROP TABLE flw_flows_aud');
        $this->addSql('DROP TABLE flw_actions_aud');
        $this->addSql('DROP TABLE flw_guidancefields_aud');
        $this->addSql('DROP TABLE flw_guidancefields_flw_guidancefieldvalidators_1_c_aud');
        $this->addSql('DROP TABLE flw_guidancefields_flw_flowsteps_c_aud');
        $this->addSql('DROP TABLE flw_flowsteps_aud');
        $this->addSql('DROP TABLE flw_flowsteps_flw_flowstepproperties_1_c_aud');
        $this->addSql('DROP TABLE flw_flowstepslink_aud');
        $this->addSql('DROP TABLE flw_flowstepproperties_aud');
        $this->addSql('DROP TABLE grid_panels_aud');
        $this->addSql('DROP TABLE grid_panels_flw_guidancefieldvalidators_1_c_aud');
        $this->addSql('DROP TABLE grid_gridtemplates_aud');
        $this->addSql('DROP TABLE list_cell_aud');
        $this->addSql('DROP TABLE list_cells_aud');
        $this->addSql('DROP TABLE list_dynamic_list_aud');
        $this->addSql('DROP TABLE list_row_action_aud');
        $this->addSql('DROP TABLE list_row_bar_aud');
        $this->addSql('DROP TABLE list_sorting_options_aud');
        $this->addSql('DROP TABLE list_top_action_aud');
        $this->addSql('DROP TABLE list_topbar_aud');
        $this->addSql('DROP TABLE list_topbar_list_sorting_options_c_aud');
        $this->addSql('DROP TABLE list_topbar_list_top_action_c_aud');
        $this->addSql('DROP TABLE menu_mainmenu_aud');
        $this->addSql('DROP TABLE menu_mainmenu_dash_dashboard_c_aud');
        $this->addSql('DROP TABLE securitygroups_aud');
        $this->addSql('DROP TABLE securitygroups_acl_roles_aud');
        $this->addSql('DROP TABLE securitygroups_api_aud');
        $this->addSql('DROP TABLE securitygroups_records_aud');
        $this->addSql('DROP TABLE securitygroups_users_aud');
        $this->addSql('DROP TABLE fe_selectwithsearch_aud');
        $this->addSql('DROP TABLE trans_translation_aud');
        $this->addSql('DROP TABLE users_aud');
        $this->addSql('DROP TABLE user_guidance_recovery_aud');
        $this->addSql('DROP TABLE user_login_aud');
        $this->addSql('DROP TABLE flw_guidancefieldvalidators_aud');
        $this->addSql('DROP TABLE flw_guidancefieldsvalidators_conditions_aud');
        $this->addSql('DROP TRIGGER IF EXISTS acl_actions_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS acl_actions_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS acl_actions_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS acl_roles_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS acl_roles_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS acl_roles_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS acl_roles_users_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS acl_roles_users_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS acl_roles_users_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS acl_roles_actions_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS acl_roles_actions_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS acl_roles_actions_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS conditionalmessage_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS conditionalmessage_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS conditionalmessage_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS conditional_message_validators_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS conditional_message_validators_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS conditional_message_validators_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS conf_defaults_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS conf_defaults_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS conf_defaults_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS dash_dashboard_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS dash_dashboard_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS dash_dashboard_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS dash_dashboard_dash_dashboardproperties_c_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS dash_dashboard_dash_dashboardproperties_c_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS dash_dashboard_dash_dashboardproperties_c_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS dash_dashboardmenu_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS dash_dashboardmenu_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS dash_dashboardmenu_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS dash_dashboardmenu_dash_dashboardmenuactiongroup_1_c_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS dash_dashboardmenu_dash_dashboardmenuactiongroup_1_c_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS dash_dashboardmenu_dash_dashboardmenuactiongroup_1_c_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS dash_dashboardmenu_dash_menuactions_1_c_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS dash_dashboardmenu_dash_menuactions_1_c_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS dash_dashboardmenu_dash_menuactions_1_c_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS dash_menuactions_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS dash_menuactions_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS dash_menuactions_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS dash_dashboardmenuactiongroup_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS dash_dashboardmenuactiongroup_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS dash_dashboardmenuactiongroup_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS dash_menuactiongroup_x_dash_menuactiongroup_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS dash_menuactiongroup_x_dash_menuactiongroup_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS dash_menuactiongroup_x_dash_menuactiongroup_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS dash_dashboardmenuactiongroup_dash_menuactions_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS dash_dashboardmenuactiongroup_dash_menuactions_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS dash_dashboardmenuactiongroup_dash_menuactions_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS dash_dashboardproperties_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS dash_dashboardproperties_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS dash_dashboardproperties_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS list_external_object_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS list_external_object_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS list_external_object_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS list_external_object_linkfields_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS list_external_object_linkfields_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS list_external_object_linkfields_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS fltrs_filters_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS fltrs_filters_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS fltrs_filters_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS fltrs_fields_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS fltrs_fields_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS fltrs_fields_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS fltrs_fieldsgroup_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS fltrs_fieldsgroup_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS fltrs_fieldsgroup_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS fltrs_fieldsgroup_fltrs_fields_1_c_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS fltrs_fieldsgroup_fltrs_fields_1_c_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS fltrs_fieldsgroup_fltrs_fields_1_c_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS fltrs_fieldsgroup_fltrs_filters_1_c_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS fltrs_fieldsgroup_fltrs_filters_1_c_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS fltrs_fieldsgroup_fltrs_filters_1_c_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS find_search_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS find_search_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS find_search_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS flw_flows_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS flw_flows_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS flw_flows_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS flw_actions_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS flw_actions_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS flw_actions_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS flw_guidancefields_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS flw_guidancefields_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS flw_guidancefields_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS flw_guidancefields_flw_guidancefieldvalidators_1_c_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS flw_guidancefields_flw_guidancefieldvalidators_1_c_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS flw_guidancefields_flw_guidancefieldvalidators_1_c_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS flw_guidancefields_flw_flowsteps_c_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS flw_guidancefields_flw_flowsteps_c_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS flw_guidancefields_flw_flowsteps_c_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS flw_flowsteps_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS flw_flowsteps_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS flw_flowsteps_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS flw_flowsteps_flw_flowstepproperties_1_c_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS flw_flowsteps_flw_flowstepproperties_1_c_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS flw_flowsteps_flw_flowstepproperties_1_c_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS flw_flowstepslink_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS flw_flowstepslink_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS flw_flowstepslink_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS flw_flowstepproperties_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS flw_flowstepproperties_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS flw_flowstepproperties_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS grid_panels_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS grid_panels_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS grid_panels_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS grid_panels_flw_guidancefieldvalidators_1_c_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS grid_panels_flw_guidancefieldvalidators_1_c_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS grid_panels_flw_guidancefieldvalidators_1_c_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS grid_gridtemplates_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS grid_gridtemplates_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS grid_gridtemplates_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS list_cell_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS list_cell_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS list_cell_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS list_cells_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS list_cells_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS list_cells_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS list_dynamic_list_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS list_dynamic_list_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS list_dynamic_list_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS list_row_action_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS list_row_action_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS list_row_action_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS list_row_bar_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS list_row_bar_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS list_row_bar_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS list_sorting_options_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS list_sorting_options_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS list_sorting_options_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS list_top_action_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS list_top_action_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS list_top_action_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS list_topbar_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS list_topbar_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS list_topbar_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS list_topbar_list_sorting_options_c_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS list_topbar_list_sorting_options_c_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS list_topbar_list_sorting_options_c_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS list_topbar_list_top_action_c_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS list_topbar_list_top_action_c_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS list_topbar_list_top_action_c_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS menu_mainmenu_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS menu_mainmenu_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS menu_mainmenu_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS menu_mainmenu_dash_dashboard_c_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS menu_mainmenu_dash_dashboard_c_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS menu_mainmenu_dash_dashboard_c_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS securitygroups_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS securitygroups_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS securitygroups_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS securitygroups_acl_roles_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS securitygroups_acl_roles_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS securitygroups_acl_roles_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS securitygroups_api_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS securitygroups_api_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS securitygroups_api_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS securitygroups_records_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS securitygroups_records_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS securitygroups_records_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS securitygroups_users_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS securitygroups_users_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS securitygroups_users_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS fe_selectwithsearch_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS fe_selectwithsearch_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS fe_selectwithsearch_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS trans_translation_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS trans_translation_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS trans_translation_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS users_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS users_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS users_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS user_guidance_recovery_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS user_guidance_recovery_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS user_guidance_recovery_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS user_login_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS user_login_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS user_login_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS flw_guidancefieldvalidators_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS flw_guidancefieldvalidators_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS flw_guidancefieldvalidators_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS flw_guidancefieldsvalidators_conditions_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS flw_guidancefieldsvalidators_conditions_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS flw_guidancefieldsvalidators_conditions_audit_delete;');
    }
    // phpcs:enable
}
