<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\EventListener;

use Doctrine\DBAL\Types\Type;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use ExEss\Bundle\DoctrineExtensionsBundle\Type\AbstractEnumType;

class EnumValueListener
{
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        $em = $args->getObjectManager();
        $metadata = $em->getClassMetadata(\get_class($entity));

        foreach ($metadata->getFieldNames() as $fieldName) {
            $type = $metadata->getTypeOfField($fieldName);
            $dbalType = Type::getType($type);

            if ($dbalType instanceof AbstractEnumType) {
                $property = $metadata->getReflectionClass()->getProperty($fieldName);
                $property->setAccessible(true);
                $value = $property->getValue($entity);

                // if value is null, we let it pass (we assume the property is type hinted correctly)
                if ($value !== null
                    && !$dbalType->accepts($value)
                ) {
                    throw new \RuntimeException(\sprintf(
                        'Property "%s" on entity "%s" with type "%s" contained '
                        .'an invalid value "%s" but allowed values are: %s',
                        $fieldName,
                        $metadata->getName(),
                        \get_class($dbalType),
                        $value,
                        \json_encode(\array_keys($dbalType::getValues()))
                    ));
                }
            }
        }
    }
}
