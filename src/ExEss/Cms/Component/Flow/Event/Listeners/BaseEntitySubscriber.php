<?php

namespace ExEss\Cms\Component\Flow\Event\Listeners;

use Doctrine\ORM\EntityManagerInterface;
use ExEss\Cms\Component\Flow\Event\FlowEvent;
use ExEss\Cms\Component\Flow\Event\FlowEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BaseEntitySubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FlowEvents::INIT => 'getBaseEntityAndSetInModel',
            FlowEvents::INIT_CHILD_FLOW => 'getBaseEntityAndSetInModel',
        ];
    }

    public function getBaseEntityAndSetInModel(FlowEvent $event): void
    {
        $this->getBaseEntity($event);

        $model = $event->getModel();
        if ($event->getBaseEntity()) {
            $model->recordId = $event->getRecordId();
        }
        $model->baseModule = $event->getFlow()->getBaseObject();
    }

    public function getBaseEntity(FlowEvent $event): void
    {
        $baseObject = $event->getFlow()->getBaseObject();
        $recordId = $event->getRecordId();

        // set the base module on the model, and the id in case of a set recordId
        if (!$event->getFlow()->isExternal() && isset($baseObject, $recordId)) {
            $event->setBaseEntity($this->em->getRepository($baseObject)->find($recordId));
        }
    }
}
