<?php
namespace ExEss\Cms\Api\V8_Custom\Events;

class FlowEvents
{
    public const INIT = 'flow.init';
    public const INIT_CHILD_FLOW = 'flow.init-child-flow';
    public const CHANGED = 'flow.changed';
    public const NEXT_STEP = 'flow.next-step';
    public const NEXT_STEP_FORCED = 'flow.next-step-forced';
    public const CONFIRM = 'flow.confirm';
    public const CONFIRM_CREATE_LIST_ROW = 'flow.confirm-create-list-row';

    public static function fromDwpEvent(string $dwpEvent): string
    {
        $const = \str_replace('-', '_', $dwpEvent);

        return \constant("self::$const");
    }
}
