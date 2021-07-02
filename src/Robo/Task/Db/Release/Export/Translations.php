<?php

namespace App\Robo\Task\Db\Release\Export;

use ExEss\Bundle\CmsBundle\Factory\ClassMetadataFactory;

class Translations extends AbstractDbExport
{
    protected string $subPath = '/translations';

    /**
     * @inheritdoc
     */
    public function runExport()
    {
        /** @var ClassMetadataFactory $metadataFactory */
        $metadataFactory = $this->getContainer()->get(ClassMetadataFactory::class);

        $fatEntities = [
            $metadataFactory->getMetadataFor(\TRANS_Translation::class),
        ];

        $this->dumpBeanTables($fatEntities, $this->subPath . '/release_translations.sql');
    }
}
