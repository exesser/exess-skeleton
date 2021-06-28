<?php
namespace ExEss\Cms\Component\Flow\Event\Listeners;

use ExEss\Cms\Collection\ObjectCollection;
use ExEss\Cms\Doctrine\Type\TranslationDomain;
use ExEss\Cms\Component\Flow\Event\FlowEvent;
use ExEss\Cms\Component\Flow\Event\FlowEvents;
use ExEss\Cms\Component\Flow\Request\FlowAction;
use ExEss\Cms\Component\Flow\Response;
use ExEss\Cms\Component\Flow\Response\CurrentStep;
use ExEss\Cms\Component\Flow\Response\ProgressStep;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProgressSubscriber implements EventSubscriberInterface
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FlowEvents::INIT => 'injectProgress',
            FlowEvents::INIT_CHILD_FLOW => 'injectProgress',
            FlowEvents::NEXT_STEP => 'injectProgress',
            FlowEvents::NEXT_STEP_FORCED => 'injectProgress',
        ];
    }

    /**
     * @throws \InvalidArgumentException In case a step was requested that is not part of this flow.
     * @throws \LogicException In case ProgressSubscriber has not run yet.
     */
    public function injectProgress(FlowEvent $event): void
    {
        if (!$event->getNextStep() instanceof Response\NextStep) {
            throw new \LogicException('ProgressSubscriber must run after NextStepSubscriber');
        }
        $nextStep = $event->getNextStep();

        if (!$nextStep->getFlowStep() && $event->getAction()->getEvent() === FlowAction::EVENT_INIT) {
            // this is the init event of a flow without steps, we're done
            $event->stopPropagation();
            return;
        }

        $steps = new ObjectCollection(ProgressStep::class);

        $nextStepIndex = null;

        // get the current flow step
        $activeStep = null;
        if (\in_array(
            $event->getAction()->getEvent(),
            [FlowAction::EVENT_INIT, FlowAction::EVENT_INIT_CHILD_FLOW],
            true
        )) {
            $activeStep = $event->getFlow()->getSteps()->first();
        }

        // build the steps block
        foreach ($event->getFlow()->getSteps() as $stepIndex => $flowStep) {
            $step = new ProgressStep(
                $flowStep->getId(),
                $flowStep->getKey(),
                $this->translator->trans($flowStep->getLabel(), [], TranslationDomain::GUIDANCE_TITLE),
                $flowStep->getType()
            );

            // check if this will become the active step
            if ($flowStep === $nextStep->getFlowStep()) {
                $nextStepIndex = $stepIndex;
                $step->isActive(true);
            }

            if (
                $event->getAction()->getEvent() !== FlowAction::EVENT_INIT
                && $flowStep->getKey() === $event->getAction()->getCurrentStep()
            ) {
                $activeStep = $flowStep;
            }

            $steps[] = $step;
        }

        // verify the next step was found
        if (!$activeStep) {
            throw new \InvalidArgumentException(\sprintf(
                'Failed to set the next active step, step with key %s not found in the flow',
                $nextStep->getFlowStep()->getKey()
            ));
        }

        // check if submitting the next step will save the flow
        $willSave = $nextStepIndex === $event->getFlow()->getSteps()->count() - 1;

        $event->getResponse()
            ->setSteps($steps)
            ->setCurrentStep(new CurrentStep($willSave, $nextStep, $activeStep))
        ;
    }
}
