<?php declare(strict_types = 1);
// phpcs:ignoreFile

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
* Auto-generated Migration: Please modify to your needs!
*/
final class Version20210616105750 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql("REPLACE INTO `trans_translation` (`id`, `name`, `translation`, `domain`, `created_by`, `locale`)
        VALUES
            ('5e8b801e-3e63-6967-60c2-111111111124', 'ExEss\\Cms\\Entity\\Property', 'Dashboard Properties', 'module', 1, 'en_BE'),
            ('5e8b801e-3e63-6967-60c2-111111111173', 'ExEss\\Cms\\Entity\\AclAction', 'ACL Action', 'module', 1, 'en_BE'),
            ('5e8b801e-3e63-6967-60c2-111111111171', 'ExEss\\Cms\\Entity\\AclRole', 'ACL Role', 'module', 1, 'en_BE'),
            ('5e8b801e-3e63-6967-60c2-111111111119', 'ExEss\\Cms\\Entity\\ConditionalMessage', 'Conditional Message', 'module', 1, 'en_BE'),
            ('5e8b801e-3e63-6967-60c2-111111111110', 'ExEss\\Cms\\Entity\\ConfDefaults', 'Conf Defaults', 'module', 1, 'en_BE'),
            ('5e8b801e-3e63-6967-60c2-111111111122', 'ExEss\\Cms\\Entity\\DashboardMenu', 'Dashboard Menu', 'module', 1, 'en_BE'),
            ('5e8b801e-3e63-6967-60c2-111111111123', 'ExEss\\Cms\\Entity\\DashboardMenuActionGroup', 'Dashboard Menu Groups', 'module', 1, 'en_BE'),
            ('5e8b801e-3e63-6967-60c2-111111111125', 'ExEss\\Cms\\Entity\\DashboardMenuAction', 'Dashboard Menu Actions', 'module', 1, 'en_BE'),
            ('5e8b801e-3e63-6967-60c2-111111111130', 'ExEss\\Cms\\Entity\\Filter', 'Filters', 'module', 1, 'en_BE'),
            ('5e8b801e-3e63-6967-60c2-111111111128', 'ExEss\\Cms\\Entity\\FilterField', 'Filters Fields', 'module', 1, 'en_BE'),
            ('5e8b801e-3e63-6967-60c2-111111111129', 'ExEss\\Cms\\Entity\\FilterFieldGroup', 'Filters Groups', 'module', 1, 'en_BE'),
            ('5e8b801e-3e63-6967-60c2-111111111126', 'ExEss\\Cms\\Entity\\SelectWithSearch', 'Select With Search', 'module', 1, 'en_BE'),
            ('5e8b801e-3e63-6967-60c2-111111111127', 'ExEss\\Cms\\Entity\\FindSearch', 'Find Search', 'module', 1, 'en_BE'),
            ('5e8b801e-3e63-6967-60c2-111111111132', 'ExEss\\Cms\\Entity\\Flow', 'Flows', 'module', 1, 'en_BE'),
            ('5e8b801e-3e63-6967-60c2-111111111136', 'ExEss\\Cms\\Entity\\FlowField', 'Flow Fields', 'module', 1, 'en_BE'),
            ('5e8b801e-3e63-6967-60c2-111111111134', 'ExEss\\Cms\\Entity\\FlowStep', 'Flow Steps', 'module', 1, 'en_BE'),
            ('5e8b801e-3e63-6967-60c2-111111111135', 'ExEss\\Cms\\Entity\\FlowStepLink', 'Flow Steps Link', 'module', 1, 'en_BE'),
            ('5e8b801e-3e63-6967-60c2-111111111139', 'ExEss\\Cms\\Entity\\GridPanel', 'Grid Panels', 'module', 1, 'en_BE'),
            ('5e8b801e-3e63-6967-60c2-111111111138', 'ExEss\\Cms\\Entity\\GridTemplate', 'Grid Templates', 'module', 1, 'en_BE'),
            ('5e8b801e-3e63-6967-60c2-111111111140', 'ExEss\\Cms\\Entity\\ListCell', 'List Cell', 'module', 1, 'en_BE'),
            ('5e8b801e-3e63-6967-60c2-111111111141', 'ExEss\\Cms\\Entity\\ListCellLink', 'List Cells Link', 'module', 1, 'en_BE'),
            ('5e8b801e-3e63-6967-60c2-111111111142', 'ExEss\\Cms\\Entity\\ListDynamic', 'Lists', 'module', 1, 'en_BE'),
            ('5e8b801e-3e63-6967-60c2-111111111145', 'ExEss\\Cms\\Entity\\ListRowAction', 'List Row Action', 'module', 1, 'en_BE'),
            ('5e8b801e-3e63-6967-60c2-111111111146', 'ExEss\\Cms\\Entity\\ListRowBar', 'List Row Bar', 'module', 1, 'en_BE'),
            ('5e8b801e-3e63-6967-60c2-111111111147', 'ExEss\\Cms\\Entity\\ListSortingOption', 'List Sorting Options', 'module', 1, 'en_BE'),
            ('5e8b801e-3e63-6967-60c2-111111111149', 'ExEss\\Cms\\Entity\\ListTopAction', 'List Top Action', 'module', 1, 'en_BE'),
            ('5e8b801e-3e63-6967-60c2-111111111148', 'ExEss\\Cms\\Entity\\ListTopBar', 'List Top Bar', 'module', 1, 'en_BE'),
            ('5e8b801e-3e63-6967-60c2-111111111150', 'ExEss\\Cms\\Entity\\Menu', 'Main Menu', 'module', 1, 'en_BE'),
            ('5e8b801e-3e63-6967-60c2-111111111172', 'ExEss\\Cms\\Entity\\SecurityGroup', 'Security Group', 'module', 1, 'en_BE'),
            ('5e8b801e-3e63-6967-60c2-111111111169', 'ExEss\\Cms\\Entity\\SecurityGroupApi', 'Security Group API', 'module', 1, 'en_BE'),
            ('5e8b801e-3e63-6967-60c2-111111111143', 'ExEss\\Cms\\Entity\\ExternalObject', 'List External Object', 'module', 1, 'en_BE'),
            ('5e8b801e-3e63-6967-60c2-111111111144', 'ExEss\\Cms\\Entity\\ExternalObjectLink', 'List External Object Link', 'module', 1, 'en_BE'),
            ('5e8b801e-3e63-6967-60c2-111111111137', 'ExEss\\Cms\\Entity\\Validator', 'Flow Field Validators', 'module', 1, 'en_BE'),
            ('5e8b801e-3e63-6967-60c2-111111111174', 'ExEss\\Cms\\Entity\\SecuritygroupsUser', 'Security Groups Users', 'module', 1, 'en_BE'),
            ('5e8b801e-3e63-6967-60c2-111111111170', 'ExEss\\Cms\\Entity\\Translation', 'Translations', 'module', 1, 'en_BE');");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
