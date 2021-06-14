<?php
namespace ExEss\Cms\Robo\Task;

use Robo\TaskAccessor;

trait FileSystemAware
{
    use TaskAccessor;

    protected function removeAndRecreate(string $dir, bool $onlySubfolders = false): void
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
}
