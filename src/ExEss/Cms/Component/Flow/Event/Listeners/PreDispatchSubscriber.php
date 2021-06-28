<?php

namespace ExEss\Cms\Component\Flow\Event\Listeners;

use DeepCopy\DeepCopy;
use Doctrine\ORM\EntityManager;
use ExEss\Cms\Entity\Flow;
use ExEss\Cms\Component\Flow\Event\FlowEvent;
use ExEss\Cms\Component\Flow\Event\FlowEvents;
use ExEss\Cms\Component\Flow\Request\FlowAction;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PreDispatchSubscriber implements EventSubscriberInterface
{
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        // high prio, since these always need to be the very first listener to be handled
        return [
            FlowEvents::INIT => [
                ['preDispatch', 999999],
            ],
            FlowEvents::INIT_CHILD_FLOW => [
                ['preDispatch', 999999],
            ],
            FlowEvents::CHANGED => [
                ['preDispatch', 999999],
            ],
            FlowEvents::NEXT_STEP => [
                ['preDispatchNextStep', 999999],
            ],
            FlowEvents::NEXT_STEP_FORCED => [
                ['preDispatch', 999999],
            ],
            FlowEvents::CONFIRM_CREATE_LIST_ROW => [
                ['preDispatch', 999999],
            ],
            FlowEvents::CONFIRM => [
                ['preDispatch', 999999],
            ],
        ];
    }

    /**
     * Prepares dispatching flow events and loads some data in the event object
     *
     * ONLY do stuff here that needs to be done for ALL flow events
     *
     * @throws \InvalidArgumentException In case a guidanceAction is set for an INIT event.
     */
    public function preDispatch(FlowEvent $event): void
    {
        if ($event->getAction()->getEvent() !== FlowAction::EVENT_INIT && $event->getGuidanceAction() !== null) {
            throw new \InvalidArgumentException('guidanceAction can ONLY be set for an INIT event');
        }

        /** @var Flow $flow */
        $flow = $this->em->getRepository(Flow::class)->get($event->getFlowKey());

        $model = $event->getModel();
        $params = $event->getParams();

        // when DWP is requesting a new step is not sending the recordType anymore
        // so we store it on the model on the first call
        if (empty($model->recordTypeOfRecordId)) {
            $model->recordTypeOfRecordId = $event->getRecordType() ?? $flow->getBaseObject();
        }
        $params['recordTypeOfRecordId'] = $model->recordTypeOfRecordId;

        // if we don't have a recordType check if we have one on available in the module
        if (!$event->getRecordType() && !empty($model->recordTypeOfRecordId)) {
            $event->setRecordType($model->recordTypeOfRecordId);
        }

        if ($event->getRecordType() && !isset($params['recordType'])) {
            $params['recordType'] = $event->getRecordType();
        }

        // store a copy of the model as it was posted to us
        $params['origModel'] = (new DeepCopy())->copy($event->getModel());
        if ($event->getAction()->getEvent() === FlowAction::EVENT_CHANGED) {
            $params['returnDelta'] = $params['returnDelta'] ?? true;
        }

        $event->setParams($params);
        $event->setFlow($flow);
    }

    /**
     * @throws \LogicException In case the currentStep is not given for a NEXT STEP event.
     */
    public function preDispatchNextStep(FlowEvent $event): void
    {
        if ($event->getAction()->getCurrentStep() === null) {
            throw new \LogicException('There should always be a currentStep given upon a NEXT STEP event');
        }

        $this->preDispatch($event);
    }
}
