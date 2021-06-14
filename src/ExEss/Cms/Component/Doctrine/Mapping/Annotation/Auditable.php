<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Doctrine\Mapping\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
final class Auditable extends Annotation
{
}
