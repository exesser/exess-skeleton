<?php declare(strict_types=1);

namespace ExEss\Cms\Entity;

use Doctrine\ORM\Mapping as ORM;
use ExEss\Cms\Component\Doctrine\Mapping\Annotation\Auditable;

/**
 * @ORM\Table(name="user_guidance_recovery")
 * @ORM\Entity
 * @Auditable
 */
class UserGuidanceRecovery
{
    /**
     * @ORM\Column(name="recovery_data", type="json", nullable=true)
     */
    private ?array $recoveryData = null;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="User", inversedBy="guidanceRecovery")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id", referencedColumnName="id")
     * })
     */
    private User $id;

    public function getRecoveryData(): ?array
    {
        return $this->recoveryData;
    }

    public function setRecoveryData(?array $recoveryData): self
    {
        $this->recoveryData = $recoveryData;

        return $this;
    }

    public function getId(): ?User
    {
        return $this->id;
    }

    public function setId(?User $id): void
    {
        $this->id = $id;
    }
}
