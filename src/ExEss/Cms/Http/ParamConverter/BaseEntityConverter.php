<?php declare(strict_types=1);

namespace ExEss\Cms\Http\ParamConverter;

use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class BaseEntityConverter implements ParamConverterInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function supports(ParamConverter $configuration): bool
    {
        $options = $configuration->getOptions();

        return isset($options['record_type'], $options['record_id']);
    }

    public function apply(Request $request, ParamConverter $configuration): void
    {
        $entityName = $request->attributes->get($configuration->getOptions()['record_type']);
        $entityId = $request->attributes->get($configuration->getOptions()['record_id']);

        $entity = $this->em->getRepository($entityName)->find($entityId);

        $param = $configuration->getName();

        $request->attributes->set($param, $entity);
    }
}
