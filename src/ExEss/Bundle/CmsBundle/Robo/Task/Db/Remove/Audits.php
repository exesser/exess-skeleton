<?php
namespace ExEss\Bundle\CmsBundle\Robo\Task\Db\Remove;

use PDO;
use Robo\Result;
use Robo\Task\BaseTask;
use ExEss\Bundle\CmsBundle\Robo\Task\DatabaseAware;
use Symfony\Component\Console\Output\OutputInterface;

class Audits extends BaseTask
{
    use DatabaseAware;

    public function __construct(OutputInterface $output)
    {
        $this->setOutput($output);
    }

    public function run(): Result
    {
        $dbh = $this->getPdoConnection();

        $qry = "SELECT CONCAT('TRUNCATE TABLE ',table_name,';') AS statement
                FROM information_schema.tables
                WHERE table_schema = '" . $this->getDatabaseConfig()['db_name'] . "'
                      AND table_name LIKE '%\_aud'";

        $result = $dbh->query($qry);

        foreach ($result->fetchAll(PDO::FETCH_ASSOC) as ['statement' => $statement]) {
            $this->io()->writeln($statement);
            if (!$dbh->query($statement)->execute()) {
                return new Result($this, Result::EXITCODE_ERROR, "Truncate failed: $statement");
            };
        }

        return new Result($this, Result::EXITCODE_OK);
    }
}
