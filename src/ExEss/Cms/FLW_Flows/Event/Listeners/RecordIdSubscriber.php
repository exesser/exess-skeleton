<?php
namespace ExEss\Cms\FLW_Flows\Event\Listeners;

use Doctrine\ORM\EntityManagerInterface;
use ExEss\Cms\Api\V8_Custom\Events\FlowEvent;
use ExEss\Cms\Api\V8_Custom\Events\FlowEvents;
use ExEss\Cms\CRUD\Helpers\CrudFlowHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RecordIdSubscriber implements EventSubscriberInterface
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
            FlowEvents::INIT => 'setRecordIdWithGuidanceOverride',
            FlowEvents::INIT_CHILD_FLOW => 'setRecordIdWithGuidanceOverride',
            FlowEvents::NEXT_STEP => 'setRecordIdFromAction',
            FlowEvents::NEXT_STEP_FORCED => 'setRecordIdFromAction',
            FlowEvents::CONFIRM => [
                ['setRecordIdsFromModel', 900],
                ['setFirstOfRecordIds'],
            ],
        ];
    }

    public function setFirstOfRecordIds(FlowEvent $event): void
    {
        $recordIds = $event->getAction()->getRecordIds();
        $event->setRecordId($recordIds[0] ?? null);
    }

    public function setRecordIdFromAction(FlowEvent $event): void
    {
        $this->setRecordIdFromRecordIds($event, $event->getAction()->getRecordIds());
    }

    public function setRecordIdWithGuidanceOverride(FlowEvent $event): void
    {
        $recordIds = $event->getAction()->getRecordIds();

        $this->setRecordIdFromRecordIds($event, $recordIds);

        // if multi record, store the posted record ids on the model
        if (\count($recordIds) > 1) {
            $event->getModel()->recordIds = $event->getAction()->getRecordIds();
        }
    }

    private function setRecordIdFromRecordIds(FlowEvent $event, array $recordIds): void
    {
        // we are only interested in the first id, as we do not care if it is a mass update
        $recordId = $recordIds[0] ?? null;

        if ($recordId) {
            $params = $event->getParams();
            $params['recordId'] = $recordId;
            $event->setParams($params);
        }

        // If the base object of the guidance flow is not the same as the one of the recordId
        // then record Id should be null
        if (!$event->getRecordType()) {
            $recordId = null;
        } elseif (
            $event->getFlow()->getBaseObject() !== $event->getRecordType()
            && $this->em->getMetadataFactory()->hasMetadataFor($event->getRecordType())
            && !CrudFlowHelper::isCrudFlow($event->getFlowKey())    // as these flows have a dynamic base object
        ) {
            $recordId = null;
        }

        $event->setRecordId($recordId);
    }

    /**
     * When the field 'batch_ids' is present in the model, this is a lost of recordIds to process
     * This listener parses the text data and assigns the ids to recordIds in correct place
     */
    public function setRecordIdsFromModel(FlowEvent $event): void
    {
        if (!isset($event->getModel()->batch_ids)) {
            return;
        }

        $event->getModel()->recordIds = \preg_split('~(\r\n|\n|\r|;| )~', $event->getModel()->batch_ids);
        unset($event->getModel()->batch_ids);
    }
}
