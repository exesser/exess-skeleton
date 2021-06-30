<?php
namespace ExEss\Bundle\CmsBundle\Api\V8_Custom\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use ExEss\Bundle\CmsBundle\Entity\FlowAction;
use ExEss\Bundle\CmsBundle\Component\Flow\Action\Arguments;
use ExEss\Bundle\CmsBundle\Component\Flow\Action\Command;
use ExEss\Bundle\CmsBundle\Component\Flow\Response\Model;
use ExEss\Bundle\CmsBundle\Helper\DataCleaner;
use ExEss\Bundle\CmsBundle\Logger\Logger;

class SimpleActionFactory
{
    private Logger $logger;

    private EntityManager $em;

    public function __construct(EntityManager $em, Logger $logger)
    {
        $this->logger = $logger;
        $this->em = $em;
    }

    /**
     * @param null|string|FlowAction $action FLW_Action or id or guid.
     */
    public function getCommand(
        $action,
        array $recordIds = [],
        ?string $recordType = null,
        array $params = []
    ): ?Command {
        if (empty($action)) {
            return null;
        }

        if (!$action instanceof FlowAction) {
            $action = $this->findAction($action);
            if (!$action) {
                return null;
            }
        }

        $json = \json_encode($action->getJson());

        if (!empty($params['recordIds']) && empty($params['recordId'])) {
            $params['recordId'] = \current($params['recordIds']);
        }

        foreach ($params as $paramKey => $paramValue) {
            if ($paramValue instanceof Model) {
                foreach ($paramValue as $modelKey => $modelValue) {
                    if ($modelValue instanceof Model) {
                        continue;
                    }
                    $json = \str_replace('%' . $modelKey . '%', \str_replace('\\', '\\\\', $modelValue), $json);
                }
                continue;
            }

            if (\is_string($paramValue)) {
                $json = \str_replace('%' . $paramKey . '%', \str_replace('\\', '\\\\', $paramValue), $json);
            }
        }
        $data = DataCleaner::jsonDecode($json, false);
        if (!$data) {
            return null;
        }

        if (!empty($data->arguments->params->recordType) && empty($recordType)) {
            $recordType = $data->arguments->params->recordType;
        }

        $this->handleJson($data);

        return new Command(
            $data->command,
            $this->getArguments($data, $params, $recordIds, $recordType),
            $data->backendCommand ?? null,
            $data->relatedBeans ?? []
        );
    }

    private function findAction(string $actionKey): ?FlowAction
    {
        try {
            return $this->em->getRepository(FlowAction::class)->get($actionKey);
        } catch (NoResultException $e) {
            if ($action = $this->em->getRepository(FlowAction::class)->find($actionKey)) {
                return $action;
            }

            $this->logger->error(\sprintf(
                'We did not find an action with this key: %s [%s]',
                $actionKey,
                __METHOD__
            ));
            return null;
        }
    }

    /**
     * @throws \LogicException In case of failure.
     * @throws \InvalidArgumentException In case of invalid arguments.
     */
    private function getArguments(
        \stdClass $data,
        array $params,
        array $recordIds,
        ?string $recordType = null
    ): Arguments {
        $arguments = new Arguments();
        $arguments->recordIds = $recordIds;

        if (!isset($data->arguments)) {
            return $arguments;
        }

        foreach ($data->arguments as $key => $value) {
            $arguments->$key = $value;
        }

        if (
            isset($arguments->params, $arguments->params->model)
            && !empty($params['model'])
        ) {
            $model = $params['model'];
            if (!$model instanceof Model) {
                $model = new Model($model);
            }

            if ($arguments->params->model instanceof \stdClass) {
                foreach ($arguments->params->model as $newModelKey => $oldModelKey) {
                    if (\is_string($oldModelKey) && $model->hasField($oldModelKey)) {
                        $arguments->params->model->{$newModelKey} = $model->findFieldValue($oldModelKey);
                    }
                }
            } else {
                $arguments->params->model = $model;
            }
        }

        $this->handleArguments($arguments, $recordIds, $recordType, $params);

        if (
            isset($arguments->params)
            && \count($recordIds) === 1
            && !empty($recordIds[0])
        ) {
            // we only support PARAMS on 1 fat entity, because on this level it is only currently used for
            // navigate to a specific fat entity page. So we expect that the recordIds is an exact count of 1
            $arguments->params->recordId = $recordIds[0];
            $arguments->params->recordType = $recordType;
        }

        return $arguments;
    }

    protected function handleJson(\stdClass $data): \stdClass
    {
        return $data;
    }

    protected function handleArguments(Arguments $arguments, array $recordIds, ?string $recordType, array $params): void
    {
        // do nothing
    }
}
