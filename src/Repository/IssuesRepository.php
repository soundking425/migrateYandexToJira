<?php

namespace App\Repository;

use App\Entity\Issues;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Issues>
 *
 * @method Issues|null find($id, $lockMode = null, $lockVersion = null)
 * @method Issues|null findOneBy(array $criteria, array $orderBy = null)
 * @method Issues[]    findAll()
 * @method Issues[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IssuesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Issues::class);
    }

    public function create($issue)
    {
        $issues = new Issues();
        $createdAt = new \DateTimeImmutable($issue['createdAt']);

        $issues
            ->setTitle($issue['summary'])
            ->setDescription($issue['description'] ?? '')
            ->setDescriptionHtml($issue['descriptionHtml'] ?? '')
            ->setKey($issue['key'])
            ->setKeyYandex($issue['key'])
            ->setIdYandex($issue['id'])
            ->setStatus($issue['status']['display'])
            ->setParent($issue['parent']['key'] ?? null)
            ->setCreatedAt($createdAt)
            ->setType($issue['type'] ?? null)
            ->setEpic($issue['epic']['key'] ?? null)
            ->setPriority($issue['priority'])
            ->setQueue($issue['queue'])
            ->setAttachments($issue['attachments'] ?? null)
            ->setBoards($issue['boards']['name'] ?? null)
            ->setComponents($issue['components'] ?? null)
            ->setProject($issue['queue']['key'])
        ;

        $this->getEntityManager()->persist($issues);
        $this->getEntityManager()->flush();
    }

    public function findAttachments()
    {
        return $this->createQueryBuilder("i")
            ->andWhere('i.attachments IS NOT NULL')
            ->andWhere('i.fileLoad IS NULL')
            ->orderBy('i.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function saveIssue(Issues $issue)
    {
        $this->getEntityManager()->persist($issue);
        $this->getEntityManager()->flush();
    }

    public function findAllSortCreate()
    {
        return $this->createQueryBuilder("i")
//            ->andWhere('i.id = 4838')

//            ->andWhere('i.key = :val')
//            ->setParameter('val', 'MED-2114')

            ->andWhere('i.project != :val')
            ->setParameter('val', 'LGEXP')
//
//            ->andWhere('i.components IS NOT NULL')
//            ->andWhere('i.fileLoad IS NOT NULL')

            ->orderBy('i.createdAt', 'ASC')
//            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

    public function findOneKey($key)
    {
        return $this->createQueryBuilder("i")
            ->andWhere('i.key = :val')
            ->setParameter('val', $key)
            ->getQuery()
            ->getOneOrNullResult();
    }

//    /**
//     * @return Issues[] Returns an array of Issues objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Issues
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
