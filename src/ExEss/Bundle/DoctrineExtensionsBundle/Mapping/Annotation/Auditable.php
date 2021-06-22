<?php declare(strict_types=1);

namespace ExEss\Bundle\DoctrineExtensionsBundle\Mapping\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
final class Auditable extends Annotation
{
}
