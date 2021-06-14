<?php
namespace ExEss\Cms\FLW_Flows\Event\Listeners;

use ExEss\Cms\Api\V8_Custom\Events\FlowEvent;
use ExEss\Cms\Api\V8_Custom\Events\FlowEvents;
use ExEss\Cms\Entity\FlowStep;
use ExEss\Cms\FLW_Flows\Response\CurrentStep;
use ExEss\Cms\FLW_Flows\Response\NextStep;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NextStepSubscriber implements EventSubscriberInterface
{
    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FlowEvents::INIT => 'getFirstStep',
            FlowEvents::INIT_CHILD_FLOW => 'getFirstStep',
            FlowEvents::CHANGED => 'getCurrentStep',
            FlowEvents::NEXT_STEP => 'getNextStep',
            FlowEvents::NEXT_STEP_FORCED => 'getNextStepForced',
            FlowEvents::CONFIRM => 'getCurrentStep',
        ];
    }

    public function getFirstStep(FlowEvent $event): void
    {
        $event->setNextStep($this->getNextStepFrom($event));
    }

    public function getCurrentStep(FlowEvent $event): void
    {
        if (!$event->getAction()->getCurrentStep()) {
            // current step was not supplied in the request, can't do anything
            return;
        }

        foreach ($event->getFlow()->getSteps() as $flowStep) {
            if ($flowStep->getKey() === $event->getAction()->getCurrentStep()) {
                $event->getResponse()
                    ->setCurrentStep(new CurrentStep(false, new NextStep(), $flowStep))
                ;
                break;
            }
        }
    }

    /**
     * @throws \LogicException In case there is no current step set.
     */
    public function getNextStep(FlowEvent $event): void
    {
        if ($event->getAction()->getCurrentStep() === null) {
            throw new \LogicException('There should always be a currentStep upon a NEXT STEP event');
        }

        $this->getNextStepForced($event);
    }

    /**
     * @throws \LogicException In case there is no next step.
     */
    public function getNextStepForced(FlowEvent $event): void
    {
        $nextStep = $this->getNextStepFrom($event);

        if ($nextStep->getFlowStep() === null) {
            throw new \LogicException('There is no NEXT STEP, there should have been one!');
        }

        $event->setNextStep($nextStep);
    }

    private function getNextStepFrom(FlowEvent $event): NextStep
    {
        $flowSteps = $event->getFlow()->getSteps();

        $nextStep = new NextStep();
        $nextStep->setRecordId($event->getRecordId());

        if ($event->getAction()->getNextStep()) {
            foreach ($flowSteps as $step) {
                if ($step->getKey() === $event->getAction()->getNextStep()) {
                    $nextStep->setFlowStep($step);
                    break;
                }
            }
        } elseif ($event->getAction()->getCurrentStep()) {
            // find the current step
            foreach ($flowSteps as $step) {
                if ($step->getKey() === $event->getAction()->getCurrentStep()) {
                    break;
                }
            }
            // proceed to the next step
            $flowSteps->next();
            // get the next step if any
            if ($step = $flowSteps->current()) {
                $nextStep->setFlowStep($step);
            } else {
                $nextStep->setLastStep(true);
            }
        } else {
            if ($step = $flowSteps->first()) {
                /** @var FlowStep $step */
                $nextStep->setFlowStep($step);
            }
        }

        if ($flowSteps->count() <= 1) {
            $nextStep->setLastStep(true);
        }

        return $nextStep;
    }
}
