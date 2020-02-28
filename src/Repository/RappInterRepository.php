<?php

namespace App\Repository;

use App\Entity\RappInter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method RappInter|null find($id, $lockMode = null, $lockVersion = null)
 * @method RappInter|null findOneBy(array $criteria, array $orderBy = null)
 * @method RappInter[]    findAll()
 * @method RappInter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RappInterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RappInter::class);
    }

    // /**
    //  * @return RappInter[] Returns an array of RappInter objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RappInter
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
