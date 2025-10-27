<?php

declare(strict_types=1);

/*
 * This file is part of the svc/profile-bundle.
 *
 * (c) 2025 Sven Vetter <dev@sv-systems.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Svc\ProfileBundle\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Svc\ProfileBundle\Repository\UserChangesRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UserChangesRepository::class)]
#[UniqueEntity(fields: ['hashedToken'], message: 'There is already an token')]
#[UniqueEntity(fields: ['id', 'changeType'], message: 'There is already an request for this user')]
class UserChanges
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\OneToOne()]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: 'smallint')]
    private ?int $changeType = null;

    #[ORM\Column()]
    private ?\DateTimeImmutable $expiresAt = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $newMail = null;

    #[ORM\Column(length: 100, unique: true, nullable: true)]
    private ?string $hashedToken = null;

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

    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(\DateTimeImmutable $expiresAt): self
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function getHashedToken(): ?string
    {
        return $this->hashedToken;
    }

    public function setHashedToken(string $hashedToken): self
    {
        $this->hashedToken = $hashedToken;

        return $this;
    }
}
