<?php declare(strict_types=1);

namespace ExEss\Cms\EventListener;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use ExEss\Cms\Api\V8_Custom\Service\Security;
use ExEss\Cms\Entity\Dashboard;
use ExEss\Cms\Entity\FlowStep;
use ExEss\Cms\Entity\GridTemplate;
use ExEss\Cms\Entity\Property;
use ExEss\Cms\Entity\User;

class PropertyUpdateListener
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @param Dashboard|FlowStep $entity
     */
    public function postUpdate($entity, LifecycleEventArgs $args): void
    {
        $this->process($entity, $args->getEntityManager());
    }

    /**
     * @param Dashboard|FlowStep $entity
     */
    public function postPersist($entity, LifecycleEventArgs $args): void
    {
        $this->process($entity, $args->getEntityManager());
    }

    /**
     * @param Dashboard|FlowStep $entity
     */
    private function process($entity, EntityManager $em): void
    {
        if (($grid = $entity->getGridTemplate()) === null) {
            return;
        }

        $properties = $entity->getProperties();
        if ($this->hasMatchingProperties($properties, $grid)) {
            return;
        }

        $this->deleteUnusedProperties($properties, $grid);

        foreach ($this->getMissingProperties($properties, $grid) as $propertyName) {
            $property = new Property();
            $property->setName($propertyName);
            $property->setCreatedBy($this->security->getCurrentUser() ?? $em->getRepository(User::class)->find('1'));

            $em->persist($property);

            $properties->add($property);
        }
    }

    private function hasMatchingProperties(Collection $properties, GridTemplate $grid): bool
    {
        $gridProperties = $this->getPropertiesFromGrid($grid);
        $existingProperties = $properties->map(function (Property $property) {
            return $property->getName();
        })->toArray();

        return $gridProperties === $existingProperties;
    }

    private function deleteUnusedProperties(Collection $properties, GridTemplate $grid): void
    {
        $gridProperties = $this->getPropertiesFromGrid($grid);
        foreach ($properties as $property) {
            if (!\in_array($property->getName(), $gridProperties, true)) {
                $properties->removeElement($property);
            }
        }
    }

    /**
     * @return array|string[]
     */
    private function getPropertiesFromGrid(GridTemplate $grid): array
    {
        $matches = [];
        $values = [];
        if (!empty($jsonFields = $grid->getJsonFields())
            && \preg_match_all('~({?%[^%]+%}?)~', \json_encode($jsonFields), $matches)
        ) {
            // we do not care about the first one, as this is not a property
            // and we want to allow Front End Expressions to go through unmodified to the DWP
            foreach ($matches[1] as $value) {
                if (!\preg_match('~\{\%[^%]+\%\}~', $value, $matches)) {
                    $values[] = \trim($value, '%');
                }
            }
        }

        return $values;
    }

    /**
     * @return array|string[]
     */
    private function getMissingProperties(Collection $properties, GridTemplate $grid): array
    {
        return \array_diff(
            $this->getPropertiesFromGrid($grid),
            $properties->map(function (Property $property) {
                return $property->getName();
            })->toArray()
        );
    }
}
