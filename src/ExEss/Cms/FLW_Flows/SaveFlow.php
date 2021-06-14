<?php
namespace ExEss\Cms\FLW_Flows;

use ExEss\Cms\Entity\Flow;
use Psr\Container\ContainerInterface;
use ExEss\Cms\Collection\ObjectCollection;
use ExEss\Cms\FLW_Flows\Handler\FlowData;
use ExEss\Cms\FLW_Flows\Response\Model;

class SaveFlow
{
    // FIXME move these consts to their corresponding handlers.
    public const CRUD_RECORD_DETAILS = 'CrudRecordDetails';
    public const CRUD_EDIT = 'CrudRecordEdit';
    public const CRUD_CREATE = 'crud_record_create';
    public const CRUD_NEW_RELATION = 'CrudNewRelation';

    public const CRUD_RECORD_FLOWS = [self::CRUD_RECORD_DETAILS, self::CRUD_CREATE, self::CRUD_EDIT];

    private array $flowHandlerQueue;

    private ContainerInterface $container;

    public function __construct(ContainerInterface $container, array $flowHandlerQueue)
    {
        $this->container = $container;
        $this->flowHandlerQueue = $flowHandlerQueue;
    }

    /**
     * This function handles all flow saving and delegates flow specific functionality to its own class.
     */
    public function save(
        Flow $flow,
        Model $model,
        ?Model $parentModel = null,
        ?ObjectCollection $entities = null
    ): FlowData {
        $data = new FlowData($flow, $model, $parentModel, $entities);

        foreach ($this->flowHandlerQueue as $handlerName) {
            if ($handlerName::shouldHandle($data)) {
                /** @var \ExEss\Cms\FLW_Flows\Handler\SaveHandlerInterface $handler */
                $handler = $this->container->get($handlerName);
                $handler->handle($data);
            }
        }

        return $data;
    }
}
