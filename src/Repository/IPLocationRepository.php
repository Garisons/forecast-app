<?php

namespace App\Repository;

use App\Entity\IPLocation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method IPLocation|null find($id, $lockMode = null, $lockVersion = null)
 * @method IPLocation|null findOneBy(array $criteria, array $orderBy = null)
 * @method IPLocation[]    findAll()
 * @method IPLocation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IPLocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IPLocation::class);
    }

    // /**
    //  * @return IPLocation[] Returns an array of IPLocation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?IPLocation
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
