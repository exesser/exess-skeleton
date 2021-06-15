<?php declare(strict_types=1);

namespace ExEss\Cms\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
use ExEss\Cms\Doctrine\Type\Locale;
use ExEss\Cms\Component\Doctrine\Mapping\Annotation\Auditable;
use ExEss\Cms\Component\Session\User\UserInterface as CmsUserInterface;
use ExEss\Cms\Doctrine\Type\SecurityGroupType;
use Symfony\Component\Security\Core\Encoder\SodiumPasswordEncoder;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(
 *     name="users",
 *     indexes={
 *          @ORM\Index(name="idx_user_name", columns={"user_name", "is_group", "status", "last_name", "first_name"})
 *     }
 * )
 * @ORM\Entity
 * @Auditable
 */
class User implements UserInterface, CmsUserInterface
{
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const USERNAME_ADMIN = 'superadmin';

    /**
     * @ORM\Column(name="id", type="string", length=36, nullable=false, options={"fixed"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private string $id;

    /**
     * @ORM\Column(name="user_name", type="string", length=60, nullable=true)
     */
    private ?string $userName = null;

    /**
     * @ORM\Column(name="user_hash", type="string", length=255, nullable=true)
     */
    private ?string $userHash = null;

    /**
     * @ORM\Column(name="salt", type="string", length=40)
     */
    private string $salt;

    /**
     * @ORM\Column(name="system_generated_password", type="boolean", nullable=true)
     */
    private ?bool $systemGeneratedPassword = null;

    /**
     * @ORM\Column(name="pwd_last_changed", type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $pwdLastChanged = null;

    /**
     * @ORM\Column(name="sugar_login", type="boolean", nullable=true, options={"default"="1"})
     */
    private ?bool $sugarLogin = true;

    /**
     * @ORM\Column(name="first_name", type="string", length=30, nullable=true)
     */
    private ?string $firstName = null;

    /**
     * @ORM\Column(name="last_name", type="string", length=30, nullable=true)
     */
    private ?string $lastName = null;

    /**
     * @ORM\Column(name="external_auth_only", type="boolean", nullable=true, options={"default":"0"})
     */
    private ?bool $externalAuthOnly = false;

    /**
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private ?string $description = null;

    /**
     * @ORM\Column(name="date_entered", type="datetime", nullable=false)
     */
    private \DateTimeInterface $dateEntered;

    /**
     * @ORM\Column(name="date_modified", type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $dateModified = null;

    /**
     * @ORM\Column(name="modified_user_id", type="string", length=36, nullable=true, options={"fixed"=true})
     */
    private ?string $modifiedUserId = null;

    /**
     * @ORM\Column(name="created_by", type="string", length=36, nullable=false, options={"fixed"=true})
     */
    private ?string $createdBy;

    /**
     * @ORM\Column(name="status", type="enum_user_status", nullable=true)
     */
    private ?string $status = null;

    /**
     * @ORM\Column(name="portal_only", type="boolean", nullable=true, options={"default":"0"})
     */
    private ?bool $portalOnly = false;

    /**
     * @ORM\Column(name="employee_status", type="string", length=100, nullable=true)
     */
    private ?string $employeeStatus = null;

    /**
     * @ORM\Column(name="is_group", type="boolean", nullable=true, options={"default":"0"})
     */
    private ?bool $isGroup = false;

    /**
     * @ORM\Column(name="selfcare_toc", type="boolean", nullable=false, options={"default":"0"})
     */
    private bool $selfcareToc = false;

    /**
     * @ORM\Column(name="preferred_locale", type="enum_locale", nullable=true, options={"default"="en_BE"})
     */
    private ?string $preferredLocale = Locale::DEFAULT;

    /**
     * @ORM\ManyToMany(targetEntity="AclRole", mappedBy="users")
     * @var Collection|AclRole[]
     */
    private Collection $roles;

    /**
     * @ORM\OneToOne(targetEntity="UserLogin", mappedBy="id")
     */
    private ?UserLogin $lastLogin;

    /**
     * @ORM\OneToOne(targetEntity="UserGuidanceRecovery", mappedBy="id")
     */
    private ?UserGuidanceRecovery $guidanceRecovery;

    /**
     * @ORM\OneToMany(targetEntity="SecurityGroupUser", mappedBy="user")
     * @var Collection|SecurityGroupUser[]
     */
    private Collection $userGroups;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->userGroups = new ArrayCollection();
        $this->salt = \sha1(\random_bytes(12));
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(?string $userName): self
    {
        $this->userName = $userName;

        return $this;
    }

    public function getEmail(): string
    {
        if (null === $this->getUserName() || false === \strpos($this->getUserName(), '@')) {
            throw new DomainException('Expected user to have at least one email address');
        }

        return $this->getUserName();
    }

    public function getUserHash(): ?string
    {
        return $this->userHash;
    }

    public function setUserHash(?string $userHash): self
    {
        $this->userHash = $userHash;

        return $this;
    }

    public function getSystemGeneratedPassword(): ?bool
    {
        return $this->systemGeneratedPassword;
    }

    public function setSystemGeneratedPassword(?bool $systemGeneratedPassword): self
    {
        $this->systemGeneratedPassword = $systemGeneratedPassword;

        return $this;
    }

    public function getPwdLastChanged(): ?\DateTimeInterface
    {
        return $this->pwdLastChanged;
    }

    public function setPwdLastChanged(?\DateTimeInterface $pwdLastChanged): self
    {
        $this->pwdLastChanged = $pwdLastChanged;

        return $this;
    }

    public function getSugarLogin(): ?bool
    {
        return $this->sugarLogin;
    }

    public function setSugarLogin(?bool $sugarLogin): self
    {
        $this->sugarLogin = $sugarLogin;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getName(): string
    {
        return \trim($this->getFirstName() . ' ' . $this->getLastName());
    }

    public function getExternalAuthOnly(): ?bool
    {
        return $this->externalAuthOnly;
    }

    public function setExternalAuthOnly(?bool $externalAuthOnly): self
    {
        $this->externalAuthOnly = $externalAuthOnly;

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

    public function getDateEntered(): ?\DateTimeInterface
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

    public function getModifiedUserId(): ?string
    {
        return $this->modifiedUserId;
    }

    public function setModifiedUserId(?string $modifiedUserId): self
    {
        $this->modifiedUserId = $modifiedUserId;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setCreatedBy(string $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPortalOnly(): ?bool
    {
        return $this->portalOnly;
    }

    public function setPortalOnly(?bool $portalOnly): self
    {
        $this->portalOnly = $portalOnly;

        return $this;
    }

    public function getEmployeeStatus(): ?string
    {
        return $this->employeeStatus;
    }

    public function setEmployeeStatus(?string $employeeStatus): self
    {
        $this->employeeStatus = $employeeStatus;

        return $this;
    }

    public function getIsGroup(): ?bool
    {
        return $this->isGroup;
    }

    public function setIsGroup(?bool $isGroup): self
    {
        $this->isGroup = $isGroup;

        return $this;
    }

    public function getSelfcareToc(): ?bool
    {
        return $this->selfcareToc;
    }

    public function setSelfcareToc(bool $selfcareToc): self
    {
        $this->selfcareToc = $selfcareToc;

        return $this;
    }

    public function getPreferredLocale(): ?string
    {
        return $this->preferredLocale;
    }

    public function setPreferredLocale(?string $preferredLocale): self
    {
        $this->preferredLocale = $preferredLocale;

        return $this;
    }

    public function getLastLogin(): ?UserLogin
    {
        return $this->lastLogin;
    }

    public function addRole(AclRole $role): void
    {
        $this->roles->add($role);
        $role->addUser($this);
    }

    public function getRoles(): array
    {
        $roles = ['ROLE_USER'];
        foreach ($this->roles as $role) {
            $roles[] = $role->getCode();
        }

        foreach ($this->userGroups as $group) {
            foreach ($group->getSecurityGroup()->getRoles() as $role) {
                $roles[] = $role->getCode();
            }
        }

        return \array_unique($roles);
    }

    public function hasRole(string $role): bool
    {
        return \in_array($role, $this->getRoles(), true);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('ROLE_ADMIN');
    }

    public function isAgent(): bool
    {
        return $this->hasMatchedUserGroup(
            [
                SecurityGroupType::DEALER,
                SecurityGroupType::EMPLOYEE,
                SecurityGroupType::THIRD_PARTY,
            ]
        );
    }

    public function hasMatchedUserGroup(array $types): bool
    {
        return \count(
            \array_filter(
                $this->userGroups->toArray(),
                function (SecurityGroupUser $userGroup) use ($types) {
                    return \in_array(
                        $userGroup->getSecuritygroup()->getType(),
                        $types,
                        true
                    ) || \in_array(
                        $userGroup->getSecuritygroup()->getCode(),
                        $types,
                        true
                    );
                }
            )
        ) > 0;
    }

    public function getUserGroups(): Collection
    {
        return $this->userGroups;
    }

    public function removeUserGroup(SecurityGroupUser $group): void
    {
        $this->userGroups->removeElement($group);
    }

    public function getPrimaryGroup(): ?SecurityGroup
    {
        /** @var SecurityGroupUser $group */
        foreach ($this->userGroups as $group) {
            if ($group->isPrimaryGroup()) {
                return $group->getSecurityGroup();
            }
        }
        return null;
    }

    public function getGuidanceRecovery(): ?UserGuidanceRecovery
    {
        return $this->guidanceRecovery;
    }

    public function hasRecoveryData(): bool
    {
        $recoveryData = $this->getGuidanceRecovery();

        return ($recoveryData instanceof UserGuidanceRecovery && !empty($recoveryData->getRecoveryData()));
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->userHash;
    }

    /**
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return $this->salt;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }

    /**
     * Generate a new hash from plaintext password
     * @todo move this
     */
    public static function getPasswordHash(string $password, string $salt): string
    {
        $encoder = new SodiumPasswordEncoder();
        return $encoder->encodePassword($password, $salt);
    }
}
