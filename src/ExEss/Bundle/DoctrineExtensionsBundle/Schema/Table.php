<?php declare(strict_types=1);

namespace ExEss\Bundle\DoctrineExtensionsBundle\Schema;

use Doctrine\DBAL\Schema\Table as BaseTable;

/**
 * Not added to container
 */
class Table extends BaseTable
{
    public function __construct(BaseTable $table)
    {
        parent::__construct(
            $table->_name,
            $table->_columns,
            $table->_indexes,
            $table->_fkConstraints,
            0,
            $table->_options
        );
    }
}
