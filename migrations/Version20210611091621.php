<?php

declare(strict_types=1);
// phpcs:ignoreFile

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210611091621 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE acl_actions ADD description TEXT DEFAULT NULL, CHANGE name name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE acl_actions ADD CONSTRAINT FK_6E78FA2CDE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE acl_actions ADD CONSTRAINT FK_6E78FA2CBAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_6E78FA2CDE12AB56 ON acl_actions (created_by)');
        $this->addSql('CREATE INDEX IDX_6E78FA2CBAA24139 ON acl_actions (modified_user_id)');
        $this->addSql('ALTER TABLE acl_roles CHANGE name name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE acl_roles ADD CONSTRAINT FK_32A76378DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE acl_roles ADD CONSTRAINT FK_32A76378BAA24139 FOREIGN KEY (modified_user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_32A76378DE12AB56 ON acl_roles (created_by)');
        $this->addSql('CREATE INDEX IDX_32A76378BAA24139 ON acl_roles (modified_user_id)');
        $this->addSql('ALTER TABLE flw_guidancefields CHANGE field_multiple field_multiple TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE list_dynamic_list CHANGE items_per_page items_per_page INT DEFAULT 10 NOT NULL');
        $this->addSql('ALTER TABLE fe_selectwithsearch CHANGE items_on_page items_on_page INT DEFAULT 50 NOT NULL');
        $this->addSql('ALTER TABLE acl_actions_aud ADD description TEXT DEFAULT NULL, CHANGE name name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE acl_roles_aud CHANGE name name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE flw_guidancefields_aud CHANGE field_multiple field_multiple TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE list_dynamic_list_aud CHANGE items_per_page items_per_page INT DEFAULT NULL');
        $this->addSql('ALTER TABLE fe_selectwithsearch_aud CHANGE items_on_page items_on_page INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE acl_actions DROP FOREIGN KEY FK_6E78FA2CDE12AB56');
        $this->addSql('ALTER TABLE acl_actions DROP FOREIGN KEY FK_6E78FA2CBAA24139');
        $this->addSql('DROP INDEX IDX_6E78FA2CDE12AB56 ON acl_actions');
        $this->addSql('DROP INDEX IDX_6E78FA2CBAA24139 ON acl_actions');
        $this->addSql('ALTER TABLE acl_actions DROP description, CHANGE name name VARCHAR(150) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE acl_actions_aud DROP description, CHANGE name name VARCHAR(150) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE acl_roles DROP FOREIGN KEY FK_32A76378DE12AB56');
        $this->addSql('ALTER TABLE acl_roles DROP FOREIGN KEY FK_32A76378BAA24139');
        $this->addSql('DROP INDEX IDX_32A76378DE12AB56 ON acl_roles');
        $this->addSql('DROP INDEX IDX_32A76378BAA24139 ON acl_roles');
        $this->addSql('ALTER TABLE acl_roles CHANGE name name VARCHAR(150) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE acl_roles_aud CHANGE name name VARCHAR(150) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE fe_selectwithsearch CHANGE items_on_page items_on_page VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'50\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE fe_selectwithsearch_aud CHANGE items_on_page items_on_page VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE flw_guidancefields CHANGE field_multiple field_multiple ENUM(\'true\') CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:enum_field_multiple)\'');
        $this->addSql('ALTER TABLE flw_guidancefields_aud CHANGE field_multiple field_multiple VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE list_dynamic_list CHANGE items_per_page items_per_page VARCHAR(5) CHARACTER SET utf8mb4 DEFAULT \'10\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE list_dynamic_list_aud CHANGE items_per_page items_per_page VARCHAR(5) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
