<?php declare(strict_types = 1);
// phpcs:ignoreFile

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
* Auto-generated Migration: Please modify to your needs!
*/
final class Version20210622071847 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE list_external_object_linkfields ADD list_id CHAR(36) DEFAULT NULL, ADD entity_name VARCHAR(255) DEFAULT NULL, ADD entity_field VARCHAR(255) DEFAULT NULL, DROP suite_bean_name, DROP suite_bean_field');
        $this->addSql('ALTER TABLE list_external_object_linkfields ADD CONSTRAINT FK_656422843DAE168B FOREIGN KEY (list_id) REFERENCES list_dynamic_list (id)');
        $this->addSql('CREATE INDEX IDX_656422843DAE168B ON list_external_object_linkfields (list_id)');
        $this->addSql('ALTER TABLE list_external_object_linkfields_aud ADD list_id CHAR(36) DEFAULT NULL, ADD entity_name VARCHAR(255) DEFAULT NULL, ADD entity_field VARCHAR(255) DEFAULT NULL, DROP suite_bean_name, DROP suite_bean_field');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE list_external_object_linkfields DROP FOREIGN KEY FK_656422843DAE168B');
        $this->addSql('DROP INDEX IDX_656422843DAE168B ON list_external_object_linkfields');
        $this->addSql('ALTER TABLE list_external_object_linkfields ADD suite_bean_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD suite_bean_field VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP list_id, DROP entity_name, DROP entity_field');
        $this->addSql('ALTER TABLE list_external_object_linkfields_aud ADD suite_bean_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD suite_bean_field VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP list_id, DROP entity_name, DROP entity_field');
    }
}
