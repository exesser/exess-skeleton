<?php
namespace ExEss\Bundle\CmsBundle\Component\Flow\Handler;

use ExEss\Bundle\CmsBundle\Collection\ObjectCollection;
use ExEss\Bundle\CmsBundle\Dictionary\Model\Dwp;
use ExEss\Bundle\CmsBundle\Entity\Flow;
use ExEss\Bundle\CmsBundle\Component\Flow\Response\Model;

class FlowData
{
    private ObjectCollection $entities;

    private Flow $flow;

    private Model $model;

    private array $convertedModel;

    private ?Model $parentModel;

    public function __construct(
        Flow $flow,
        ?Model $model = null,
        ?Model $parentModel = null,
        ?ObjectCollection $entities = null
    ) {
        $this->model = $model ?? new Model();
        $this->parentModel = $parentModel;
        $this->flow = $flow;
        $this->entities = $entities ?? new ObjectCollection(ObjectCollection::class, []);
    }

    public function getFlowKey(): string
    {
        return $this->flow->getKey();
    }

    /**
     * Make Model Readonly Again
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * @return ObjectCollection|ObjectCollection[]
     */
    public function getEntities(): ObjectCollection
    {
        return $this->entities;
    }

    /**
     * @param ObjectCollection|ObjectCollection[] $entities
     */
    public function setEntities(ObjectCollection $entities): FlowData
    {
        $this->entities = $entities;

        return $this;
    }

    public function getConvertedModel(): array
    {
        return $this->convertedModel;
    }

    public function setConvertedModel(array $convertedModel): void
    {
        $this->convertedModel = $convertedModel;
    }

    public function getFlow(): Flow
    {
        return $this->flow;
    }

    public function getBaseModuleName(): ?string
    {
        return $this->model->getFieldValue('baseModule', null, true);
    }

    public function getBaseModuleBeanList(): ?ObjectCollection
    {
        if ($this->getBaseModuleName() && $this->getEntities()->offsetExists($this->getBaseModuleName())) {
            $list = $this->getEntities()[$this->getBaseModuleName()];
            $list->rewind(); //Always rewind before handing it off to guarantee consistent behaviour
            return $list;
        }

        return null;
    }

    public function getBaseModuleBean(): ?object
    {
        return $this->getBaseModuleBeanList() ? $this->getBaseModuleBeanList()->current() : null;
    }

    public function getRecordId(): ?string
    {
        $entities = $this->getEntities();
        if (!\count($entities)) {
            return null;
        }
        $returnModule = $this->model->getFieldValue(Dwp::RETURN_MODULE, null, true);
        if (isset($returnModule, $entities[$returnModule][0])) {
            return $entities[$returnModule][0]->getId();
        }

        if ($baseFatEntity = $this->getBaseModuleBean()) {
            return $baseFatEntity->getId();
        }

        return null;
    }

    public function getParentModel(): ?Model
    {
        return $this->parentModel;
    }

    public function setParentModel(?Model $parentModel = null): void
    {
        $this->parentModel = $parentModel;
    }
}
