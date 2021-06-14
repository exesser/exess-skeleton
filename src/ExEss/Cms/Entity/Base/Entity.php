<?php declare(strict_types=1);

namespace ExEss\Cms\Entity\Base;

use Doctrine\ORM\Mapping as ORM;
use ExEss\Cms\Component\Doctrine\Mapping\Annotation\Auditable;
use ExEss\Cms\Entity\User;

/**
 * @ORM\MappedSuperclass()
 * @Auditable
 */
abstract class Entity
{
    /**
     * @ORM\Column(name="id", type="string", length=36, nullable=false, options={"fixed"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected string $id;

    /**
     * @ORM\Column(name="date_entered", type="datetime", nullable=false)
     */
    protected \DateTimeInterface $dateEntered;

    /**
     * @ORM\Column(name="date_modified", type="datetime", nullable=true)
     */
    protected ?\DateTimeInterface $dateModified = null;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=false)
     * })
     */
    protected User $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="modified_user_id", referencedColumnName="id", nullable=true)
     * })
     */
    protected ?User $modifiedUser = null;

    /**
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    protected ?string $name = null;

    /**
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    protected ?string $description = null;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getDateEntered(): \DateTimeInterface
    {
        return $this->dateEntered;
    }

    public function setDateEntered(\DateTimeInterface $dateEntered): self
    {
        $this->dateEntered = $dateEntered;

        return $this;
    }

    public function getDateModified(): ?\DateTimeInterface
    {
        return $this->dateModified;
    }

    public function setDateModified(?\DateTimeInterface $dateModified): self
    {
        $this->dateModified = $dateModified;

        return $this;
    }

    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getModifiedUser(): ?User
    {
        return $this->modifiedUser;
    }

    public function setModifiedUser(User $modifiedUser): self
    {
        $this->modifiedUser = $modifiedUser;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
