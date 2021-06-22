<?php declare(strict_types=1);

namespace ExEss\Cms\Entity\Behavior;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ExEss\Bundle\DoctrineExtensionsBundle\Mapping\Annotation\Auditable;
use ExEss\Cms\Entity\SecurityGroup;

trait SecurityGroups
{
    /**
     * @ORM\ManyToMany(targetEntity="SecurityGroup")
     * @ORM\JoinTable(
     *     inverseJoinColumns={@ORM\JoinColumn(name="security_group_id", referencedColumnName="id")}
     * )
     * @Auditable
     * @var Collection|SecurityGroup[]
     */
    protected Collection $securityGroups;

    public function getSecurityGroups(): Collection
    {
        return $this->securityGroups ?? new ArrayCollection();
    }

    /**
     * @param Collection|SecurityGroup[] $securityGroups
     */
    public function setSecurityGroups(Collection $securityGroups): self
    {
        $this->securityGroups = $securityGroups;

        return $this;
    }

    public function addSecurityGroup(SecurityGroup $securityGroup): self
    {
        $this->securityGroups = $this->securityGroups ?? new ArrayCollection();
        $this->securityGroups->add($securityGroup);

        return $this;
    }
}
