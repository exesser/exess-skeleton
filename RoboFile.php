<?php

use Robo\Result;
use ExEss\Cms\CRUD\Config\ConfigurationTask;
use ExEss\Cms\Robo\Task\Db\LoadTestDump;
use ExEss\Cms\Robo\Task\Db\Release\Exporter;
use ExEss\Cms\Robo\Task\Db\Release\Importer;
use ExEss\Cms\Robo\Task\Db\TableSizes;
use ExEss\Cms\Robo\Task\Debug;
use ExEss\Cms\Robo\Task\Generate;

/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{
    private const ENVIRONMENT_LOCAL = 'local';
    private const ENVIRONMENT_DEVELOPMENT = 'dev';
    private const ENVIRONMENT_FUNCTIONAL_API_TEST = 'autotst';

    private const ENVIRONMENTS = [
        self::ENVIRONMENT_LOCAL,
        self::ENVIRONMENT_DEVELOPMENT,
        self::ENVIRONMENT_FUNCTIONAL_API_TEST,
    ];

    /**
     * clean vendor packages to remove tests and examples
     */
    private function composerClean(): void
    {
        $objects = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator('vendor', FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        $folderNames = ['build', 'test', 'tests', 'example', 'examples', 'scenarios'];
        $foldersToClean = [];
        /** @var SplFileInfo $object */
        foreach ($objects as $name => $object) {
            if ($object->isDir() && \in_array($object->getBasename(), $folderNames)) {
                $foldersToClean[] = $object->getRealPath();
            }
        }
        if (!empty($foldersToClean)) {
            // sort so longest are first to avoid parent folder removing sub folders that are also in the list
            \rsort($foldersToClean, \SORT_STRING);
            $this->_cleanDir($foldersToClean);
        }
    }

    /**
     * Repairs and rebuilds DB, Extensions, Vardefs, etc.
     */
    public function rebuild(): void
    {
        if ($this->taskExec('rm -rf var/cache/*')->run()->wasSuccessful()) {
            $this->say('Emptied cache folder');
        }

        $this->taskExec('bin/console nova:cache:clear')->run();

        if ($this->taskExec('bin/console nova:rebuild')->run()->wasSuccessful()) {
            $this->say('Quick Repair and Rebuild executed');
        }
    }

    /**
     * Prepares a fresh checkout from git for running the application, but DOESN'T do
     * any database operations yet (as chances are quite big the previously deployed version still uses it!)
     */
    public function deployPrepare(
        string $environment,
        array $opts = ['no-dev' => false]
    ): void {
        $this->stopOnFail();

        $this->checkEnvironment($environment);

        if ($environment === self::ENVIRONMENT_LOCAL) {
            $this->taskComposerInstall('composer --no-interaction' . ($opts['no-dev'] ? ' --no-dev' : ''))->run();
            $this->composerClean();
            $this->_copy('.env.test.local.dist', '.env.test.local');
        } else {
            $this->_touch('.env.test.local');
        }

        // for PR testing, ensure the current dump is loaded BEFORE rebuilding
        if ($environment === self::ENVIRONMENT_FUNCTIONAL_API_TEST) {
            $this->dbLoadTestDump();
        }

        $this->rebuild();
        $this->generateLightEntities();
        $this->generateSoapProxies($environment);
        $this->taskComposerDumpAutoload('composer --no-interaction')->run();

        (new Generate\OpenApiContract())->run();
    }

    /**
     * Perform any database alterations / loads in this deployment step
     */
    public function deployDatabase(string $environment): void
    {
        //update the CRUD config
        $this->crudInstall();
    }

    /**
     * runs all the operations necessary to get a fully up to date local system
     *
     * @param string $environment The DTAP environment on which this command is run.
     * @param string $client The client for which we want to run suite.
     */
    public function build(string $environment, array $opts = ['no-dev' => false]): void
    {
        $this->deployPrepare($environment, $opts);
        $this->deployDatabase($environment);
    }

    /**
     * Load test dump in database (default: application database)
     */
    public function dbLoadTestDump(?string $database = null): Result
    {
        return (new LoadTestDump($this->io(), $database))->run();
    }

    /**
     * @param string $environment The OTAP environment on which this command is run.
     * @throws \InvalidArgumentException When incorrect environment is given.
     */
    protected function checkEnvironment(string $environment): void
    {
        if (!\in_array($environment, self::ENVIRONMENTS, true)) {
            throw new \InvalidArgumentException(
                'invalid environment given, should be one of: '
                . \implode(self::ENVIRONMENTS)
            );
        }
    }

    /**
     * @param string|null $filter Filter on event, class or method name.
     */
    public function debugEvents(?string $filter = null): Result
    {
        return (new Debug\Events($this->io(), $filter))->run();
    }

    private function removeAndRecreate(string $dir, bool $onlySubfolders = false): void
    {
        $removeDir = $onlySubfolders ? "$dir/*/" : $dir;
        if ($this->taskExec("rm -rf $removeDir")->run()->wasSuccessful()) {
            $this->say('Removed old proxy classes');
        }
        if (!$onlySubfolders) {
            $this->_mkdir($dir);
            $this->_chmod($dir, 0777);
        }
    }

    public function generateSoapProxies(string $environment): Result
    {
        $storeWsdl = $environment === self::ENVIRONMENT_LOCAL;
        $this->removeAndRecreate(Generate\SoapProxies::PROXY_DIR, $storeWsdl);

        return (new Generate\SoapProxies($this->io(), $storeWsdl))->run();
    }

    /**
     * runs migrations for CRUD configuration and CRUD views for DWP
     */
    public function crudInstall(?string $database = null): Result
    {
        return (new ConfigurationTask($this->io(), $database))->run();
    }

    /**
     * Truncate all the audit tables
     */
    public function dbAuditsRemove(): Result
    {
        return (new \ExEss\Cms\Robo\Task\Db\Remove\Audits($this->io()))->run();
    }

    /**
     * Display table sizes, hides tables <10MB by default
     *
     * @option $a Display all tables, including <10MB
     * @option $b Display only really big tables, >100MB
     */
    public function dbTableSizes(array $opts = ['all' => false, 'big' => false]): Result
    {
        return (new TableSizes($this->io(), $opts['all'], $opts['big']))->run();
    }

    /**
     * creates the SQL files with the complete configuration + data dump to be restored on other envs
     */
    public function releaseExport(): Result
    {
        return (new Exporter($this->io()))->run();
    }

    /**
     * creates the SQL files with the complete configuration + data dump (including deleted records and audits)
     */
    public function releaseExportAll(): Result
    {
        return (new Exporter($this->io()))->setForceExportAll()->run();
    }

    public function releaseImport(?string $databaseName = null): Result
    {
        return (new Importer($this->io(), $databaseName))->run();
    }
}
