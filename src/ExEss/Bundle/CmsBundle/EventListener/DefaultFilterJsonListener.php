<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\EventListener;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use ExEss\Bundle\CmsBundle\Entity\Filter;
use ExEss\Bundle\CmsBundle\Entity\FilterField;
use ExEss\Bundle\CmsBundle\Entity\FilterFieldGroup;
use ExEss\Bundle\CmsBundle\Service\FilterService;

class DefaultFilterJsonListener
{
    private FilterService $filterService;

    public function __construct(FilterService $filterService)
    {
        $this->filterService = $filterService;
    }

    /**
     * @param FilterField|FilterFieldGroup $entity
     */
    public function postUpdate($entity, LifecycleEventArgs $args): void
    {
        $this->process($entity, $args->getEntityManager());
    }

    /**
     * @param FilterField|FilterFieldGroup $entity
     */
    public function postPersist($entity, LifecycleEventArgs $args): void
    {
        $this->process($entity, $args->getEntityManager());
    }

    /**
     * @param FilterField|FilterFieldGroup $entity
     */
    private function process($entity, EntityManager $em): void
    {
        foreach ($this->getUniqueFiltersFor($entity) as $filter) {
            $filterNew = $this->filterService->generateFilterModelAndForm(
                $filter,
                false
            )['model'];

            if ($filter->getDefaultFiltersJson() !== $filterNew) {
                $filter->setDefaultFiltersJson($filterNew);
                $em->persist($filter);

                foreach ($filter->getLists() as $list) {
                    $list->setFiltersHaveChanged(true);
                    $em->persist($list);
                }
            }
        }
    }

    /**
     * @param FilterField|FilterFieldGroup $entity
     * @return Collection|Filter[]
     */
    protected static function getUniqueFiltersFor($entity): Collection
    {
        $filters = new ArrayCollection();

        foreach ($entity instanceof FilterField ? $entity->getGroups() : [$entity] as $group) {
            foreach ($group->getFilters() as $filter) {
                $filters[$filter->getId()] = $filter;
            }
        }

        return $filters;
    }
}
