<?php
namespace ExEss\Cms\FLW_Flows\ResponseHandler;

use ExEss\Cms\Api\V8_Custom\Events\FlowEvent;
use ExEss\Cms\FLW_Flows\Response\Form;

/**
 * Removes all hidden fields from the response form (if set)
 */
class HiddenFieldRemoveHandler implements Handler
{
    /**
     * @inheritdoc
     */
    public function shouldModify(FlowEvent $event): bool
    {
        return $event->getResponse()->getForm() !== null;
    }

    public function __invoke(FlowEvent $event): void
    {
        /** @var Form $form */
        $form = $event->getResponse()->getForm();

        if (!$form instanceof Form) {
            return;
        }

        foreach ($form->getGroups() as $group) {
            if (!isset($group->fields) || !\is_array($group->fields)) {
                continue;
            }

            foreach ($group->fields as $key => $field) {
                if (isset($field->type) && $field->type === 'hidden') {
                    unset($group->fields[$key]);
                }
            }

            $group->fields = \array_values($group->fields);
        }
    }
}
