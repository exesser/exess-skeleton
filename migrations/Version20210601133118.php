<?php

declare(strict_types=1);
// phpcs:ignoreFile

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210601133118 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE flw_flowsteps_flw_flowstepproperties_1_c');
        $this->addSql('DROP TABLE flw_flowsteps_flw_flowstepproperties_1_c_aud');
        $this->addSql('CREATE TABLE flw_flowsteps_flw_flowstepproperties_1_c (flow_step_id CHAR(36) NOT NULL, property_id CHAR(36) NOT NULL, INDEX IDX_1CE72E483082DA11 (flow_step_id), INDEX IDX_1CE72E48549213EC (property_id), PRIMARY KEY(flow_step_id, property_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE properties (id CHAR(36) NOT NULL, created_by CHAR(36) NOT NULL, modified_user_id CHAR(36) DEFAULT NULL, value_c VARCHAR(255) DEFAULT NULL, date_entered DATETIME NOT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, INDEX fk_users_id_0a713cf0 (modified_user_id), INDEX fk_users_id_96f8b053 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE flw_flowsteps_flw_flowstepproperties_1_c_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', flow_step_id CHAR(36) NOT NULL, property_id CHAR(36) NOT NULL, INDEX idx_operation_id (audit_operation, flow_step_id, property_id), PRIMARY KEY(audit_timestamp, flow_step_id, property_id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE properties_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') NOT NULL COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) NOT NULL, created_by CHAR(36) DEFAULT NULL, modified_user_id CHAR(36) DEFAULT NULL, value_c VARCHAR(255) DEFAULT NULL, date_entered DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE flw_flowsteps_flw_flowstepproperties_1_c ADD CONSTRAINT FK_1CE72E483082DA11 FOREIGN KEY (flow_step_id) REFERENCES flw_flowsteps (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE flw_flowsteps_flw_flowstepproperties_1_c ADD CONSTRAINT FK_1CE72E48549213EC FOREIGN KEY (property_id) REFERENCES properties (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE properties ADD CONSTRAINT FK_87C331C7DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE properties ADD CONSTRAINT FK_87C331C7BAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE security_group_flow_step_property DROP FOREIGN KEY FK_1E54593A1D1C7A89');
        $this->addSql('DROP INDEX IDX_1E54593A1D1C7A89 ON security_group_flow_step_property');
        $this->addSql('ALTER TABLE security_group_flow_step_property DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE security_group_flow_step_property CHANGE flow_step_property_id property_id CHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE security_group_flow_step_property ADD CONSTRAINT FK_1E54593A549213EC FOREIGN KEY (property_id) REFERENCES properties (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_1E54593A549213EC ON security_group_flow_step_property (property_id)');
        $this->addSql('ALTER TABLE security_group_flow_step_property ADD PRIMARY KEY (property_id, security_group_id)');
        $this->addSql('DROP INDEX idx_operation_id ON security_group_flow_step_property_aud');
        $this->addSql('ALTER TABLE security_group_flow_step_property_aud DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE security_group_flow_step_property_aud CHANGE flow_step_property_id property_id CHAR(36) NOT NULL');
        $this->addSql('CREATE INDEX idx_operation_id ON security_group_flow_step_property_aud (audit_operation, property_id, security_group_id)');
        $this->addSql('ALTER TABLE security_group_flow_step_property_aud ADD PRIMARY KEY (audit_timestamp, property_id, security_group_id, audit_operation)');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_flow_step_property_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_flow_step_property_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_flow_step_property_audit_delete;');
        $this->addSql('CREATE TRIGGER flw_flowsteps_flw_flowstepproperties_1_c_audit_insert
          AFTER INSERT ON flw_flowsteps_flw_flowstepproperties_1_c
          FOR EACH ROW BEGIN
          INSERT INTO flw_flowsteps_flw_flowstepproperties_1_c_aud
            SELECT now(6), \'INSERT\', flw_flowsteps_flw_flowstepproperties_1_c.* 
            FROM flw_flowsteps_flw_flowstepproperties_1_c 
            WHERE flow_step_id = NEW.flow_step_id AND property_id = NEW.property_id;
        END;');
        $this->addSql('CREATE TRIGGER flw_flowsteps_flw_flowstepproperties_1_c_audit_update
          AFTER UPDATE ON flw_flowsteps_flw_flowstepproperties_1_c
          FOR EACH ROW BEGIN
          INSERT INTO flw_flowsteps_flw_flowstepproperties_1_c_aud
            SELECT now(6), \'UPDATE\', flw_flowsteps_flw_flowstepproperties_1_c.* 
            FROM flw_flowsteps_flw_flowstepproperties_1_c 
            WHERE flow_step_id = NEW.flow_step_id AND property_id = NEW.property_id;
        END;');
        $this->addSql('CREATE TRIGGER flw_flowsteps_flw_flowstepproperties_1_c_audit_delete
          BEFORE DELETE ON flw_flowsteps_flw_flowstepproperties_1_c
          FOR EACH ROW BEGIN
          INSERT INTO flw_flowsteps_flw_flowstepproperties_1_c_aud
            SELECT now(6), \'DELETE\', flw_flowsteps_flw_flowstepproperties_1_c.* 
            FROM flw_flowsteps_flw_flowstepproperties_1_c 
            WHERE flow_step_id = OLD.flow_step_id AND property_id = OLD.property_id;
        END;');
        $this->addSql('CREATE TRIGGER properties_audit_insert
          AFTER INSERT ON properties
          FOR EACH ROW BEGIN
          INSERT INTO properties_aud
            SELECT now(6), \'INSERT\', properties.* 
            FROM properties 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER properties_audit_update
          AFTER UPDATE ON properties
          FOR EACH ROW BEGIN
          INSERT INTO properties_aud
            SELECT now(6), \'UPDATE\', properties.* 
            FROM properties 
            WHERE id = NEW.id;
        END;');
        $this->addSql('CREATE TRIGGER properties_audit_delete
          BEFORE DELETE ON properties
          FOR EACH ROW BEGIN
          INSERT INTO properties_aud
            SELECT now(6), \'DELETE\', properties.* 
            FROM properties 
            WHERE id = OLD.id;
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
        $this->addSql('DROP TABLE flw_flowstepproperties');
        $this->addSql('DROP TABLE flw_flowstepproperties_aud');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE flw_flowsteps_flw_flowstepproperties_1_c');
        $this->addSql('DROP TABLE properties');
        $this->addSql('DROP TABLE flw_flowsteps_flw_flowstepproperties_1_c_aud');
        $this->addSql('DROP TABLE properties_aud');
        $this->addSql('DROP INDEX IDX_1E54593A549213EC ON security_group_flow_step_property');
        $this->addSql('ALTER TABLE security_group_flow_step_property DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE security_group_flow_step_property CHANGE property_id flow_step_property_id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE security_group_flow_step_property ADD CONSTRAINT FK_1E54593A1D1C7A89 FOREIGN KEY (flow_step_property_id) REFERENCES flw_flowstepproperties (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_1E54593A1D1C7A89 ON security_group_flow_step_property (flow_step_property_id)');
        $this->addSql('ALTER TABLE security_group_flow_step_property ADD PRIMARY KEY (flow_step_property_id, security_group_id)');
        $this->addSql('ALTER TABLE security_group_flow_step_property_aud DROP PRIMARY KEY');
        $this->addSql('DROP INDEX idx_operation_id ON security_group_flow_step_property_aud');
        $this->addSql('ALTER TABLE security_group_flow_step_property_aud CHANGE property_id flow_step_property_id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE security_group_flow_step_property_aud ADD PRIMARY KEY (audit_timestamp, flow_step_property_id, security_group_id, audit_operation)');
        $this->addSql('CREATE INDEX idx_operation_id ON security_group_flow_step_property_aud (audit_operation, flow_step_property_id, security_group_id)');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_flow_step_property_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_flow_step_property_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS security_group_flow_step_property_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS flw_flowsteps_flw_flowstepproperties_1_c_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS flw_flowsteps_flw_flowstepproperties_1_c_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS flw_flowsteps_flw_flowstepproperties_1_c_audit_delete;');
        $this->addSql('DROP TRIGGER IF EXISTS properties_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS properties_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS properties_audit_delete;');
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
    }
}
