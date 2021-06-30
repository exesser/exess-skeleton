<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\CRUD\Config;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\Proxy;
use ExEss\Bundle\CmsBundle\Dictionary\Format;
use ExEss\Bundle\CmsBundle\Entity;
use ReflectionClass;

class CrudMetadata
{
    // @todo move to a yaml config file
    private const MAPPING = [
        Entity\Dashboard::class => [
            'suffixOnDuplicate' => ["name", "key"],
            'quickSearch' => ["name", "key"],
            'c2r1' => ['key', 'mainRecordType', 'gridTemplate|key'],
        ],
        Entity\AclAction::class => [
            'quickSearch' => ["name", "category"],
            'c2r1' => 'category',
            'c2r2' => 'aclAccess',
        ],
        Entity\FilterField::class => [
            'order' => 'sort',
            'c2r1' => 'sort',
        ],
        Entity\FilterFieldGroup::class => [
            'order' => 'sort',
            'c2r1' => 'sort',
        ],
        Entity\Flow::class => [
            'quickSearch' => ["name", "key"],
            'c2r1' => 'key',
        ],
        Entity\FlowAction::class => [
            'quickSearch' => ["name", "guid"],
            'c2r1' => 'guid',
        ],
        Entity\Property::class => [
            'c2r1' => 'value',
        ],
        Entity\GridTemplate::class => [
            'suffixOnDuplicate' => ["name", "key"],
            'quickSearch' => ["name", "key"],
            'c2r1' => 'key',
        ],
        Entity\ListCellLink::class => [
            'order' => 'order',
            'c2r1' => 'order',
        ],
        Entity\Validator::class => [
            'c2r1' => ['validator', 'validatorValue'],
            'c2r2' => ['validatorMin', 'validatorMax'],
        ],
        Entity\FlowStepLink::class => [
            'order' => 'order',
            'c2r1' => 'order',
        ],
        Entity\ConfDefaults::class => [
            'quickSearch' => ["name", "systemId", "parameter", "value"],
            'c2r1' => ['systemId', 'parameter', 'value'],
        ],
        Entity\Translation::class => [
            'quickSearch' => ["name", "translation", "domain", "locale"],
            'order' => 'translation',
            'c2r1' => ['translation', 'domain', 'locale'],
        ],
        Entity\User::class => [
            'quickSearch' => ["userName", 'firstName', 'lastName'],
            'order' => 'userName',
            'c1r1' => 'userName',
            'c2r1' => 'status',
            'c2r2' => ['firstName', 'lastName']
        ],
        Entity\FlowField::class => [
            'quickSearch' => ['name', 'fieldId'],
            'order' => ['fieldGroup', 'order'],
            'c1r1' => 'fieldId',
            'c1r2' => 'label',
            'c2r1' => 'type',
            'c2r2' => ['fieldGroup', 'order'],
        ],
    ];

    public static function getOrder(string $entity): array
    {
        if (isset(self::MAPPING[$entity]['order'])) {
            $config = self::MAPPING[$entity]['order'];
            return \is_string($config) ? [$config] : $config;
        }

        // defaults to
        return ['name'];
    }

    public static function getQuickSearchFields(string $entity): array
    {
        if (isset(self::MAPPING[$entity]['quickSearch'])) {
            $config = self::MAPPING[$entity]['quickSearch'];
            return \is_string($config) ? [$config] : $config;
        }

        // defaults to
        return ['name'];
    }

    public static function getMakeUniqueOnDuplicateFields(string $entity): array
    {
        if (isset(self::MAPPING[$entity]['suffixOnDuplicate'])) {
            $config = self::MAPPING[$entity]['suffixOnDuplicate'];
            return \is_string($config) ? [$config] : $config;
        }

        // defaults to
        return ['name'];
    }

    public static function getCrudListC1R1(ClassMetadata $metadata, object $entity): ?string
    {
        if ($config = (self::MAPPING[$metadata->getName()]['c1r1'] ?? null)) {
            return self::getConfiguredValue($config, $metadata, $entity);
        }

        // defaults to
        $reflectionClass = $metadata->getReflectionClass();
        if ($metadata->hasField('name')
            && !empty($value = self::getValue($reflectionClass, $entity, 'name'))
        ) {
            return $value;
        }
        if ($metadata->hasField('dateEntered')
            && !empty($value = self::getValue($reflectionClass, $entity, 'dateEntered'))
        ) {
            return $metadata->getName() . ' - ' . $value->format(Format::DB_DATETIME_FORMAT);
        }

        $value = self::getValue($reflectionClass, $entity, 'id');
        if (\is_object($value)) {
            $value = $value->getId();
        }

        return \substr($value, 0, 8);
    }

    /**
     * @return mixed
     */
    public static function getCrudListC1R2(ClassMetadata $metadata, object $entity)
    {
        if ($config = (self::MAPPING[$metadata->getName()]['c1r2'] ?? null)) {
            return self::getConfiguredValue($config, $metadata, $entity);
        }

        // defaults to
        return '';
    }

    /**
     * @return mixed
     */
    public static function getCrudListC2R1(ClassMetadata $metadata, object $entity)
    {
        if ($config = (self::MAPPING[$metadata->getName()]['c2r1'] ?? null)) {
            return self::getConfiguredValue($config, $metadata, $entity);
        }

        // defaults to
        return '';
    }

    /**
     * @return mixed
     */
    public static function getCrudListC2R2(ClassMetadata $metadata, object $entity)
    {
        if ($config = (self::MAPPING[$metadata->getName()]['c2r2'] ?? null)) {
            return self::getConfiguredValue($config, $metadata, $entity);
        }

        // defaults to
        return '';
    }

    /**
     * @param string|array $config
     */
    private static function getConfiguredValue($config, ClassMetadata $metadata, object $entity): ?string
    {
        if (\is_string($config)) {
            $config = [$config];
        }

        // ensure data is loaded
        if ($entity instanceof Proxy) {
            $entity->__load();
        }
        $class = $metadata->getReflectionClass();
        $values = [];
        foreach ($config as $field) {
            $parts = \explode('|', $field);
            if (\count($parts) === 1) {
                $values[] = self::getValue($class, $entity, $field);
            } elseif (\count($parts) === 2) {
                // association, follow it, one deep max
                $association = $parts[0];
                if (!$metadata->hasAssociation($association)) {
                    throw new \DomainException("No association $association found");
                }
                $targetClass = $metadata->getAssociationTargetClass($association);
                $value = self::getValue($class, $entity, $association);
                if (\is_object($value)) {
                    // ensure data is loaded
                    if ($value instanceof Proxy) {
                        $value->__load();
                    }
                    $values[] = self::getValue(new ReflectionClass($targetClass), $value, $parts[1]);
                } else {
                    $values[] = $value;
                }
            }
        }

        return \implode(' - ', $values);
    }

    /**
     * @return mixed
     */
    private static function getValue(ReflectionClass $class, object $entity, string $field)
    {
        // no hasProperty check, let it throw exception
        $property = $class->getProperty($field);
        $property->setAccessible(true);

        return $property->getValue($entity);
    }
}
