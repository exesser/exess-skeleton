<?php declare(strict_types=1);

use ExEss\Bundle\CmsBundle\Component\Codeception\Traits\ServiceActions;

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
     * @return string[]
     */
    public function getContainerKeys(): array
    {
        return $this->grabService('service_container')->getServiceIds();
    }

   /**
    * Define custom actions here
    */
}
