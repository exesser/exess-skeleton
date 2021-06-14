<?php

namespace ExEss\Cms\Robo\Task\Db\Release\Import;

use ExEss\Cms\Robo\Task\Db\SqlFileImport;

class Translations extends AbstractDbImport
{
    protected string $subPath = '/translations';

    /**
     * @inheritdoc
     */
    public function runImport()
    {
        (
            new SqlFileImport(
                $this->output(),
                \glob(self::RELEASE_DIR . $this->subPath . "/*.sql"),
                $this->getDatabase()
            )
        )->run();
    }
}
