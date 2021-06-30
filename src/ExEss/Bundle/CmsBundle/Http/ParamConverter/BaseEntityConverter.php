<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Http\ParamConverter;

use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class BaseEntityConverter implements ParamConverterInterface
{
    public const ARGUMENT_NAME = 'baseEntity';

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function supports(ParamConverter $configuration): bool
    {
        return $configuration->getName() === self::ARGUMENT_NAME;
    }

    public function apply(Request $request, ParamConverter $configuration): void
    {
        if (!$request->attributes->has('record_type')
            || !$request->attributes->has('record_id')
        ) {
            return;
        }

        $entityName = $request->attributes->get('record_type');
        $entityId = $request->attributes->get('record_id');

        $request->attributes->set(
            $configuration->getName(),
            $this->em->getRepository($entityName)->find($entityId)
        );
    }
}
