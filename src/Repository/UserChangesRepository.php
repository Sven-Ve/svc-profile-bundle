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

namespace Svc\ProfileBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Svc\ProfileBundle\Entity\UserChanges;

/**
 * @method UserChanges|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserChanges|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserChanges[]    findAll()
 * @method UserChanges[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserChangesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserChanges::class);
    }
}
