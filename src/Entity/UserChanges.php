<?php

namespace Svc\ProfileBundle\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Svc\ProfileBundle\Repository\UserChangesRepository;

/**
 * @ORM\Entity(repositoryClass=UserChangesRepository::class)
 */
class UserChanges
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="smallint")
     */
    private $changeType;

    /**
    * @ORM\Column(type="datetime")
    */
    private $expiresAt;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $newMail;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getChangeType(): ?int
    {
        return $this->changeType;
    }

    public function setChangeType(int $changeType): self
    {
        $this->changeType = $changeType;

        return $this;
    }

    public function getNewMail(): ?string
    {
        return $this->newMail;
    }

    public function setNewMail(?string $newMail): self
    {
        $this->newMail = $newMail;

        return $this;
    }

    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(\DateTimeInterface $expiresAt): self
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }
}
