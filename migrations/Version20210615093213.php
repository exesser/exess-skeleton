<?php declare(strict_types = 1);
// phpcs:ignoreFile

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
* Auto-generated Migration: Please modify to your needs!
*/
final class Version20210615093213 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE grid_panels ADD list_id CHAR(36) DEFAULT NULL, DROP list_key, CHANGE flow_id flow_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE grid_panels ADD CONSTRAINT FK_ABF9B9F87EB60D1B FOREIGN KEY (flow_id) REFERENCES flw_flows (id)');
        $this->addSql('ALTER TABLE grid_panels ADD CONSTRAINT FK_ABF9B9F83DAE168B FOREIGN KEY (list_id) REFERENCES list_dynamic_list (id)');
        $this->addSql('CREATE INDEX IDX_ABF9B9F87EB60D1B ON grid_panels (flow_id)');
        $this->addSql('CREATE INDEX IDX_ABF9B9F83DAE168B ON grid_panels (list_id)');
        $this->addSql('ALTER TABLE list_cell DROP mainmenukey, DROP dashboardid, DROP customhandler');
        $this->addSql('ALTER TABLE grid_panels_aud ADD list_id CHAR(36) DEFAULT NULL, DROP list_key, CHANGE flow_id flow_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE list_cell_aud DROP mainmenukey, DROP dashboardid, DROP customhandler');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE grid_panels DROP FOREIGN KEY FK_ABF9B9F87EB60D1B');
        $this->addSql('ALTER TABLE grid_panels DROP FOREIGN KEY FK_ABF9B9F83DAE168B');
        $this->addSql('DROP INDEX IDX_ABF9B9F87EB60D1B ON grid_panels');
        $this->addSql('DROP INDEX IDX_ABF9B9F83DAE168B ON grid_panels');
        $this->addSql('ALTER TABLE grid_panels ADD list_key VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP list_id, CHANGE flow_id flow_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE grid_panels_aud ADD list_key VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP list_id, CHANGE flow_id flow_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE list_cell ADD mainmenukey VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD dashboardid VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD customhandler VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE list_cell_aud ADD mainmenukey VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD dashboardid VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD customhandler VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
