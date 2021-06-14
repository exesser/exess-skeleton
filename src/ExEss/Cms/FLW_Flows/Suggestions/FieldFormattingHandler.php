<?php
namespace ExEss\Cms\FLW_Flows\Suggestions;

use ExEss\Cms\Entity\Flow;
use ExEss\Cms\FieldFormatter\PhoneNumberFormatter;
use ExEss\Cms\FieldFormatter\VatNumberFormatter;
use ExEss\Cms\FLW_Flows\Request\FlowAction;
use ExEss\Cms\FLW_Flows\Response;
use ExEss\Cms\FLW_Flows\Response\Model;

class FieldFormattingHandler extends AbstractSuggestionHandler
{
    protected PhoneNumberFormatter $phoneNumberFormatter;

    protected VatNumberFormatter $vatNumberFormatter;

    public function __construct(VatNumberFormatter $vatNumberFormatter, PhoneNumberFormatter $phoneNumberFormatter)
    {
        $this->phoneNumberFormatter = $phoneNumberFormatter;
        $this->vatNumberFormatter = $vatNumberFormatter;
    }

    /**
     * Should handle when ;
     *
     *  - event === EVENT::EVENT_CONFIRM && the model has PHONE keys OR VAT keys
     *  - event === EVENT::EVENT_CHANGED && the focus is on PHONE OR VAT
     *
     * @inheritdoc
     */
    public static function shouldHandle(Response $response, FlowAction $action, Flow $flow): bool
    {
        $model = $response->getModel();

        return
            (
                $action->getEvent() === FlowAction::EVENT_CONFIRM
                && (!empty(self::getPhoneFieldKeys($model)) || !empty(self::getVatFieldKeys($model)))
            )

            ||

            (
                $response->getModel()
                && $action->getEvent() === FlowAction::EVENT_CHANGED
                && $action->getFocus()
                && $model->hasNonEmptyValueFor($action->getFocus())
                && (self::phoneFieldHasFocus($action) || self::vatNumberFieldHasFocus($action))
            );
    }

    protected function doHandle(Response $response, FlowAction $action, Flow $flow): void
    {
        $model = $response->getModel();

        if ($action->getEvent() === FlowAction::EVENT_CONFIRM) {
            foreach (self::getPhoneFieldKeys($response->getModel()) as $key) {
                $this->doPhoneFormattingOn($key, $response->getModel());
            }

            foreach (self::getVatFieldKeys($response->getModel()) as $key) {
                $this->doVatNumberFormattingOn($key, $model);
            }
        } else {
            if (self::phoneFieldHasFocus($action)) {
                $this->doPhoneFormattingOn($action->getFocus(), $response->getModel());
            }

            if (self::vatNumberFieldHasFocus($action)) {
                $this->doVatNumberFormattingOn($action->getFocus(), $response->getModel());
            }
        }
    }

    /**
     * Does the formatting of a phone number on a certain field.
     */
    private function doPhoneFormattingOn(string $field, Model $model): void
    {
        $phoneNumber = $this->phoneNumberFormatter->format($model->{$field});

        if ($this->phoneNumberFormatter->isValid($phoneNumber)) {
            $model->{$field} = $phoneNumber;
        }
    }

    private function doVatNumberFormattingOn(string $field, Model $model): void
    {
        $vatNumber = $this->vatNumberFormatter->format($model->{$field});

        if ($this->vatNumberFormatter->isValid($vatNumber)) {
            $model->setFieldValue($field, $vatNumber);
        }
    }

    private static function phoneFieldHasFocus(FlowAction $action): bool
    {
        return \strpos($action->getFocus(), 'phone') !== false
            || \strpos($action->getFocus(), 'mobile') !== false;
    }

    private static function vatNumberFieldHasFocus(FlowAction $action): bool
    {
        return \strpos($action->getFocus(), 'company_number_c') !== false;
    }

    private static function getPhoneFieldKeys(Model $model): array
    {
        $keys = $model->findAllFieldKeys('contact_details_value');

        return \array_filter($keys, function ($key) {
            return false !== \strpos($key, 'mobile') || false !== \strpos($key, 'phone');
        });
    }

    private static function getVatFieldKeys(Model $model): array
    {
        return $model->findAllFieldKeys('company_number_c');
    }
}
