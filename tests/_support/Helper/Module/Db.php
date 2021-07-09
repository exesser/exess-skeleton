<?php
namespace Helper\Module;

use Codeception\Module\Db as CodeceptionModule;
use Codeception\TestInterface;

class Db extends CodeceptionModule
{
    /**
     * Override to prevent removing inserted records.
     */
    //@codingStandardsIgnoreStart
    public function _after(TestInterface $test)
    {
        //@codingStandardsIgnoreEnd
        $this->insertedRows = [];
        parent::_after($test);
    }
}
