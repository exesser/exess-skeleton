<?php declare(strict_types=1);

namespace ExEss\Cms\Http\ParamConverter;

use ExEss\Cms\Helper\DataCleaner;
use ExEss\Cms\Http\Factory\JsonBodyFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class JsonBodyConverter implements ParamConverterInterface
{
    public const ARGUMENT_NAME = 'jsonBody';

    private JsonBodyFactory $factory;

    public function __construct(JsonBodyFactory $factory)
    {
        $this->factory = $factory;
    }

    public function supports(ParamConverter $configuration): bool
    {
        return $configuration->getName() === self::ARGUMENT_NAME;
    }

    public function apply(Request $request, ParamConverter $configuration): void
    {
        $request->attributes->set(
            $configuration->getName(),
            $this->factory
                ->create($configuration->getClass())
                ->configure(
                    DataCleaner::jsonDecode($request->getContent())
                )
        );
    }
}
