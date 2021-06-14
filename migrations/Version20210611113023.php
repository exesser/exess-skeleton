<?php declare(strict_types = 1);
// phpcs:ignoreFile

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
* Auto-generated Migration: Please modify to your needs!
*/
final class Version20210611113023 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE grid_gridtemplates CHANGE json_fields_c json_fields_c JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE grid_gridtemplates_aud CHANGE json_fields_c json_fields_c JSON DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE grid_gridtemplates CHANGE json_fields_c json_fields_c TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE grid_gridtemplates_aud CHANGE json_fields_c json_fields_c TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
