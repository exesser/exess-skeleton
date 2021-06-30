<?php declare(strict_types=1);

use ExEss\Bundle\CmsBundle\Component\Codeception\Traits\ServiceActions;
use ExEss\Bundle\CmsBundle\Component\Flow\Action\Arguments;
use ExEss\Bundle\CmsBundle\Component\Flow\Action\BackendCommandExecutor;
use ExEss\Bundle\CmsBundle\Component\Flow\Action\Command;
use ExEss\Bundle\CmsBundle\Component\Flow\Response\Model;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class FunctionalTester extends \Codeception\Actor
{
    use _generated\FunctionalTesterActions;
    use ServiceActions;

   /**
    * Define custom actions here
    */

    /**
     * Executes a command from the container
     */
    public function executeCommand(string $alias, array $arguments, array $model = []): void
    {
        $executor = $this->grabService(BackendCommandExecutor::class);
        $command = new Command($alias, new Arguments(), $alias);

        $executor->execute($command, $arguments, new Model($model));
    }

    /**
     * @return string[]
     */
    public function getContainerKeys(): array
    {
        return $this->grabService('service_container')->getServiceIds();
    }
}
