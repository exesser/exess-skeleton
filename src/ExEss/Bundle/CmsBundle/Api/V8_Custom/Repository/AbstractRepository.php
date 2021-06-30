<?php
namespace ExEss\Bundle\CmsBundle\Api\V8_Custom\Repository;

abstract class AbstractRepository implements RepositoryInterface
{
    /**
     * @inheritdoc
     *
     * @throws \BadMethodCallException When the call is not implemented.
     */
    public function findBy(array $requestData)
    {
        throw new \BadMethodCallException('Not yet implemented');
    }

    /**
     * @inheritdoc
     *
     * @throws \BadMethodCallException When the call is not implemented.
     */
    public function findOneBy(array $requestData): object
    {
        throw new \BadMethodCallException('Not yet implemented');
    }

    /**
     * @inheritdoc
     */
    abstract public function getRequest(array $requestData);

    public function combineCalls(): bool
    {
        return false;
    }
}
