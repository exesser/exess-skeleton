<?php

declare(strict_types=1);
// phpcs:ignoreFile

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210504082034 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE security_group_conditional_message (conditional_message_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX IDX_D80855C65091F067 (conditional_message_id), INDEX IDX_D80855C69D3F5E95 (security_group_id), PRIMARY KEY(conditional_message_id, security_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_dashboard (dashboard_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX IDX_64BF8BA4B9D04D2B (dashboard_id), INDEX IDX_64BF8BA49D3F5E95 (security_group_id), PRIMARY KEY(dashboard_id, security_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_dashboard_menu (dashboard_menu_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX IDX_504C556A7D333B3F (dashboard_menu_id), INDEX IDX_504C556A9D3F5E95 (security_group_id), PRIMARY KEY(dashboard_menu_id, security_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_dashboard_menu_action (dashboard_menu_action_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX IDX_9B745B14D743A99 (dashboard_menu_action_id), INDEX IDX_9B745B149D3F5E95 (security_group_id), PRIMARY KEY(dashboard_menu_action_id, security_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_dashboard_menu_action_group (dashboard_menu_action_group_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX IDX_256776D4A79437DF (dashboard_menu_action_group_id), INDEX IDX_256776D49D3F5E95 (security_group_id), PRIMARY KEY(dashboard_menu_action_group_id, security_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_dashboard_property (dashboard_property_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX IDX_577361D7EB330A3 (dashboard_property_id), INDEX IDX_577361D79D3F5E95 (security_group_id), PRIMARY KEY(dashboard_property_id, security_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_external_object_link (external_object_link_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX IDX_18F4D622BC839A37 (external_object_link_id), INDEX IDX_18F4D6229D3F5E95 (security_group_id), PRIMARY KEY(external_object_link_id, security_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_filter (filter_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX IDX_96D77954D395B25E (filter_id), INDEX IDX_96D779549D3F5E95 (security_group_id), PRIMARY KEY(filter_id, security_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_filter_field (filter_field_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX IDX_F00D24F979580210 (filter_field_id), INDEX IDX_F00D24F99D3F5E95 (security_group_id), PRIMARY KEY(filter_field_id, security_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_filter_field_group (filter_field_group_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX IDX_A68AC241B6665182 (filter_field_group_id), INDEX IDX_A68AC2419D3F5E95 (security_group_id), PRIMARY KEY(filter_field_group_id, security_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_find_search (find_search_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX IDX_AFDD4A56C6BD9E80 (find_search_id), INDEX IDX_AFDD4A569D3F5E95 (security_group_id), PRIMARY KEY(find_search_id, security_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_flow (flow_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX IDX_BB485F757EB60D1B (flow_id), INDEX IDX_BB485F759D3F5E95 (security_group_id), PRIMARY KEY(flow_id, security_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_flow_action (flow_action_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX IDX_5F87F4AC8F1653AA (flow_action_id), INDEX IDX_5F87F4AC9D3F5E95 (security_group_id), PRIMARY KEY(flow_action_id, security_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_flow_field (flow_field_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX IDX_A7CA0C8FD77DC8E9 (flow_field_id), INDEX IDX_A7CA0C8F9D3F5E95 (security_group_id), PRIMARY KEY(flow_field_id, security_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_flow_step_link (flow_step_link_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX IDX_5027A7C4523F70BA (flow_step_link_id), INDEX IDX_5027A7C49D3F5E95 (security_group_id), PRIMARY KEY(flow_step_link_id, security_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_flow_step_property (flow_step_property_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX IDX_1E54593A1D1C7A89 (flow_step_property_id), INDEX IDX_1E54593A9D3F5E95 (security_group_id), PRIMARY KEY(flow_step_property_id, security_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_grid_panel (grid_panel_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX IDX_450BE7D1E0B613B0 (grid_panel_id), INDEX IDX_450BE7D19D3F5E95 (security_group_id), PRIMARY KEY(grid_panel_id, security_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_grid_template (grid_template_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX IDX_94E8F2744AAAEE12 (grid_template_id), INDEX IDX_94E8F2749D3F5E95 (security_group_id), PRIMARY KEY(grid_template_id, security_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_list_cell_link (list_cell_link_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX IDX_F292FCC7B23D0DDD (list_cell_link_id), INDEX IDX_F292FCC79D3F5E95 (security_group_id), PRIMARY KEY(list_cell_link_id, security_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_list (list_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX IDX_AD40711D3DAE168B (list_id), INDEX IDX_AD40711D9D3F5E95 (security_group_id), PRIMARY KEY(list_id, security_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_list_row_action (list_row_action_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX IDX_E7923908E1382E13 (list_row_action_id), INDEX IDX_E79239089D3F5E95 (security_group_id), PRIMARY KEY(list_row_action_id, security_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_list_sorting_option (list_sorting_option_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX IDX_FDB9FCE9A0237525 (list_sorting_option_id), INDEX IDX_FDB9FCE99D3F5E95 (security_group_id), PRIMARY KEY(list_sorting_option_id, security_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_list_top_action (list_top_action_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX IDX_E049405668E6E44D (list_top_action_id), INDEX IDX_E04940569D3F5E95 (security_group_id), PRIMARY KEY(list_top_action_id, security_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_list_top_bar (list_top_bar_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX IDX_C1ED7319854AFB6A (list_top_bar_id), INDEX IDX_C1ED73199D3F5E95 (security_group_id), PRIMARY KEY(list_top_bar_id, security_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_menu (menu_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX IDX_948DB396CCD7E912 (menu_id), INDEX IDX_948DB3969D3F5E95 (security_group_id), PRIMARY KEY(menu_id, security_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_security_group_api (security_group_api_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX IDX_DFC62DA7BD851F71 (security_group_api_id), INDEX IDX_DFC62DA79D3F5E95 (security_group_id), PRIMARY KEY(security_group_api_id, security_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_validator (validator_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX IDX_C289E494B0644AEC (validator_id), INDEX IDX_C289E4949D3F5E95 (security_group_id), PRIMARY KEY(validator_id, security_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_conditional_message_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', conditional_message_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, conditional_message_id, security_group_id), PRIMARY KEY(audit_timestamp, conditional_message_id, security_group_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_dashboard_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', dashboard_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, dashboard_id, security_group_id), PRIMARY KEY(audit_timestamp, dashboard_id, security_group_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_dashboard_menu_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', dashboard_menu_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, dashboard_menu_id, security_group_id), PRIMARY KEY(audit_timestamp, dashboard_menu_id, security_group_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_dashboard_menu_action_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', dashboard_menu_action_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, dashboard_menu_action_id, security_group_id), PRIMARY KEY(audit_timestamp, dashboard_menu_action_id, security_group_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_dashboard_menu_action_group_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', dashboard_menu_action_group_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, dashboard_menu_action_group_id, security_group_id), PRIMARY KEY(audit_timestamp, dashboard_menu_action_group_id, security_group_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_dashboard_property_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', dashboard_property_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, dashboard_property_id, security_group_id), PRIMARY KEY(audit_timestamp, dashboard_property_id, security_group_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_external_object_link_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', external_object_link_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, external_object_link_id, security_group_id), PRIMARY KEY(audit_timestamp, external_object_link_id, security_group_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_filter_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', filter_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, filter_id, security_group_id), PRIMARY KEY(audit_timestamp, filter_id, security_group_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_filter_field_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', filter_field_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, filter_field_id, security_group_id), PRIMARY KEY(audit_timestamp, filter_field_id, security_group_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_filter_field_group_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', filter_field_group_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, filter_field_group_id, security_group_id), PRIMARY KEY(audit_timestamp, filter_field_group_id, security_group_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_find_search_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', find_search_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, find_search_id, security_group_id), PRIMARY KEY(audit_timestamp, find_search_id, security_group_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_flow_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', flow_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, flow_id, security_group_id), PRIMARY KEY(audit_timestamp, flow_id, security_group_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_flow_action_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', flow_action_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, flow_action_id, security_group_id), PRIMARY KEY(audit_timestamp, flow_action_id, security_group_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_flow_field_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', flow_field_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, flow_field_id, security_group_id), PRIMARY KEY(audit_timestamp, flow_field_id, security_group_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_flow_step_link_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', flow_step_link_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, flow_step_link_id, security_group_id), PRIMARY KEY(audit_timestamp, flow_step_link_id, security_group_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_flow_step_property_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', flow_step_property_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, flow_step_property_id, security_group_id), PRIMARY KEY(audit_timestamp, flow_step_property_id, security_group_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_grid_panel_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', grid_panel_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, grid_panel_id, security_group_id), PRIMARY KEY(audit_timestamp, grid_panel_id, security_group_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_grid_template_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', grid_template_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, grid_template_id, security_group_id), PRIMARY KEY(audit_timestamp, grid_template_id, security_group_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_list_cell_link_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', list_cell_link_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, list_cell_link_id, security_group_id), PRIMARY KEY(audit_timestamp, list_cell_link_id, security_group_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_list_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', list_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, list_id, security_group_id), PRIMARY KEY(audit_timestamp, list_id, security_group_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_list_row_action_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', list_row_action_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, list_row_action_id, security_group_id), PRIMARY KEY(audit_timestamp, list_row_action_id, security_group_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_list_sorting_option_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', list_sorting_option_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, list_sorting_option_id, security_group_id), PRIMARY KEY(audit_timestamp, list_sorting_option_id, security_group_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_list_top_action_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', list_top_action_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, list_top_action_id, security_group_id), PRIMARY KEY(audit_timestamp, list_top_action_id, security_group_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_list_top_bar_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', list_top_bar_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, list_top_bar_id, security_group_id), PRIMARY KEY(audit_timestamp, list_top_bar_id, security_group_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_menu_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', menu_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, menu_id, security_group_id), PRIMARY KEY(audit_timestamp, menu_id, security_group_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_security_group_api_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', security_group_api_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, security_group_api_id, security_group_id), PRIMARY KEY(audit_timestamp, security_group_api_id, security_group_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_validator_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', validator_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, validator_id, security_group_id), PRIMARY KEY(audit_timestamp, validator_id, security_group_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE security_group_conditional_message ADD CONSTRAINT FK_D80855C65091F067 FOREIGN KEY (conditional_message_id) REFERENCES conditionalmessage (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_conditional_message ADD CONSTRAINT FK_D80855C69D3F5E95 FOREIGN KEY (security_group_id) REFERENCES securitygroups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_dashboard ADD CONSTRAINT FK_64BF8BA4B9D04D2B FOREIGN KEY (dashboard_id) REFERENCES dash_dashboard (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_dashboard ADD CONSTRAINT FK_64BF8BA49D3F5E95 FOREIGN KEY (security_group_id) REFERENCES securitygroups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_dashboard_menu ADD CONSTRAINT FK_504C556A7D333B3F FOREIGN KEY (dashboard_menu_id) REFERENCES dash_dashboardmenu (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_dashboard_menu ADD CONSTRAINT FK_504C556A9D3F5E95 FOREIGN KEY (security_group_id) REFERENCES securitygroups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_dashboard_menu_action ADD CONSTRAINT FK_9B745B14D743A99 FOREIGN KEY (dashboard_menu_action_id) REFERENCES dash_menuactions (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_dashboard_menu_action ADD CONSTRAINT FK_9B745B149D3F5E95 FOREIGN KEY (security_group_id) REFERENCES securitygroups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_dashboard_menu_action_group ADD CONSTRAINT FK_256776D4A79437DF FOREIGN KEY (dashboard_menu_action_group_id) REFERENCES dash_dashboardmenuactiongroup (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_dashboard_menu_action_group ADD CONSTRAINT FK_256776D49D3F5E95 FOREIGN KEY (security_group_id) REFERENCES securitygroups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_dashboard_property ADD CONSTRAINT FK_577361D7EB330A3 FOREIGN KEY (dashboard_property_id) REFERENCES dash_dashboardproperties (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_dashboard_property ADD CONSTRAINT FK_577361D79D3F5E95 FOREIGN KEY (security_group_id) REFERENCES securitygroups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_external_object_link ADD CONSTRAINT FK_18F4D622BC839A37 FOREIGN KEY (external_object_link_id) REFERENCES list_external_object_linkfields (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_external_object_link ADD CONSTRAINT FK_18F4D6229D3F5E95 FOREIGN KEY (security_group_id) REFERENCES securitygroups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_filter ADD CONSTRAINT FK_96D77954D395B25E FOREIGN KEY (filter_id) REFERENCES fltrs_filters (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_filter ADD CONSTRAINT FK_96D779549D3F5E95 FOREIGN KEY (security_group_id) REFERENCES securitygroups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_filter_field ADD CONSTRAINT FK_F00D24F979580210 FOREIGN KEY (filter_field_id) REFERENCES fltrs_fields (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_filter_field ADD CONSTRAINT FK_F00D24F99D3F5E95 FOREIGN KEY (security_group_id) REFERENCES securitygroups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_filter_field_group ADD CONSTRAINT FK_A68AC241B6665182 FOREIGN KEY (filter_field_group_id) REFERENCES fltrs_fieldsgroup (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_filter_field_group ADD CONSTRAINT FK_A68AC2419D3F5E95 FOREIGN KEY (security_group_id) REFERENCES securitygroups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_find_search ADD CONSTRAINT FK_AFDD4A56C6BD9E80 FOREIGN KEY (find_search_id) REFERENCES find_search (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_find_search ADD CONSTRAINT FK_AFDD4A569D3F5E95 FOREIGN KEY (security_group_id) REFERENCES securitygroups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_flow ADD CONSTRAINT FK_BB485F757EB60D1B FOREIGN KEY (flow_id) REFERENCES flw_flows (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_flow ADD CONSTRAINT FK_BB485F759D3F5E95 FOREIGN KEY (security_group_id) REFERENCES securitygroups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_flow_action ADD CONSTRAINT FK_5F87F4AC8F1653AA FOREIGN KEY (flow_action_id) REFERENCES flw_actions (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_flow_action ADD CONSTRAINT FK_5F87F4AC9D3F5E95 FOREIGN KEY (security_group_id) REFERENCES securitygroups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_flow_field ADD CONSTRAINT FK_A7CA0C8FD77DC8E9 FOREIGN KEY (flow_field_id) REFERENCES flw_guidancefields (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_flow_field ADD CONSTRAINT FK_A7CA0C8F9D3F5E95 FOREIGN KEY (security_group_id) REFERENCES securitygroups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_flow_step_link ADD CONSTRAINT FK_5027A7C4523F70BA FOREIGN KEY (flow_step_link_id) REFERENCES flw_flowstepslink (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_flow_step_link ADD CONSTRAINT FK_5027A7C49D3F5E95 FOREIGN KEY (security_group_id) REFERENCES securitygroups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_flow_step_property ADD CONSTRAINT FK_1E54593A1D1C7A89 FOREIGN KEY (flow_step_property_id) REFERENCES flw_flowstepproperties (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_flow_step_property ADD CONSTRAINT FK_1E54593A9D3F5E95 FOREIGN KEY (security_group_id) REFERENCES securitygroups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_grid_panel ADD CONSTRAINT FK_450BE7D1E0B613B0 FOREIGN KEY (grid_panel_id) REFERENCES grid_panels (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_grid_panel ADD CONSTRAINT FK_450BE7D19D3F5E95 FOREIGN KEY (security_group_id) REFERENCES securitygroups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_grid_template ADD CONSTRAINT FK_94E8F2744AAAEE12 FOREIGN KEY (grid_template_id) REFERENCES grid_gridtemplates (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_grid_template ADD CONSTRAINT FK_94E8F2749D3F5E95 FOREIGN KEY (security_group_id) REFERENCES securitygroups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_list_cell_link ADD CONSTRAINT FK_F292FCC7B23D0DDD FOREIGN KEY (list_cell_link_id) REFERENCES list_cells (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_list_cell_link ADD CONSTRAINT FK_F292FCC79D3F5E95 FOREIGN KEY (security_group_id) REFERENCES securitygroups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_list ADD CONSTRAINT FK_AD40711D3DAE168B FOREIGN KEY (list_id) REFERENCES list_dynamic_list (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_list ADD CONSTRAINT FK_AD40711D9D3F5E95 FOREIGN KEY (security_group_id) REFERENCES securitygroups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_list_row_action ADD CONSTRAINT FK_E7923908E1382E13 FOREIGN KEY (list_row_action_id) REFERENCES list_row_action (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_list_row_action ADD CONSTRAINT FK_E79239089D3F5E95 FOREIGN KEY (security_group_id) REFERENCES securitygroups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_list_sorting_option ADD CONSTRAINT FK_FDB9FCE9A0237525 FOREIGN KEY (list_sorting_option_id) REFERENCES list_sorting_options (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_list_sorting_option ADD CONSTRAINT FK_FDB9FCE99D3F5E95 FOREIGN KEY (security_group_id) REFERENCES securitygroups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_list_top_action ADD CONSTRAINT FK_E049405668E6E44D FOREIGN KEY (list_top_action_id) REFERENCES list_top_action (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_list_top_action ADD CONSTRAINT FK_E04940569D3F5E95 FOREIGN KEY (security_group_id) REFERENCES securitygroups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_list_top_bar ADD CONSTRAINT FK_C1ED7319854AFB6A FOREIGN KEY (list_top_bar_id) REFERENCES list_topbar (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_list_top_bar ADD CONSTRAINT FK_C1ED73199D3F5E95 FOREIGN KEY (security_group_id) REFERENCES securitygroups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_menu ADD CONSTRAINT FK_948DB396CCD7E912 FOREIGN KEY (menu_id) REFERENCES menu_mainmenu (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_menu ADD CONSTRAINT FK_948DB3969D3F5E95 FOREIGN KEY (security_group_id) REFERENCES securitygroups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_security_group_api ADD CONSTRAINT FK_DFC62DA7BD851F71 FOREIGN KEY (security_group_api_id) REFERENCES securitygroups_api (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_security_group_api ADD CONSTRAINT FK_DFC62DA79D3F5E95 FOREIGN KEY (security_group_id) REFERENCES securitygroups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_validator ADD CONSTRAINT FK_C289E494B0644AEC FOREIGN KEY (validator_id) REFERENCES flw_guidancefieldvalidators (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_validator ADD CONSTRAINT FK_C289E4949D3F5E95 FOREIGN KEY (security_group_id) REFERENCES securitygroups (id) ON DELETE CASCADE');
        $this->addSql('CREATE TRIGGER security_group_conditional_message_audit_insert
          AFTER INSERT ON security_group_conditional_message
          FOR EACH ROW BEGIN
          INSERT INTO security_group_conditional_message_aud
            SELECT now(6), \'INSERT\', security_group_conditional_message.* 
            FROM security_group_conditional_message 
            WHERE conditional_message_id = NEW.conditional_message_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_conditional_message_audit_update
          AFTER UPDATE ON security_group_conditional_message
          FOR EACH ROW BEGIN
          INSERT INTO security_group_conditional_message_aud
            SELECT now(6), \'UPDATE\', security_group_conditional_message.* 
            FROM security_group_conditional_message 
            WHERE conditional_message_id = NEW.conditional_message_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_conditional_message_audit_delete
          BEFORE DELETE ON security_group_conditional_message
          FOR EACH ROW BEGIN
          INSERT INTO security_group_conditional_message_aud
            SELECT now(6), \'DELETE\', security_group_conditional_message.* 
            FROM security_group_conditional_message 
            WHERE conditional_message_id = OLD.conditional_message_id AND security_group_id = OLD.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_dashboard_audit_insert
          AFTER INSERT ON security_group_dashboard
          FOR EACH ROW BEGIN
          INSERT INTO security_group_dashboard_aud
            SELECT now(6), \'INSERT\', security_group_dashboard.* 
            FROM security_group_dashboard 
            WHERE dashboard_id = NEW.dashboard_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_dashboard_audit_update
          AFTER UPDATE ON security_group_dashboard
          FOR EACH ROW BEGIN
          INSERT INTO security_group_dashboard_aud
            SELECT now(6), \'UPDATE\', security_group_dashboard.* 
            FROM security_group_dashboard 
            WHERE dashboard_id = NEW.dashboard_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_dashboard_audit_delete
          BEFORE DELETE ON security_group_dashboard
          FOR EACH ROW BEGIN
          INSERT INTO security_group_dashboard_aud
            SELECT now(6), \'DELETE\', security_group_dashboard.* 
            FROM security_group_dashboard 
            WHERE dashboard_id = OLD.dashboard_id AND security_group_id = OLD.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_dashboard_menu_audit_insert
          AFTER INSERT ON security_group_dashboard_menu
          FOR EACH ROW BEGIN
          INSERT INTO security_group_dashboard_menu_aud
            SELECT now(6), \'INSERT\', security_group_dashboard_menu.* 
            FROM security_group_dashboard_menu 
            WHERE dashboard_menu_id = NEW.dashboard_menu_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_dashboard_menu_audit_update
          AFTER UPDATE ON security_group_dashboard_menu
          FOR EACH ROW BEGIN
          INSERT INTO security_group_dashboard_menu_aud
            SELECT now(6), \'UPDATE\', security_group_dashboard_menu.* 
            FROM security_group_dashboard_menu 
            WHERE dashboard_menu_id = NEW.dashboard_menu_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_dashboard_menu_audit_delete
          BEFORE DELETE ON security_group_dashboard_menu
          FOR EACH ROW BEGIN
          INSERT INTO security_group_dashboard_menu_aud
            SELECT now(6), \'DELETE\', security_group_dashboard_menu.* 
            FROM security_group_dashboard_menu 
            WHERE dashboard_menu_id = OLD.dashboard_menu_id AND security_group_id = OLD.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_dashboard_menu_action_audit_insert
          AFTER INSERT ON security_group_dashboard_menu_action
          FOR EACH ROW BEGIN
          INSERT INTO security_group_dashboard_menu_action_aud
            SELECT now(6), \'INSERT\', security_group_dashboard_menu_action.* 
            FROM security_group_dashboard_menu_action 
            WHERE dashboard_menu_action_id = NEW.dashboard_menu_action_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_dashboard_menu_action_audit_update
          AFTER UPDATE ON security_group_dashboard_menu_action
          FOR EACH ROW BEGIN
          INSERT INTO security_group_dashboard_menu_action_aud
            SELECT now(6), \'UPDATE\', security_group_dashboard_menu_action.* 
            FROM security_group_dashboard_menu_action 
            WHERE dashboard_menu_action_id = NEW.dashboard_menu_action_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_dashboard_menu_action_audit_delete
          BEFORE DELETE ON security_group_dashboard_menu_action
          FOR EACH ROW BEGIN
          INSERT INTO security_group_dashboard_menu_action_aud
            SELECT now(6), \'DELETE\', security_group_dashboard_menu_action.* 
            FROM security_group_dashboard_menu_action 
            WHERE dashboard_menu_action_id = OLD.dashboard_menu_action_id AND security_group_id = OLD.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_dashboard_menu_action_group_audit_insert
          AFTER INSERT ON security_group_dashboard_menu_action_group
          FOR EACH ROW BEGIN
          INSERT INTO security_group_dashboard_menu_action_group_aud
            SELECT now(6), \'INSERT\', security_group_dashboard_menu_action_group.* 
            FROM security_group_dashboard_menu_action_group 
            WHERE dashboard_menu_action_group_id = NEW.dashboard_menu_action_group_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_dashboard_menu_action_group_audit_update
          AFTER UPDATE ON security_group_dashboard_menu_action_group
          FOR EACH ROW BEGIN
          INSERT INTO security_group_dashboard_menu_action_group_aud
            SELECT now(6), \'UPDATE\', security_group_dashboard_menu_action_group.* 
            FROM security_group_dashboard_menu_action_group 
            WHERE dashboard_menu_action_group_id = NEW.dashboard_menu_action_group_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_dashboard_menu_action_group_audit_delete
          BEFORE DELETE ON security_group_dashboard_menu_action_group
          FOR EACH ROW BEGIN
          INSERT INTO security_group_dashboard_menu_action_group_aud
            SELECT now(6), \'DELETE\', security_group_dashboard_menu_action_group.* 
            FROM security_group_dashboard_menu_action_group 
            WHERE dashboard_menu_action_group_id = OLD.dashboard_menu_action_group_id AND security_group_id = OLD.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_dashboard_property_audit_insert
          AFTER INSERT ON security_group_dashboard_property
          FOR EACH ROW BEGIN
          INSERT INTO security_group_dashboard_property_aud
            SELECT now(6), \'INSERT\', security_group_dashboard_property.* 
            FROM security_group_dashboard_property 
            WHERE dashboard_property_id = NEW.dashboard_property_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_dashboard_property_audit_update
          AFTER UPDATE ON security_group_dashboard_property
          FOR EACH ROW BEGIN
          INSERT INTO security_group_dashboard_property_aud
            SELECT now(6), \'UPDATE\', security_group_dashboard_property.* 
            FROM security_group_dashboard_property 
            WHERE dashboard_property_id = NEW.dashboard_property_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_dashboard_property_audit_delete
          BEFORE DELETE ON security_group_dashboard_property
          FOR EACH ROW BEGIN
          INSERT INTO security_group_dashboard_property_aud
            SELECT now(6), \'DELETE\', security_group_dashboard_property.* 
            FROM security_group_dashboard_property 
            WHERE dashboard_property_id = OLD.dashboard_property_id AND security_group_id = OLD.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_external_object_link_audit_insert
          AFTER INSERT ON security_group_external_object_link
          FOR EACH ROW BEGIN
          INSERT INTO security_group_external_object_link_aud
            SELECT now(6), \'INSERT\', security_group_external_object_link.* 
            FROM security_group_external_object_link 
            WHERE external_object_link_id = NEW.external_object_link_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_external_object_link_audit_update
          AFTER UPDATE ON security_group_external_object_link
          FOR EACH ROW BEGIN
          INSERT INTO security_group_external_object_link_aud
            SELECT now(6), \'UPDATE\', security_group_external_object_link.* 
            FROM security_group_external_object_link 
            WHERE external_object_link_id = NEW.external_object_link_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_external_object_link_audit_delete
          BEFORE DELETE ON security_group_external_object_link
          FOR EACH ROW BEGIN
          INSERT INTO security_group_external_object_link_aud
            SELECT now(6), \'DELETE\', security_group_external_object_link.* 
            FROM security_group_external_object_link 
            WHERE external_object_link_id = OLD.external_object_link_id AND security_group_id = OLD.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_filter_audit_insert
          AFTER INSERT ON security_group_filter
          FOR EACH ROW BEGIN
          INSERT INTO security_group_filter_aud
            SELECT now(6), \'INSERT\', security_group_filter.* 
            FROM security_group_filter 
            WHERE filter_id = NEW.filter_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_filter_audit_update
          AFTER UPDATE ON security_group_filter
          FOR EACH ROW BEGIN
          INSERT INTO security_group_filter_aud
            SELECT now(6), \'UPDATE\', security_group_filter.* 
            FROM security_group_filter 
            WHERE filter_id = NEW.filter_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_filter_audit_delete
          BEFORE DELETE ON security_group_filter
          FOR EACH ROW BEGIN
          INSERT INTO security_group_filter_aud
            SELECT now(6), \'DELETE\', security_group_filter.* 
            FROM security_group_filter 
            WHERE filter_id = OLD.filter_id AND security_group_id = OLD.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_filter_field_audit_insert
          AFTER INSERT ON security_group_filter_field
          FOR EACH ROW BEGIN
          INSERT INTO security_group_filter_field_aud
            SELECT now(6), \'INSERT\', security_group_filter_field.* 
            FROM security_group_filter_field 
            WHERE filter_field_id = NEW.filter_field_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_filter_field_audit_update
          AFTER UPDATE ON security_group_filter_field
          FOR EACH ROW BEGIN
          INSERT INTO security_group_filter_field_aud
            SELECT now(6), \'UPDATE\', security_group_filter_field.* 
            FROM security_group_filter_field 
            WHERE filter_field_id = NEW.filter_field_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_filter_field_audit_delete
          BEFORE DELETE ON security_group_filter_field
          FOR EACH ROW BEGIN
          INSERT INTO security_group_filter_field_aud
            SELECT now(6), \'DELETE\', security_group_filter_field.* 
            FROM security_group_filter_field 
            WHERE filter_field_id = OLD.filter_field_id AND security_group_id = OLD.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_filter_field_group_audit_insert
          AFTER INSERT ON security_group_filter_field_group
          FOR EACH ROW BEGIN
          INSERT INTO security_group_filter_field_group_aud
            SELECT now(6), \'INSERT\', security_group_filter_field_group.* 
            FROM security_group_filter_field_group 
            WHERE filter_field_group_id = NEW.filter_field_group_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_filter_field_group_audit_update
          AFTER UPDATE ON security_group_filter_field_group
          FOR EACH ROW BEGIN
          INSERT INTO security_group_filter_field_group_aud
            SELECT now(6), \'UPDATE\', security_group_filter_field_group.* 
            FROM security_group_filter_field_group 
            WHERE filter_field_group_id = NEW.filter_field_group_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_filter_field_group_audit_delete
          BEFORE DELETE ON security_group_filter_field_group
          FOR EACH ROW BEGIN
          INSERT INTO security_group_filter_field_group_aud
            SELECT now(6), \'DELETE\', security_group_filter_field_group.* 
            FROM security_group_filter_field_group 
            WHERE filter_field_group_id = OLD.filter_field_group_id AND security_group_id = OLD.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_find_search_audit_insert
          AFTER INSERT ON security_group_find_search
          FOR EACH ROW BEGIN
          INSERT INTO security_group_find_search_aud
            SELECT now(6), \'INSERT\', security_group_find_search.* 
            FROM security_group_find_search 
            WHERE find_search_id = NEW.find_search_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_find_search_audit_update
          AFTER UPDATE ON security_group_find_search
          FOR EACH ROW BEGIN
          INSERT INTO security_group_find_search_aud
            SELECT now(6), \'UPDATE\', security_group_find_search.* 
            FROM security_group_find_search 
            WHERE find_search_id = NEW.find_search_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_find_search_audit_delete
          BEFORE DELETE ON security_group_find_search
          FOR EACH ROW BEGIN
          INSERT INTO security_group_find_search_aud
            SELECT now(6), \'DELETE\', security_group_find_search.* 
            FROM security_group_find_search 
            WHERE find_search_id = OLD.find_search_id AND security_group_id = OLD.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_flow_audit_insert
          AFTER INSERT ON security_group_flow
          FOR EACH ROW BEGIN
          INSERT INTO security_group_flow_aud
            SELECT now(6), \'INSERT\', security_group_flow.* 
            FROM security_group_flow 
            WHERE flow_id = NEW.flow_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_flow_audit_update
          AFTER UPDATE ON security_group_flow
          FOR EACH ROW BEGIN
          INSERT INTO security_group_flow_aud
            SELECT now(6), \'UPDATE\', security_group_flow.* 
            FROM security_group_flow 
            WHERE flow_id = NEW.flow_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_flow_audit_delete
          BEFORE DELETE ON security_group_flow
          FOR EACH ROW BEGIN
          INSERT INTO security_group_flow_aud
            SELECT now(6), \'DELETE\', security_group_flow.* 
            FROM security_group_flow 
            WHERE flow_id = OLD.flow_id AND security_group_id = OLD.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_flow_action_audit_insert
          AFTER INSERT ON security_group_flow_action
          FOR EACH ROW BEGIN
          INSERT INTO security_group_flow_action_aud
            SELECT now(6), \'INSERT\', security_group_flow_action.* 
            FROM security_group_flow_action 
            WHERE flow_action_id = NEW.flow_action_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_flow_action_audit_update
          AFTER UPDATE ON security_group_flow_action
          FOR EACH ROW BEGIN
          INSERT INTO security_group_flow_action_aud
            SELECT now(6), \'UPDATE\', security_group_flow_action.* 
            FROM security_group_flow_action 
            WHERE flow_action_id = NEW.flow_action_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_flow_action_audit_delete
          BEFORE DELETE ON security_group_flow_action
          FOR EACH ROW BEGIN
          INSERT INTO security_group_flow_action_aud
            SELECT now(6), \'DELETE\', security_group_flow_action.* 
            FROM security_group_flow_action 
            WHERE flow_action_id = OLD.flow_action_id AND security_group_id = OLD.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_flow_field_audit_insert
          AFTER INSERT ON security_group_flow_field
          FOR EACH ROW BEGIN
          INSERT INTO security_group_flow_field_aud
            SELECT now(6), \'INSERT\', security_group_flow_field.* 
            FROM security_group_flow_field 
            WHERE flow_field_id = NEW.flow_field_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_flow_field_audit_update
          AFTER UPDATE ON security_group_flow_field
          FOR EACH ROW BEGIN
          INSERT INTO security_group_flow_field_aud
            SELECT now(6), \'UPDATE\', security_group_flow_field.* 
            FROM security_group_flow_field 
            WHERE flow_field_id = NEW.flow_field_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_flow_field_audit_delete
          BEFORE DELETE ON security_group_flow_field
          FOR EACH ROW BEGIN
          INSERT INTO security_group_flow_field_aud
            SELECT now(6), \'DELETE\', security_group_flow_field.* 
            FROM security_group_flow_field 
            WHERE flow_field_id = OLD.flow_field_id AND security_group_id = OLD.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_flow_step_link_audit_insert
          AFTER INSERT ON security_group_flow_step_link
          FOR EACH ROW BEGIN
          INSERT INTO security_group_flow_step_link_aud
            SELECT now(6), \'INSERT\', security_group_flow_step_link.* 
            FROM security_group_flow_step_link 
            WHERE flow_step_link_id = NEW.flow_step_link_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_flow_step_link_audit_update
          AFTER UPDATE ON security_group_flow_step_link
          FOR EACH ROW BEGIN
          INSERT INTO security_group_flow_step_link_aud
            SELECT now(6), \'UPDATE\', security_group_flow_step_link.* 
            FROM security_group_flow_step_link 
            WHERE flow_step_link_id = NEW.flow_step_link_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_flow_step_link_audit_delete
          BEFORE DELETE ON security_group_flow_step_link
          FOR EACH ROW BEGIN
          INSERT INTO security_group_flow_step_link_aud
            SELECT now(6), \'DELETE\', security_group_flow_step_link.* 
            FROM security_group_flow_step_link 
            WHERE flow_step_link_id = OLD.flow_step_link_id AND security_group_id = OLD.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_flow_step_property_audit_insert
          AFTER INSERT ON security_group_flow_step_property
          FOR EACH ROW BEGIN
          INSERT INTO security_group_flow_step_property_aud
            SELECT now(6), \'INSERT\', security_group_flow_step_property.* 
            FROM security_group_flow_step_property 
            WHERE flow_step_property_id = NEW.flow_step_property_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_flow_step_property_audit_update
          AFTER UPDATE ON security_group_flow_step_property
          FOR EACH ROW BEGIN
          INSERT INTO security_group_flow_step_property_aud
            SELECT now(6), \'UPDATE\', security_group_flow_step_property.* 
            FROM security_group_flow_step_property 
            WHERE flow_step_property_id = NEW.flow_step_property_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_flow_step_property_audit_delete
          BEFORE DELETE ON security_group_flow_step_property
          FOR EACH ROW BEGIN
          INSERT INTO security_group_flow_step_property_aud
            SELECT now(6), \'DELETE\', security_group_flow_step_property.* 
            FROM security_group_flow_step_property 
            WHERE flow_step_property_id = OLD.flow_step_property_id AND security_group_id = OLD.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_grid_panel_audit_insert
          AFTER INSERT ON security_group_grid_panel
          FOR EACH ROW BEGIN
          INSERT INTO security_group_grid_panel_aud
            SELECT now(6), \'INSERT\', security_group_grid_panel.* 
            FROM security_group_grid_panel 
            WHERE grid_panel_id = NEW.grid_panel_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_grid_panel_audit_update
          AFTER UPDATE ON security_group_grid_panel
          FOR EACH ROW BEGIN
          INSERT INTO security_group_grid_panel_aud
            SELECT now(6), \'UPDATE\', security_group_grid_panel.* 
            FROM security_group_grid_panel 
            WHERE grid_panel_id = NEW.grid_panel_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_grid_panel_audit_delete
          BEFORE DELETE ON security_group_grid_panel
          FOR EACH ROW BEGIN
          INSERT INTO security_group_grid_panel_aud
            SELECT now(6), \'DELETE\', security_group_grid_panel.* 
            FROM security_group_grid_panel 
            WHERE grid_panel_id = OLD.grid_panel_id AND security_group_id = OLD.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_grid_template_audit_insert
          AFTER INSERT ON security_group_grid_template
          FOR EACH ROW BEGIN
          INSERT INTO security_group_grid_template_aud
            SELECT now(6), \'INSERT\', security_group_grid_template.* 
            FROM security_group_grid_template 
            WHERE grid_template_id = NEW.grid_template_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_grid_template_audit_update
          AFTER UPDATE ON security_group_grid_template
          FOR EACH ROW BEGIN
          INSERT INTO security_group_grid_template_aud
            SELECT now(6), \'UPDATE\', security_group_grid_template.* 
            FROM security_group_grid_template 
            WHERE grid_template_id = NEW.grid_template_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_grid_template_audit_delete
          BEFORE DELETE ON security_group_grid_template
          FOR EACH ROW BEGIN
          INSERT INTO security_group_grid_template_aud
            SELECT now(6), \'DELETE\', security_group_grid_template.* 
            FROM security_group_grid_template 
            WHERE grid_template_id = OLD.grid_template_id AND security_group_id = OLD.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_list_cell_link_audit_insert
          AFTER INSERT ON security_group_list_cell_link
          FOR EACH ROW BEGIN
          INSERT INTO security_group_list_cell_link_aud
            SELECT now(6), \'INSERT\', security_group_list_cell_link.* 
            FROM security_group_list_cell_link 
            WHERE list_cell_link_id = NEW.list_cell_link_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_list_cell_link_audit_update
          AFTER UPDATE ON security_group_list_cell_link
          FOR EACH ROW BEGIN
          INSERT INTO security_group_list_cell_link_aud
            SELECT now(6), \'UPDATE\', security_group_list_cell_link.* 
            FROM security_group_list_cell_link 
            WHERE list_cell_link_id = NEW.list_cell_link_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_list_cell_link_audit_delete
          BEFORE DELETE ON security_group_list_cell_link
          FOR EACH ROW BEGIN
          INSERT INTO security_group_list_cell_link_aud
            SELECT now(6), \'DELETE\', security_group_list_cell_link.* 
            FROM security_group_list_cell_link 
            WHERE list_cell_link_id = OLD.list_cell_link_id AND security_group_id = OLD.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_list_audit_insert
          AFTER INSERT ON security_group_list
          FOR EACH ROW BEGIN
          INSERT INTO security_group_list_aud
            SELECT now(6), \'INSERT\', security_group_list.* 
            FROM security_group_list 
            WHERE list_id = NEW.list_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_list_audit_update
          AFTER UPDATE ON security_group_list
          FOR EACH ROW BEGIN
          INSERT INTO security_group_list_aud
            SELECT now(6), \'UPDATE\', security_group_list.* 
            FROM security_group_list 
            WHERE list_id = NEW.list_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_list_audit_delete
          BEFORE DELETE ON security_group_list
          FOR EACH ROW BEGIN
          INSERT INTO security_group_list_aud
            SELECT now(6), \'DELETE\', security_group_list.* 
            FROM security_group_list 
            WHERE list_id = OLD.list_id AND security_group_id = OLD.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_list_row_action_audit_insert
          AFTER INSERT ON security_group_list_row_action
          FOR EACH ROW BEGIN
          INSERT INTO security_group_list_row_action_aud
            SELECT now(6), \'INSERT\', security_group_list_row_action.* 
            FROM security_group_list_row_action 
            WHERE list_row_action_id = NEW.list_row_action_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_list_row_action_audit_update
          AFTER UPDATE ON security_group_list_row_action
          FOR EACH ROW BEGIN
          INSERT INTO security_group_list_row_action_aud
            SELECT now(6), \'UPDATE\', security_group_list_row_action.* 
            FROM security_group_list_row_action 
            WHERE list_row_action_id = NEW.list_row_action_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_list_row_action_audit_delete
          BEFORE DELETE ON security_group_list_row_action
          FOR EACH ROW BEGIN
          INSERT INTO security_group_list_row_action_aud
            SELECT now(6), \'DELETE\', security_group_list_row_action.* 
            FROM security_group_list_row_action 
            WHERE list_row_action_id = OLD.list_row_action_id AND security_group_id = OLD.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_list_sorting_option_audit_insert
          AFTER INSERT ON security_group_list_sorting_option
          FOR EACH ROW BEGIN
          INSERT INTO security_group_list_sorting_option_aud
            SELECT now(6), \'INSERT\', security_group_list_sorting_option.* 
            FROM security_group_list_sorting_option 
            WHERE list_sorting_option_id = NEW.list_sorting_option_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_list_sorting_option_audit_update
          AFTER UPDATE ON security_group_list_sorting_option
          FOR EACH ROW BEGIN
          INSERT INTO security_group_list_sorting_option_aud
            SELECT now(6), \'UPDATE\', security_group_list_sorting_option.* 
            FROM security_group_list_sorting_option 
            WHERE list_sorting_option_id = NEW.list_sorting_option_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_list_sorting_option_audit_delete
          BEFORE DELETE ON security_group_list_sorting_option
          FOR EACH ROW BEGIN
          INSERT INTO security_group_list_sorting_option_aud
            SELECT now(6), \'DELETE\', security_group_list_sorting_option.* 
            FROM security_group_list_sorting_option 
            WHERE list_sorting_option_id = OLD.list_sorting_option_id AND security_group_id = OLD.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_list_top_action_audit_insert
          AFTER INSERT ON security_group_list_top_action
          FOR EACH ROW BEGIN
          INSERT INTO security_group_list_top_action_aud
            SELECT now(6), \'INSERT\', security_group_list_top_action.* 
            FROM security_group_list_top_action 
            WHERE list_top_action_id = NEW.list_top_action_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_list_top_action_audit_update
          AFTER UPDATE ON security_group_list_top_action
          FOR EACH ROW BEGIN
          INSERT INTO security_group_list_top_action_aud
            SELECT now(6), \'UPDATE\', security_group_list_top_action.* 
            FROM security_group_list_top_action 
            WHERE list_top_action_id = NEW.list_top_action_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_list_top_action_audit_delete
          BEFORE DELETE ON security_group_list_top_action
          FOR EACH ROW BEGIN
          INSERT INTO security_group_list_top_action_aud
            SELECT now(6), \'DELETE\', security_group_list_top_action.* 
            FROM security_group_list_top_action 
            WHERE list_top_action_id = OLD.list_top_action_id AND security_group_id = OLD.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_list_top_bar_audit_insert
          AFTER INSERT ON security_group_list_top_bar
          FOR EACH ROW BEGIN
          INSERT INTO security_group_list_top_bar_aud
            SELECT now(6), \'INSERT\', security_group_list_top_bar.* 
            FROM security_group_list_top_bar 
            WHERE list_top_bar_id = NEW.list_top_bar_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_list_top_bar_audit_update
          AFTER UPDATE ON security_group_list_top_bar
          FOR EACH ROW BEGIN
          INSERT INTO security_group_list_top_bar_aud
            SELECT now(6), \'UPDATE\', security_group_list_top_bar.* 
            FROM security_group_list_top_bar 
            WHERE list_top_bar_id = NEW.list_top_bar_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_list_top_bar_audit_delete
          BEFORE DELETE ON security_group_list_top_bar
          FOR EACH ROW BEGIN
          INSERT INTO security_group_list_top_bar_aud
            SELECT now(6), \'DELETE\', security_group_list_top_bar.* 
            FROM security_group_list_top_bar 
            WHERE list_top_bar_id = OLD.list_top_bar_id AND security_group_id = OLD.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_menu_audit_insert
          AFTER INSERT ON security_group_menu
          FOR EACH ROW BEGIN
          INSERT INTO security_group_menu_aud
            SELECT now(6), \'INSERT\', security_group_menu.* 
            FROM security_group_menu 
            WHERE menu_id = NEW.menu_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_menu_audit_update
          AFTER UPDATE ON security_group_menu
          FOR EACH ROW BEGIN
          INSERT INTO security_group_menu_aud
            SELECT now(6), \'UPDATE\', security_group_menu.* 
            FROM security_group_menu 
            WHERE menu_id = NEW.menu_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_menu_audit_delete
          BEFORE DELETE ON security_group_menu
          FOR EACH ROW BEGIN
          INSERT INTO security_group_menu_aud
            SELECT now(6), \'DELETE\', security_group_menu.* 
            FROM security_group_menu 
            WHERE menu_id = OLD.menu_id AND security_group_id = OLD.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_security_group_api_audit_insert
          AFTER INSERT ON security_group_security_group_api
          FOR EACH ROW BEGIN
          INSERT INTO security_group_security_group_api_aud
            SELECT now(6), \'INSERT\', security_group_security_group_api.* 
            FROM security_group_security_group_api 
            WHERE security_group_api_id = NEW.security_group_api_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_security_group_api_audit_update
          AFTER UPDATE ON security_group_security_group_api
          FOR EACH ROW BEGIN
          INSERT INTO security_group_security_group_api_aud
            SELECT now(6), \'UPDATE\', security_group_security_group_api.* 
            FROM security_group_security_group_api 
            WHERE security_group_api_id = NEW.security_group_api_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_security_group_api_audit_delete
          BEFORE DELETE ON security_group_security_group_api
          FOR EACH ROW BEGIN
          INSERT INTO security_group_security_group_api_aud
            SELECT now(6), \'DELETE\', security_group_security_group_api.* 
            FROM security_group_security_group_api 
            WHERE security_group_api_id = OLD.security_group_api_id AND security_group_id = OLD.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_validator_audit_insert
          AFTER INSERT ON security_group_validator
          FOR EACH ROW BEGIN
          INSERT INTO security_group_validator_aud
            SELECT now(6), \'INSERT\', security_group_validator.* 
            FROM security_group_validator 
            WHERE validator_id = NEW.validator_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_validator_audit_update
          AFTER UPDATE ON security_group_validator
          FOR EACH ROW BEGIN
          INSERT INTO security_group_validator_aud
            SELECT now(6), \'UPDATE\', security_group_validator.* 
            FROM security_group_validator 
            WHERE validator_id = NEW.validator_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_validator_audit_delete
          BEFORE DELETE ON security_group_validator
          FOR EACH ROW BEGIN
          INSERT INTO security_group_validator_aud
            SELECT now(6), \'DELETE\', security_group_validator.* 
            FROM security_group_validator 
            WHERE validator_id = OLD.validator_id AND security_group_id = OLD.security_group_id;
        END;');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE security_group_conditional_message');
        $this->addSql('DROP TABLE security_group_dashboard');
        $this->addSql('DROP TABLE security_group_dashboard_menu');
        $this->addSql('DROP TABLE security_group_dashboard_menu_action');
        $this->addSql('DROP TABLE security_group_dashboard_menu_action_group');
        $this->addSql('DROP TABLE security_group_dashboard_property');
        $this->addSql('DROP TABLE security_group_external_object_link');
        $this->addSql('DROP TABLE security_group_filter');
        $this->addSql('DROP TABLE security_group_filter_field');
        $this->addSql('DROP TABLE security_group_filter_field_group');
        $this->addSql('DROP TABLE security_group_find_search');
        $this->addSql('DROP TABLE security_group_flow');
        $this->addSql('DROP TABLE security_group_flow_action');
        $this->addSql('DROP TABLE security_group_flow_field');
        $this->addSql('DROP TABLE security_group_flow_step_link');
        $this->addSql('DROP TABLE security_group_flow_step_property');
        $this->addSql('DROP TABLE security_group_grid_panel');
        $this->addSql('DROP TABLE security_group_grid_template');
        $this->addSql('DROP TABLE security_group_list_cell_link');
        $this->addSql('DROP TABLE security_group_list');
        $this->addSql('DROP TABLE security_group_list_row_action');
        $this->addSql('DROP TABLE security_group_list_sorting_option');
        $this->addSql('DROP TABLE security_group_list_top_action');
        $this->addSql('DROP TABLE security_group_list_top_bar');
        $this->addSql('DROP TABLE security_group_menu');
        $this->addSql('DROP TABLE security_group_security_group_api');
        $this->addSql('DROP TABLE security_group_validator');
        $this->addSql('DROP TABLE security_group_conditional_message_aud');
        $this->addSql('DROP TABLE security_group_dashboard_aud');
        $this->addSql('DROP TABLE security_group_dashboard_menu_aud');
        $this->addSql('DROP TABLE security_group_dashboard_menu_action_aud');
        $this->addSql('DROP TABLE security_group_dashboard_menu_action_group_aud');
        $this->addSql('DROP TABLE security_group_dashboard_property_aud');
        $this->addSql('DROP TABLE security_group_external_object_link_aud');
        $this->addSql('DROP TABLE security_group_filter_aud');
        $this->addSql('DROP TABLE security_group_filter_field_aud');
        $this->addSql('DROP TABLE security_group_filter_field_group_aud');
        $this->addSql('DROP TABLE security_group_find_search_aud');
        $this->addSql('DROP TABLE security_group_flow_aud');
        $this->addSql('DROP TABLE security_group_flow_action_aud');
        $this->addSql('DROP TABLE security_group_flow_field_aud');
        $this->addSql('DROP TABLE security_group_flow_step_link_aud');
        $this->addSql('DROP TABLE security_group_flow_step_property_aud');
        $this->addSql('DROP TABLE security_group_grid_panel_aud');
        $this->addSql('DROP TABLE security_group_grid_template_aud');
        $this->addSql('DROP TABLE security_group_list_cell_link_aud');
        $this->addSql('DROP TABLE security_group_list_aud');
        $this->addSql('DROP TABLE security_group_list_row_action_aud');
        $this->addSql('DROP TABLE security_group_list_sorting_option_aud');
        $this->addSql('DROP TABLE security_group_list_top_action_aud');
        $this->addSql('DROP TABLE security_group_list_top_bar_aud');
        $this->addSql('DROP TABLE security_group_menu_aud');
        $this->addSql('DROP TABLE security_group_security_group_api_aud');
        $this->addSql('DROP TABLE security_group_validator_aud');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_conditional_message_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_conditional_message_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_conditional_message_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_dashboard_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_dashboard_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_dashboard_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_dashboard_menu_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_dashboard_menu_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_dashboard_menu_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_dashboard_menu_action_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_dashboard_menu_action_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_dashboard_menu_action_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_dashboard_menu_action_group_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_dashboard_menu_action_group_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_dashboard_menu_action_group_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_dashboard_property_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_dashboard_property_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_dashboard_property_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_external_object_link_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_external_object_link_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_external_object_link_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_filter_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_filter_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_filter_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_filter_field_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_filter_field_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_filter_field_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_filter_field_group_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_filter_field_group_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_filter_field_group_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_find_search_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_find_search_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_find_search_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_flow_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_flow_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_flow_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_flow_action_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_flow_action_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_flow_action_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_flow_field_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_flow_field_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_flow_field_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_flow_step_link_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_flow_step_link_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_flow_step_link_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_flow_step_property_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_flow_step_property_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_flow_step_property_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_grid_panel_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_grid_panel_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_grid_panel_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_grid_template_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_grid_template_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_grid_template_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_list_cell_link_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_list_cell_link_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_list_cell_link_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_list_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_list_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_list_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_list_row_action_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_list_row_action_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_list_row_action_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_list_sorting_option_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_list_sorting_option_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_list_sorting_option_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_list_top_action_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_list_top_action_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_list_top_action_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_list_top_bar_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_list_top_bar_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_list_top_bar_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_menu_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_menu_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_menu_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_security_group_api_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_security_group_api_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_security_group_api_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_validator_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_validator_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_validator_audit_delete;');
    }
}
