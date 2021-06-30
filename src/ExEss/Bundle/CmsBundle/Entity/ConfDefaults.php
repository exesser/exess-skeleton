<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ExEss\Bundle\CmsBundle\Entity\Base\Entity;

/**
 * @ORM\Table(name="conf_defaults", indexes={
 *     @ORM\Index(name="fk_users_id_35ba22c2", columns={"modified_user_id"}),
 *     @ORM\Index(name="fk_users_id_00d36994", columns={"created_by"})
 * })
 * @ORM\Entity
 */
class ConfDefaults extends Entity
{
    /**
     * @ORM\Column(name="systemid", type="string", length=255, nullable=true)
     */
    private ?string $systemId = null;

    /**
     * @ORM\Column(name="parameter", type="string", length=255, nullable=true)
     */
    private ?string $parameter = null;

    /**
     * @ORM\Column(name="value", type="text", length=0, nullable=true)
     */
    private ?string $value = null;
}
