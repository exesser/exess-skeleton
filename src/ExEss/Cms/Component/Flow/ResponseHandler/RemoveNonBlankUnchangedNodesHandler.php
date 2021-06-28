<?php
namespace ExEss\Cms\Component\Flow\ResponseHandler;

use ExEss\Cms\Component\Flow\Event\FlowEvent;
use ExEss\Cms\Component\Flow\Response\Model;

class RemoveNonBlankUnchangedNodesHandler implements Handler
{
    /**
     * @inheritdoc
     */
    public function shouldModify(FlowEvent $event): bool
    {
        $params = $event->getParams();

        return isset($params['returnDelta'], $params['origModel'])
            && $params['returnDelta']
            && $params['origModel'] instanceof Model
        ;
    }

    public function __invoke(FlowEvent $event): void
    {
        $response = $event->getResponse();

        $modelDiff = $this->removeNonBlankUnchangedNodes(
            $response->getModel()->toArray(),
            $event->getParams()['origModel']->toArray()
        );

        // @todo this should not be creating a new Model but change the existing one
        $response->setModel($modelDiff !== null && $modelDiff !== '' ? new Model($modelDiff) : new Model);
    }

    /**
     * @param array|string $oldModel
     * @return array|null|string
     */
    private function removeNonBlankUnchangedNodes(array $model, $oldModel)
    {
        // both models have the exact same structure!
        $didWeUnset = false;
        $keys = \array_keys($model); //Cache the keys, since the unset does nasty stuff

        foreach ($keys as $key) {
            $inOldModel = !empty($oldModel) && \is_array($oldModel) && \array_key_exists($key, $oldModel);

            if ($inOldModel && $model[$key] === $oldModel[$key]) {
                unset($model[$key]);
                $didWeUnset = true;
            } elseif ($inOldModel && \is_array($model[$key])) {
                $newValue = $this->removeNonBlankUnchangedNodes($model[$key], $oldModel[$key]);
                if (null === $newValue) {
                    unset($model[$key]);
                    $didWeUnset = true;
                    continue;
                }
                $model[$key] = $newValue;
            }
        }

        if (empty($model) && $didWeUnset) {
            return null;
        }

        if (empty($model)) {
            return '';
        }

        return $model;
    }
}
