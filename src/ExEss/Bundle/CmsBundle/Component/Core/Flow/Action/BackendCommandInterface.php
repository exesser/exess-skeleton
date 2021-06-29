<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\Core\Flow\Action;

use ExEss\Cms\Component\Flow\Response\Model;

interface BackendCommandInterface
{
    public function execute(array $recordIds, ?Model $model = null): void;
}
