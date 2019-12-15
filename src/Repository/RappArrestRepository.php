<?php

namespace App\Repository;

use App\Entity\RappArrest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method RappArrest|null find($id, $lockMode = null, $lockVersion = null)
 * @method RappArrest|null findOneBy(array $criteria, array $orderBy = null)
 * @method RappArrest[]    findAll()
 * @method RappArrest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RappArrestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RappArrest::class);
    }

    // /**
    //  * @return RappArrest[] Returns an array of RappArrest objects
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
    public function findOneBySomeField($value): ?RappArrest
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
