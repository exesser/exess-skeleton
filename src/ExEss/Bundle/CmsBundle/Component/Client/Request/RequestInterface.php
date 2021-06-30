<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\Client\Request;

interface RequestInterface
{
    /**
     * This is used in logging
     */
    public function getPath(): string;

    /**
     * @return mixed
     */
    public function getData();
}
