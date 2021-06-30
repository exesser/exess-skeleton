<?php
namespace ExEss\Bundle\CmsBundle\Component\Flow\Suggestions;

use ExEss\Bundle\CmsBundle\Entity\Flow;
use ExEss\Bundle\CmsBundle\FieldFormatter\IbanFormatter;
use ExEss\Bundle\CmsBundle\Component\Flow\Request\FlowAction;
use ExEss\Bundle\CmsBundle\Component\Flow\Response;

class IbanFormatterSuggestionHandler extends AbstractSuggestionHandler
{
    protected const IBAN = 'iban';

    protected IbanFormatter $ibanFormatter;

    public function __construct(IbanFormatter $ibanFormatter)
    {
        $this->ibanFormatter = $ibanFormatter;
    }

    /**
     * @inheritdoc
     */
    public static function shouldHandle(Response $response, FlowAction $action, Flow $flow): bool
    {
        $model = $response->getModel();

        if (false === $model->hasNonEmptyValueFor(self::IBAN)) {
            return false;
        }

        // always run on confirm && init when iban is present
        if (\in_array($action->getEvent(), [FlowAction::EVENT_INIT, FlowAction::EVENT_CONFIRM], true)) {
            return true;
        }

        $ibanKey = $model->findFieldKey(self::IBAN);

        // if event is not init || confirm, run when focus is iban
        return $action->getFocus() === $ibanKey;
    }

    /**
     * @inheritdoc
     */
    protected function doHandle(Response $response, FlowAction $action, Flow $flow): void
    {
        $model = $response->getModel();
        $iban = $model->getFieldValue(self::IBAN);

        if (\strlen($iban) < 15) {
            return;
        }

        $model->setFieldValue(self::IBAN, $this->ibanFormatter->format($iban));
    }
}
