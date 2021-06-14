<?php declare(strict_types=1);

namespace ExEss\Cms\Adapter;

use ExEss\Cms\Adapter\Request\Request;
use ExEss\Cms\Adapter\Response\Response;
use ExEss\Cms\Adapter\Transport\Transport;
use ExEss\Cms\Api\V8_Custom\Params\Validator\ValidatorFactory;

abstract class AbstractRequestAdapter implements RequestAdapter
{
    private Transport $transport;

    protected ValidatorFactory $validatorFactory;

    protected \JsonMapper $jsonMapper;

    public function __construct(Transport $transport, ValidatorFactory $validatorFactory, \JsonMapper $jsonMapper)
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
