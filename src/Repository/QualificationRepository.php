<?php

namespace App\Repository;

use App\Entity\Qualification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Qualification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Qualification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Qualification[]    findAll()
 * @method Qualification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QualificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Qualification::class);
    }
}
