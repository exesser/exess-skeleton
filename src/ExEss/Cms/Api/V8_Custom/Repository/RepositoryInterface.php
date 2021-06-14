<?php
namespace ExEss\Cms\Api\V8_Custom\Repository;

interface RepositoryInterface
{
    /**
     * @return mixed
     * @throws \Exception When the method is not implemented or something went wrong.
     */
    public function findBy(array $requestData);

    /**
     * @return mixed
     * @throws \Exception When the method is not implemented or something went wrong.
     */
    public function findOneBy(array $requestData);

    /**
     * @return void|object
     */
    public function getRequest(array $requestData);

    public function combineCalls(): bool;
}
