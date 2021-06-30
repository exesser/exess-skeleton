<?php
namespace ExEss\Bundle\CmsBundle\Base\Handler\Form;

use ExEss\Bundle\CmsBundle\Api\V8_Custom\Service\FlashMessages\FlashMessage;
use ExEss\Bundle\CmsBundle\Api\V8_Custom\Service\FlashMessages\FlashMessageContainer;
use ExEss\Bundle\CmsBundle\Api\V8_Custom\Service\FlashMessages\FlashMessageContainerAware;
use ExEss\Bundle\CmsBundle\Base\Response\BaseResponse;
use ExEss\Bundle\CmsBundle\Component\Flow\Event\Exception\CommandException;
use ExEss\Bundle\CmsBundle\Component\Flow\Handler\AbstractSaveHandler;
use ExEss\Bundle\CmsBundle\Component\Flow\Handler\FlowData;

abstract class AbstractForm extends AbstractSaveHandler implements FlashMessageContainerAware
{
    private FlashMessageContainer $flashMessageContainer;

    public function setFlashMessageContainer(FlashMessageContainer $flashMessageContainer): void
    {
        $this->flashMessageContainer = $flashMessageContainer;
    }

    protected function doHandle(FlowData $data): void
    {
        try {
            $response = $this->post($data);
        } catch (\InvalidArgumentException | CommandException $e) {
            throw $e;
        } catch (\Exception $e) {
            if ($this->flashMessageContainer instanceof FlashMessageContainer) {
                $this->flashMessageContainer->addFlashMessage(
                    new FlashMessage('Can\'t submit ' . $data->getFlowKey())
                );
            }

            throw $e;
        }

        if ($this->flashMessageContainer instanceof FlashMessageContainer
            && ($response === null || !$response->getResult())
        ) {
            $message = 'Error while sending data to the external system';

            if ($response instanceof BaseResponse) {
                $message = $response->getMessage();
            }

            $this->flashMessageContainer->addFlashMessage(
                new FlashMessage($message)
            );

            throw new \DomainException(
                $message
            );
        }
    }

    abstract protected function post(FlowData $data): BaseResponse;
}
