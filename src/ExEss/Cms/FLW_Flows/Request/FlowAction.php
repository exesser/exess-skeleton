<?php
namespace ExEss\Cms\FLW_Flows\Request;

use ExEss\Cms\Api\V8_Custom\Params\AbstractParams;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FlowAction extends AbstractParams
{
    /**
     * Events as they are sent from DWP
     */
    public const EVENT_CONFIRM_CREATE_LIST_ROW = 'CONFIRM-CREATE-LIST-ROW';
    public const EVENT_CONFIRM = 'CONFIRM';
    public const EVENT_CHANGED = 'CHANGED';
    public const EVENT_NEXT_STEP = 'NEXT-STEP';
    public const EVENT_NEXT_STEP_FORCED = 'NEXT-STEP-FORCED';
    public const EVENT_INIT = 'INIT';
    public const EVENT_INIT_CHILD_FLOW = 'INIT-CHILD-FLOW';

    public const BREAKOUT = 'breakout';
    public const READONLY = 'readOnly';

    public function __construct(array $options)
    {
        $this->configure($options);
    }

    /**
     * Method to configure the options passed through this class.
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException In case of undefined options.
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException If called from a lazy option or normalizer.
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('event')
            ->setAllowedTypes('event', ['string'])
            ->setAllowedValues('event', [
                static::EVENT_INIT,
                static::EVENT_INIT_CHILD_FLOW,
                static::EVENT_NEXT_STEP,
                static::EVENT_NEXT_STEP_FORCED,
                static::EVENT_CHANGED,
                static::EVENT_CONFIRM,
                static::EVENT_CONFIRM_CREATE_LIST_ROW,
            ])
        ;

        $resolver
            ->setDefined('recordIds')
            ->setAllowedTypes('recordIds', ['array'])
            ->setDefault('recordIds', [])
        ;

        $resolver
            ->setDefined('previousValue')
            ->setAllowedTypes('previousValue', ['null', 'string', 'boolean', 'array', 'integer'])
            ->setDefault('previousValue', null)
        ;

        $resolver
            ->setDefined('focus')
            ->setAllowedTypes('focus', ['null', 'string'])
            ->setDefault('focus', null)
            ->setNormalizer('focus', function (Options $options, $value) {
                return empty($value) ? null : $value;
            })
        ;

        $resolver
            ->setDefined('changedFields')
            ->setAllowedTypes('changedFields', ['array'])
            ->setDefault('changedFields', [])
        ;

        $resolver
            ->setDefined('currentStep')
            ->setAllowedTypes('currentStep', ['null', 'string'])
            ->setDefault('currentStep', null)
            ->setNormalizer('currentStep', function (Options $options, $value) {
                return empty($value) ? null : $value;
            })
        ;

        $resolver
            ->setDefined('nextStep')
            ->setAllowedTypes('nextStep', ['null', 'string'])
            ->setDefault('nextStep', null)
            ->setNormalizer('nextStep', function (Options $options, $value) {
                return empty($value) ? null : $value;
            })
        ;
    }

    public function getRecordIds(): array
    {
        return $this->arguments['recordIds'];
    }

    public function isEvent(string $event): bool
    {
        return $this->arguments['event'] === $event;
    }

    public function getEvent(): string
    {
        return $this->arguments['event'];
    }

    public function getFocus(): ?string
    {
        return $this->arguments['focus'];
    }

    /**
     * @return string|bool|array|null
     */
    public function getPreviousValue()
    {
        return $this->arguments['previousValue'];
    }

    public function getCurrentStep(): ?string
    {
        return $this->arguments['currentStep'];
    }

    public function getNextStep(): ?string
    {
        return $this->arguments['nextStep'];
    }

    public function getChangedFieldKeys(): array
    {
        return \array_keys($this->arguments['changedFields']);
    }

    public function getChangedFields(): array
    {
        return $this->arguments['changedFields'];
    }

    public function isFieldChanged(string $fieldKey): bool
    {
        return \array_key_exists($fieldKey, $this->arguments['changedFields']);
    }

    public function setFocus(string $focus): void
    {
        $this->arguments['focus'] = $focus;
    }
}
