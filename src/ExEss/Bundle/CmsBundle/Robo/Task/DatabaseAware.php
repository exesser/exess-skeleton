<?php
namespace ExEss\Bundle\CmsBundle\Robo\Task;

use PDO;
use Robo\Common\IO;
use ExEss\Bundle\CmsBundle\Robo\Formatter\MaskedOutputFormatter;

trait DatabaseAware
{
    use IO;
    use EnvironmentAware;

    protected function getDatabaseConfig(?string $databaseName = null): array
    {
        $this->bootstrapEnvironment();

        $password = $_ENV['DBCONFIG_DB_PASSWORD'];
        $this->io()->setFormatter((new MaskedOutputFormatter())->setStringToMask($password));

        return [
            'db_host_name' => $_ENV['DBCONFIG_DB_HOST_NAME'],
            'db_user_name' => $_ENV['DBCONFIG_DB_USER_NAME'],
            'db_password' => $password,
            'db_name' => $databaseName ?? $_ENV['DBCONFIG_DB_NAME'],
        ];
    }

    protected function getPdoConnection(?string $databaseName = null): PDO
    {
        $config = $this->getDatabaseConfig($databaseName);

        $dbh = new PDO(
            'mysql:dbname=' . $config['db_name'] . ';host=' . $config['db_host_name'],
            $config['db_user_name'],
            $config['db_password']
        );
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $dbh;
    }

    protected function getMysqliConnection(?string $databaseName = null): \mysqli
    {
        $config = $this->getDatabaseConfig($databaseName);

        return \mysqli_connect(
            $config['db_host_name'],
            $config['db_user_name'],
            $config['db_password'],
            $config['db_name']
        );
    }

    protected function getDatabaseCli(bool $withDatabase = false, ?string $databaseName = null): string
    {
        $config = $this->getDatabaseConfig($databaseName);

        return '-u' . $config['db_user_name']
            . ' -p' . $config['db_password']
            . ' -h' . $config['db_host_name']
            . ($withDatabase ? ' ' . $config['db_name'] : '')
        ;
    }

    protected function wrapForCliPipe(string $command, bool $withDatabase = false, ?string $databaseName = null): string
    {
        return $this->wrapCli("$command | mysql", $withDatabase, $databaseName);
    }

    protected function wrapForCliDump(string $options, ?string $databaseName = null): string
    {
        return $this->wrapCli("mysqldump --skip-triggers --set-gtid-purged=OFF $options", true, $databaseName);
    }

    protected function wrapCli(string $toWrap, bool $withDatabase = false, ?string $databaseName = null): string
    {
        $config = $this->getDatabaseConfig($databaseName);

        return \sprintf(
            "export MYSQL_PWD=%s && %s -u%s -h%s %s",
            $config['db_password'],
            $toWrap,
            $config['db_user_name'],
            $config['db_host_name'],
            $withDatabase ? $config['db_name'] : ''
        );
    }
}
