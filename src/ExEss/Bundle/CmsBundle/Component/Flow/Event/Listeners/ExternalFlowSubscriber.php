<?php
namespace ExEss\Bundle\CmsBundle\Component\Flow\Event\Listeners;

use ExEss\Bundle\CmsBundle\Component\Flow\Event\FlowEvent;
use ExEss\Bundle\CmsBundle\Component\Flow\Event\FlowEvents;
use ExEss\Bundle\CmsBundle\Component\Flow\Response;
use ExEss\Bundle\CmsBundle\Servicemix\ExternalObjectHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ExternalFlowSubscriber implements EventSubscriberInterface
{
    private ExternalObjectHandler $externalObjectHandler;

    public function __construct(ExternalObjectHandler $externalObjectHandler)
    {
        $this->externalObjectHandler = $externalObjectHandler;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FlowEvents::INIT => 'handleExternalFlow',
            FlowEvents::NEXT_STEP => 'handleExternalFlow',
            FlowEvents::NEXT_STEP_FORCED => 'handleExternalFlow',
        ];
    }

    public function handleExternalFlow(FlowEvent $event): void
    {
        // this listener is pointless for non external flows
        if (!$event->getFlow()->isExternal()) {
            return;
        }

        $event->setRecordId($event->getAction()->getRecordIds()[0]);

        $object = $this->externalObjectHandler->getObject($event->getFlow()->getBaseObject(), $event->getParams());

        if ($object === null) {
            return;
        }

        if ($object instanceof \JsonSerializable) {
            $objectArray = $object->jsonSerialize();
        } elseif (\is_object($object)) {
            $objectArray = $object->toArray();
        } elseif (\is_array($object)) {
            $objectArray = $object;
        }

        $this->enhanceModelWithExternalValues($event->getModel(), $objectArray);
    }

    private function enhanceModelWithExternalValues(Response\Model $model, array $objectArray): void
    {
        foreach ($objectArray as $key => $value) {
            if (!\is_array($value)) {
                $model->$key = $value;
            } else {
                $secondObjectArray = [];
                foreach ($value as $secondKey => $secondValue) {
                    $secondObjectArray[$key . '|' . $secondKey] = $secondValue;
                }
                $this->enhanceModelWithExternalValues($model, $secondObjectArray);
            }
        }
    }
}
