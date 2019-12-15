<?php

namespace App\Repository;

use App\Entity\LspdRappArrest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method LspdRappArrest|null find($id, $lockMode = null, $lockVersion = null)
 * @method LspdRappArrest|null findOneBy(array $criteria, array $orderBy = null)
 * @method LspdRappArrest[]    findAll()
 * @method LspdRappArrest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LspdRappArrestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LspdRappArrest::class);
    }

    // /**
    //  * @return LspdRappArrest[] Returns an array of LspdRappArrest objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LspdRappArrest
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
