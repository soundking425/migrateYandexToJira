<?php

namespace App\Repository;

use App\Entity\Queues;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Queues>
 *
 * @method Queues|null find($id, $lockMode = null, $lockVersion = null)
 * @method Queues|null findOneBy(array $criteria, array $orderBy = null)
 * @method Queues[]    findAll()
 * @method Queues[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QueuesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Queues::class);
    }

    //    /**
//     * @return Queues[] Returns an array of Queues objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('q.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Queues
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function create($title, $key, $pageCount)
    {
        $queues = new Queues();
        $queues
            ->setTitle($title)
            ->setKey($key)
            ->setPageCount($pageCount)
            ->setCurrentPage(1);
        $this->getEntityManager()->persist($queues);
        $this->getEntityManager()->flush();
    }

    public function save(Queues $queues)
    {
        $this->getEntityManager()->persist($queues);
        $this->getEntityManager()->flush();
    }

    public function upPage(Queues $queues)
    {
        $page = $queues->getCurrentPage();
        $queues
            ->setCurrentPage($page + 1);
        $this->getEntityManager()->persist($queues);
        $this->getEntityManager()->flush();
    }
}
