<?php

use ExEss\Cms\Robo\CRUD\ConfigurationTask;
use ExEss\Cms\Robo\Task\Db\LoadTestDump;
use ExEss\Cms\Robo\Task\Db\Release\Exporter;
use ExEss\Cms\Robo\Task\Db\Release\Importer;
use ExEss\Cms\Robo\Task\Db\Remove\Audits;
use ExEss\Cms\Robo\Task\Generate\SoapProxies;
use Robo\Result;

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

        $this->taskExec('bin/console exess:cache:clear')->run();
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
        $this->removeAndRecreate(SoapProxies::PROXY_DIR, $storeWsdl);

        return (new SoapProxies($this->io(), $storeWsdl))->run();
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
        return (new Audits($this->io()))->run();
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
