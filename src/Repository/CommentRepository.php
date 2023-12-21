<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

//    /**
//     * @return Comment[] Returns an array of Comment objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Comment
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function create($data, $issue, $issueId)
    {
        $comment = $this->createQueryBuilder('c')
            ->andWhere('c.idYandex = :val')
            ->setParameter('val', $data['id'])
            ->andWhere('c.issues = :issue')
            ->setParameter('issue', $issue)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        if (empty($comment)) {
            $comment = new Comment();
            $comment
                ->setIdYandex($data['id'])
                ->setText($data['text'] ?? null)
                ->setFile($data['attachments'] ?? null)
                ->setCreatedAt(new \DateTimeImmutable($data['createdAt']))
                ->setCreatedBy($data['createdBy'])
                ->setIssues($issue)
                ->setIssueId($issueId)
            ;
            $this->getEntityManager()->persist($comment);
            $this->getEntityManager()->flush();
        }
    }

    public function findAttachments(){
        return $this->createQueryBuilder("c")
            ->andWhere('c.file IS NOT NULL')
            ->andWhere('c.fileLoad IS NULL')
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

}
