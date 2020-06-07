<?php

namespace App\Repository;

use App\Entity\Prgram;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Prgram|null find($id, $lockMode = null, $lockVersion = null)
 * @method Prgram|null findOneBy(array $criteria, array $orderBy = null)
 * @method Prgram[]    findAll()
 * @method Prgram[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrgramRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Prgram::class);
    }

    // /**
    //  * @return Prgram[] Returns an array of Prgram objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Prgram
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
