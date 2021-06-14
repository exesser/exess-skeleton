<?php declare(strict_types=1);

namespace ExEss\Cms\Entity;

use Doctrine\ORM\Mapping as ORM;
use ExEss\Cms\Component\Doctrine\Mapping\Annotation\Auditable;

/**
 * @ORM\Table(name="user_login")
 * @ORM\Entity
 * @Auditable
 */
class UserLogin
{
    /**
     * @ORM\Column(name="last_login", type="datetime", nullable=false)
     */
    private \DateTimeInterface $lastLogin;

    /**
     * @ORM\Column(name="jwt", type="text", length=65535, nullable=true)
     */
    private ?string $jwt = null;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="User", inversedBy="lastLogin")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id", referencedColumnName="id")
     * })
     */
    private User $id;

    public function __construct(User $id)
    {
        $this->id = $id;
    }

    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->lastLogin;
    }

    public function setLastLogin(\DateTimeInterface $lastLogin): self
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    public function getJwt(): ?string
    {
        return $this->jwt;
    }

    public function setJwt(?string $jwt): void
    {
        $this->jwt = $jwt;
    }

    public function getId(): ?User
    {
        return $this->id;
    }
}
