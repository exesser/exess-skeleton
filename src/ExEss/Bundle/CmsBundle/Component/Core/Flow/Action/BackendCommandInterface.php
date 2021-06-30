<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\Core\Flow\Action;

use ExEss\Bundle\CmsBundle\Component\Flow\Response\Model;

interface BackendCommandInterface
{
    public function execute(array $recordIds, ?Model $model = null): void;
}
