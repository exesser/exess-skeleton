<?php declare(strict_types = 1);
// phpcs:ignoreFile

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
* Auto-generated Migration: Please modify to your needs!
*/
final class Version20210611144755 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE conditionalmessage CHANGE description_params description_params JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE dash_menuactions CHANGE params_c params_c JSON DEFAULT NULL, CHANGE conditionsenabled_c conditionsenabled_c JSON DEFAULT NULL, CHANGE conditions_hide_c conditions_hide_c JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE dash_dashboardmenuactiongroup CHANGE conditions_hide_c conditions_hide_c JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE fltrs_filters CHANGE default_filters_json_c default_filters_json_c JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE fltrs_fields CHANGE field_options_c field_options_c JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE find_search CHANGE params params JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE flw_actions CHANGE json json JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE flw_guidancefields CHANGE field_action_json field_action_json JSON DEFAULT NULL, CHANGE field_enum_values field_enum_values JSON DEFAULT NULL, CHANGE field_custom field_custom JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE flw_flowsteps CHANGE json_fields_c json_fields_c JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE grid_panels CHANGE params params JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE list_cell CHANGE params_c params_c JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE list_dynamic_list DROP search_config, CHANGE standard_filter standard_filter JSON DEFAULT NULL, CHANGE default_filter_values default_filter_values JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE list_row_action CHANGE conditionsenabled_c conditionsenabled_c JSON DEFAULT NULL, CHANGE conditions_hide_c conditions_hide_c JSON DEFAULT NULL, CHANGE params_c params_c JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE list_top_action CHANGE params_c params_c JSON DEFAULT NULL, CHANGE conditionsenabled_c conditionsenabled_c JSON DEFAULT NULL, CHANGE conditions_hide_c conditions_hide_c JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE menu_mainmenu CHANGE params_c params_c JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE user_guidance_recovery CHANGE recovery_data recovery_data JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE conditionalmessage_aud CHANGE description_params description_params JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE dash_menuactions_aud CHANGE params_c params_c JSON DEFAULT NULL, CHANGE conditionsenabled_c conditionsenabled_c JSON DEFAULT NULL, CHANGE conditions_hide_c conditions_hide_c JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE dash_dashboardmenuactiongroup_aud CHANGE conditions_hide_c conditions_hide_c JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE fltrs_filters_aud CHANGE default_filters_json_c default_filters_json_c JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE fltrs_fields_aud CHANGE field_options_c field_options_c JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE find_search_aud CHANGE params params JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE flw_actions_aud CHANGE json json JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE flw_guidancefields_aud CHANGE field_action_json field_action_json JSON DEFAULT NULL, CHANGE field_enum_values field_enum_values JSON DEFAULT NULL, CHANGE field_custom field_custom JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE flw_flowsteps_aud CHANGE json_fields_c json_fields_c JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE grid_panels_aud CHANGE params params JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE list_cell_aud CHANGE params_c params_c JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE list_dynamic_list_aud DROP search_config, CHANGE standard_filter standard_filter JSON DEFAULT NULL, CHANGE default_filter_values default_filter_values JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE list_row_action_aud CHANGE conditionsenabled_c conditionsenabled_c JSON DEFAULT NULL, CHANGE conditions_hide_c conditions_hide_c JSON DEFAULT NULL, CHANGE params_c params_c JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE list_top_action_aud CHANGE params_c params_c JSON DEFAULT NULL, CHANGE conditionsenabled_c conditionsenabled_c JSON DEFAULT NULL, CHANGE conditions_hide_c conditions_hide_c JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE menu_mainmenu_aud CHANGE params_c params_c JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE user_guidance_recovery_aud CHANGE recovery_data recovery_data JSON DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE conditionalmessage CHANGE description_params description_params TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE conditionalmessage_aud CHANGE description_params description_params TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE dash_dashboardmenuactiongroup CHANGE conditions_hide_c conditions_hide_c TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE dash_dashboardmenuactiongroup_aud CHANGE conditions_hide_c conditions_hide_c TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE dash_menuactions CHANGE params_c params_c TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE conditionsenabled_c conditionsenabled_c TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE conditions_hide_c conditions_hide_c TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE dash_menuactions_aud CHANGE params_c params_c TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE conditionsenabled_c conditionsenabled_c TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE conditions_hide_c conditions_hide_c TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE find_search CHANGE params params TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE find_search_aud CHANGE params params TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE fltrs_fields CHANGE field_options_c field_options_c TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE fltrs_fields_aud CHANGE field_options_c field_options_c TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE fltrs_filters CHANGE default_filters_json_c default_filters_json_c TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE fltrs_filters_aud CHANGE default_filters_json_c default_filters_json_c TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE flw_actions CHANGE json json TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE flw_actions_aud CHANGE json json TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE flw_flowsteps CHANGE json_fields_c json_fields_c TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE flw_flowsteps_aud CHANGE json_fields_c json_fields_c TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE flw_guidancefields CHANGE field_action_json field_action_json TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE field_enum_values field_enum_values TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE field_custom field_custom TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE flw_guidancefields_aud CHANGE field_action_json field_action_json TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE field_enum_values field_enum_values TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE field_custom field_custom TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE grid_panels CHANGE params params TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE grid_panels_aud CHANGE params params TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE list_cell CHANGE params_c params_c TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE list_cell_aud CHANGE params_c params_c TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE list_dynamic_list ADD search_config TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE standard_filter standard_filter TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE default_filter_values default_filter_values TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE list_dynamic_list_aud ADD search_config TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE standard_filter standard_filter TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE default_filter_values default_filter_values TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE list_row_action CHANGE conditionsenabled_c conditionsenabled_c TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE conditions_hide_c conditions_hide_c TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE params_c params_c TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE list_row_action_aud CHANGE conditionsenabled_c conditionsenabled_c TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE conditions_hide_c conditions_hide_c TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE params_c params_c TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE list_top_action CHANGE params_c params_c TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE conditionsenabled_c conditionsenabled_c TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE conditions_hide_c conditions_hide_c TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE list_top_action_aud CHANGE params_c params_c TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE conditionsenabled_c conditionsenabled_c TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE conditions_hide_c conditions_hide_c TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE menu_mainmenu CHANGE params_c params_c TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE menu_mainmenu_aud CHANGE params_c params_c TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user_guidance_recovery CHANGE recovery_data recovery_data LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user_guidance_recovery_aud CHANGE recovery_data recovery_data LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
