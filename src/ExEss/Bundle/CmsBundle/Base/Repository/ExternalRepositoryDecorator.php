<?php
namespace ExEss\Bundle\CmsBundle\Base\Repository;

use ExEss\Bundle\CmsBundle\Api\V8_Custom\Repository\RepositoryInterface;

abstract class ExternalRepositoryDecorator implements RepositoryInterface
{
    protected RepositoryInterface $externalRepository;

    public function __construct(RepositoryInterface $externalRepository)
    {
        $this->externalRepository = $externalRepository;
    }

    /**
     * @inheritdoc
     */
    public function findBy(array $requestData)
    {
        return $this->externalRepository->findBy($requestData);
    }

    /**
     * @inheritdoc
     */
    public function findOneBy(array $requestData)
    {
        return $this->externalRepository->findOneBy($requestData);
    }

    public function combineCalls(): bool
    {
        return $this->externalRepository->combineCalls();
    }
}
