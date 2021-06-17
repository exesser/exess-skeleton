<?php
namespace Helper\Module;

use Codeception\Lib\ModuleContainer;
use Codeception\Module\Db as CodeceptionModule;
use Codeception\TestInterface;

class Db extends CodeceptionModule
{
    public function __construct(ModuleContainer $moduleContainer, ?array $config = null)
    {
        // codeception doesn't support bool parameters in codeception.yml
        $config['populate'] = \filter_var($config['populate'], \FILTER_VALIDATE_BOOLEAN);

        parent::__construct($moduleContainer, $config);
    }

    /**
     * Override to prevent removing inserted records.
     *
     */
    //@codingStandardsIgnoreStart
    public function _after(TestInterface $test)
    {
        //@codingStandardsIgnoreEnd
        $this->insertedRows = [];
        parent::_after($test);
    }
}
