<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Adapter;

use ExEss\Bundle\CmsBundle\Adapter\Request\Request;
use ExEss\Bundle\CmsBundle\Adapter\Response\Response;
use ExEss\Bundle\CmsBundle\Adapter\Transport\Transport;
use ExEss\Bundle\CmsBundle\Api\V8_Custom\Params\Validator\ValidatorFactory;
use JsonMapper;

abstract class AbstractRequestAdapter implements RequestAdapter
{
    private Transport $transport;

    protected ValidatorFactory $validatorFactory;

    protected JsonMapper $jsonMapper;

    public function __construct(Transport $transport, ValidatorFactory $validatorFactory, JsonMapper $jsonMapper)
    {
        $this->transport = $transport;
        $this->validatorFactory = $validatorFactory;
        $this->jsonMapper = $jsonMapper;
    }

    protected function request(Request $request, ?\Closure $responseHandler = null): Response
    {
        return $this->transport->request($request, $responseHandler);
    }
}
