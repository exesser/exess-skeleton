<?php

namespace ExEss\Cms\Api\V8_Custom\Params\Validator;

interface ValidatedParamsInterface
{
    public function getConstraints(): array;
    public function getTemporaryArguments(): array;
}
