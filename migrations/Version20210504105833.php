<?php

declare(strict_types=1);
// phpcs:ignoreFile

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210504105833 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE securitygroups_records');
        $this->addSql('DROP TABLE securitygroups_records_aud');
        $this->addSql('DROP TRIGGER IF EXISTS securitygroups_records_audit_insert;');
        $this->addSql('DROP TRIGGER IF EXISTS securitygroups_records_audit_update;');
        $this->addSql('DROP TRIGGER IF EXISTS securitygroups_records_audit_delete;');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE securitygroups_records (id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, securitygroup_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, record_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, module CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, date_modified DATETIME DEFAULT NULL, modified_user_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created_by CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX idx_securitygroups_records_mod_sec (module, securitygroup_id), INDEX idx_securitygroups_records_del (deleted), INDEX idx_recordid_deleted (record_id, deleted), INDEX idx_securitygroups_records_mod (module, deleted, record_id, securitygroup_id), INDEX fk_securitygroups_id_86a09ddf (securitygroup_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE securitygroups_records_aud (audit_timestamp DATETIME(6) NOT NULL COMMENT \'(DC2Type:datetime_immutable_microseconds)\', audit_operation ENUM(\'DELETE\', \'INSERT\', \'UPDATE\') CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:enum_audit_operation)\', id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, securitygroup_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, record_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, module CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, date_modified DATETIME DEFAULT NULL, modified_user_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created_by CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, deleted TINYINT(1) DEFAULT NULL, INDEX idx_operation_id (audit_operation, id), PRIMARY KEY(audit_timestamp, id, audit_operation)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE securitygroups_records ADD CONSTRAINT FK_A3997AC3E7F73327 FOREIGN KEY (securitygroup_id) REFERENCES securitygroups (id)');
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
    }
}
