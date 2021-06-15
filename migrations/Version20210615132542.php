<?php declare(strict_types = 1);
// phpcs:ignoreFile

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
* Auto-generated Migration: Please modify to your needs!
*/
final class Version20210615132542 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE flw_flows DROP use_api_label_c');
        $this->addSql('ALTER TABLE flw_guidancefields DROP field_address_type, DROP api_label_c');
        $this->addSql('ALTER TABLE securitygroups DROP reliable_c');
        $this->addSql('ALTER TABLE flw_flows_aud DROP use_api_label_c');
        $this->addSql('ALTER TABLE flw_guidancefields_aud DROP field_address_type, DROP api_label_c');
        $this->addSql('ALTER TABLE securitygroups_aud DROP reliable_c');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE flw_flows ADD use_api_label_c TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE flw_flows_aud ADD use_api_label_c TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE flw_guidancefields ADD field_address_type VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD api_label_c VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE flw_guidancefields_aud ADD field_address_type VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD api_label_c VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE securitygroups ADD reliable_c TINYINT(1) DEFAULT \'0\'');
        $this->addSql('ALTER TABLE securitygroups_aud ADD reliable_c TINYINT(1) DEFAULT NULL');
    }
}
