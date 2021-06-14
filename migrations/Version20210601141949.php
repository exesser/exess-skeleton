<?php

declare(strict_types=1);
// phpcs:ignoreFile

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210601141949 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE dash_dashboard_dash_dashboardproperties_c');
        $this->addSql('DROP TABLE dash_dashboard_dash_dashboardproperties_c_aud');
        $this->addSql('CREATE TABLE dash_dashboard_dash_dashboardproperties_c (dashboard_id CHAR(36) NOT NULL, property_id CHAR(36) NOT NULL, INDEX IDX_4B916157B9D04D2B (dashboard_id), INDEX IDX_4B916157549213EC (property_id), PRIMARY KEY(dashboard_id, property_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_property (property_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX IDX_7844847C549213EC (property_id), INDEX IDX_7844847C9D3F5E95 (security_group_id), PRIMARY KEY(property_id, security_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dash_dashboard_dash_dashboardproperties_c_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', dashboard_id CHAR(36) NOT NULL, property_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, dashboard_id, property_id), PRIMARY KEY(audit_timestamp, dashboard_id, property_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_group_property_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', property_id CHAR(36) NOT NULL, security_group_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, property_id, security_group_id), PRIMARY KEY(audit_timestamp, property_id, security_group_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE dash_dashboard_dash_dashboardproperties_c ADD CONSTRAINT FK_4B916157B9D04D2B FOREIGN KEY (dashboard_id) REFERENCES dash_dashboard (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dash_dashboard_dash_dashboardproperties_c ADD CONSTRAINT FK_4B916157549213EC FOREIGN KEY (property_id) REFERENCES properties (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_property ADD CONSTRAINT FK_7844847C549213EC FOREIGN KEY (property_id) REFERENCES properties (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_property ADD CONSTRAINT FK_7844847C9D3F5E95 FOREIGN KEY (security_group_id) REFERENCES securitygroups (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE security_group_dashboard_property');
        $this->addSql('DROP TABLE security_group_dashboard_property_aud');
        $this->addSql('DROP TABLE security_group_flow_step_property');
        $this->addSql('DROP TABLE security_group_flow_step_property_aud');
        $this->addSql('DROP TRIGGER IF EXISTS dash_dashboardproperties_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS dash_dashboardproperties_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS dash_dashboardproperties_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_dashboard_property_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_dashboard_property_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_dashboard_property_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_flow_step_property_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_flow_step_property_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_flow_step_property_audit_delete;');
        $this->addSql('CREATE TRIGGER dash_dashboard_dash_dashboardproperties_c_audit_insert
          AFTER INSERT ON dash_dashboard_dash_dashboardproperties_c
          FOR EACH ROW BEGIN
          INSERT INTO dash_dashboard_dash_dashboardproperties_c_aud
            SELECT now(6), \'INSERT\', dash_dashboard_dash_dashboardproperties_c.* 
            FROM dash_dashboard_dash_dashboardproperties_c 
            WHERE dashboard_id = NEW.dashboard_id AND property_id = NEW.property_id;
        END;');
        $this->addSql('CREATE TRIGGER dash_dashboard_dash_dashboardproperties_c_audit_update
          AFTER UPDATE ON dash_dashboard_dash_dashboardproperties_c
          FOR EACH ROW BEGIN
          INSERT INTO dash_dashboard_dash_dashboardproperties_c_aud
            SELECT now(6), \'UPDATE\', dash_dashboard_dash_dashboardproperties_c.* 
            FROM dash_dashboard_dash_dashboardproperties_c 
            WHERE dashboard_id = NEW.dashboard_id AND property_id = NEW.property_id;
        END;');
        $this->addSql('CREATE TRIGGER dash_dashboard_dash_dashboardproperties_c_audit_delete
          BEFORE DELETE ON dash_dashboard_dash_dashboardproperties_c
          FOR EACH ROW BEGIN
          INSERT INTO dash_dashboard_dash_dashboardproperties_c_aud
            SELECT now(6), \'DELETE\', dash_dashboard_dash_dashboardproperties_c.* 
            FROM dash_dashboard_dash_dashboardproperties_c 
            WHERE dashboard_id = OLD.dashboard_id AND property_id = OLD.property_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_property_audit_insert
          AFTER INSERT ON security_group_property
          FOR EACH ROW BEGIN
          INSERT INTO security_group_property_aud
            SELECT now(6), \'INSERT\', security_group_property.* 
            FROM security_group_property 
            WHERE property_id = NEW.property_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_property_audit_update
          AFTER UPDATE ON security_group_property
          FOR EACH ROW BEGIN
          INSERT INTO security_group_property_aud
            SELECT now(6), \'UPDATE\', security_group_property.* 
            FROM security_group_property 
            WHERE property_id = NEW.property_id AND security_group_id = NEW.security_group_id;
        END;');
        $this->addSql('CREATE TRIGGER security_group_property_audit_delete
          BEFORE DELETE ON security_group_property
          FOR EACH ROW BEGIN
          INSERT INTO security_group_property_aud
            SELECT now(6), \'DELETE\', security_group_property.* 
            FROM security_group_property 
            WHERE property_id = OLD.property_id AND security_group_id = OLD.security_group_id;
        END;');
        $this->addSql('DROP TABLE dash_dashboardproperties');
        $this->addSql('DROP TABLE dash_dashboardproperties_aud');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE dash_dashboardproperties (id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_by CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, modified_user_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, value_c VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, description TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX fk_users_id_1f405dcb (modified_user_id), INDEX fk_users_id_2aa901d0 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE dash_dashboardproperties_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_by CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, modified_user_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, value_c VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, description TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE security_group_dashboard_property (dashboard_property_id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, security_group_id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_577361D79D3F5E95 (security_group_id), INDEX IDX_577361D7EB330A3 (dashboard_property_id), PRIMARY KEY(dashboard_property_id, security_group_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE security_group_dashboard_property_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:enum_audit_operation)\', dashboard_property_id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, security_group_id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX idx_operation_id (audit_operation, dashboard_property_id, security_group_id), PRIMARY KEY(audit_timestamp, dashboard_property_id, security_group_id, audit_operation)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE security_group_flow_step_property (property_id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, security_group_id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_1E54593A9D3F5E95 (security_group_id), INDEX IDX_1E54593A549213EC (property_id), PRIMARY KEY(property_id, security_group_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE security_group_flow_step_property_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:enum_audit_operation)\', property_id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, security_group_id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX idx_operation_id (audit_operation, property_id, security_group_id), PRIMARY KEY(audit_timestamp, property_id, security_group_id, audit_operation)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE dash_dashboardproperties ADD CONSTRAINT FK_EB15E2C2BAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE dash_dashboardproperties ADD CONSTRAINT FK_EB15E2C2DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE security_group_dashboard_property ADD CONSTRAINT FK_577361D79D3F5E95 FOREIGN KEY (security_group_id) REFERENCES securitygroups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_dashboard_property ADD CONSTRAINT FK_577361D7EB330A3 FOREIGN KEY (dashboard_property_id) REFERENCES dash_dashboardproperties (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_flow_step_property ADD CONSTRAINT FK_1E54593A549213EC FOREIGN KEY (property_id) REFERENCES properties (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_group_flow_step_property ADD CONSTRAINT FK_1E54593A9D3F5E95 FOREIGN KEY (security_group_id) REFERENCES securitygroups (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE dash_dashboard_dash_dashboardproperties_c');
        $this->addSql('DROP TABLE security_group_property');
        $this->addSql('DROP TABLE dash_dashboard_dash_dashboardproperties_c_aud');
        $this->addSql('DROP TABLE security_group_property_aud');
        $this->addSql('DROP TRIGGER IF EXISTS dash_dashboard_dash_dashboardproperties_c_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS dash_dashboard_dash_dashboardproperties_c_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS dash_dashboard_dash_dashboardproperties_c_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_property_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_property_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_property_audit_delete;');
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
        $this->addSql('CREATE TRIGGER security_group_flow_step_property_audit_insert
          AFTER INSERT ON security_group_flow_step_property
          FOR EACH ROW BEGIN
                  INSERT INTO security_group_flow_step_property_aud
                    SELECT now(6), \'INSERT\', security_group_flow_step_property.* 
                    FROM security_group_flow_step_property 
                    WHERE property_id = NEW.property_id AND security_group_id = NEW.security_group_id;
                END;');
        $this->addSql('CREATE TRIGGER security_group_flow_step_property_audit_update
          AFTER UPDATE ON security_group_flow_step_property
          FOR EACH ROW BEGIN
                  INSERT INTO security_group_flow_step_property_aud
                    SELECT now(6), \'UPDATE\', security_group_flow_step_property.* 
                    FROM security_group_flow_step_property 
                    WHERE property_id = NEW.property_id AND security_group_id = NEW.security_group_id;
                END;');
        $this->addSql('CREATE TRIGGER security_group_flow_step_property_audit_delete
          BEFORE DELETE ON security_group_flow_step_property
          FOR EACH ROW BEGIN
                  INSERT INTO security_group_flow_step_property_aud
                    SELECT now(6), \'DELETE\', security_group_flow_step_property.* 
                    FROM security_group_flow_step_property 
                    WHERE property_id = OLD.property_id AND security_group_id = OLD.security_group_id;
                END;');
    }
}
