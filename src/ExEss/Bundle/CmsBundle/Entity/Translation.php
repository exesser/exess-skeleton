<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ExEss\Bundle\CmsBundle\Entity\Base\Entity;

/**
 * @ORM\Table(
 *     name="trans_translation",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="key_name_domain_description_locale", columns={
 *              "name",
 *              "domain",
 *              "description",
 *              "locale"
 *          })
 *     },
 *     indexes={
 *          @ORM\Index(name="key_locale", columns={"locale"}),
 *          @ORM\Index(name="fk_users_id_b5f886c4", columns={"created_by"}),
 *          @ORM\Index(name="key_domain", columns={"domain"}),
 *          @ORM\Index(name="fk_users_id_349381bf", columns={"modified_user_id"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="ExEss\Bundle\CmsBundle\Repository\TranslationRepository")
 */
class Translation extends Entity
{
    /**
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    protected ?string $description;

    /**
     * @ORM\Column(name="locale", type="enum_locale", nullable=true)
     */
    private ?string $locale;

    /**
     * @ORM\Column(name="translation", type="text", length=65535, nullable=true)
     */
    private ?string $translation = null;

    /**
     * @ORM\Column(name="domain", type="enum_translation_domain", nullable=true)
     */
    private ?string $domain;

    public function __construct(
        string $name,
        string $domain,
        string $locale,
        ?string $description = null
    ) {
        $this->name = $name;
        $this->domain = $domain;
        $this->locale = $locale;
        $this->description = $description;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function getTranslation(): ?string
    {
        return $this->translation;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setTranslation(string $translation): void
    {
        $this->translation = $translation;
    }
}
