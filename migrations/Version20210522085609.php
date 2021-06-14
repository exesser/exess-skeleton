<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210522085609 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users ADD salt VARCHAR(40) NOT NULL AFTER user_hash');
        $this->addSql('ALTER TABLE users_aud ADD salt VARCHAR(40) DEFAULT NULL AFTER user_hash');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users DROP salt');
        $this->addSql('ALTER TABLE users_aud DROP salt');
    }
}
