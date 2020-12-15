<?php

namespace App\Repository;

use App\Entity\Commentary;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Commentary|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commentary|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commentary[]    findAll()
 * @method Commentary[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentaryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commentary::class);
    }

    public function findCommentariesByPost($postid) { //Retrouve les commentaires liés à un post précis
        return $this->createQueryBuilder('c')
            ->innerJoin('App\Entity\Post', 'p', 'WITH', 'c.post = p.id')
            ->where('p.id = :postid')
            ->orderBy('c.date', 'DESC')
            ->setParameter('postid', $postid)
            ->getQuery()
            ->getResult();
    }

    public function findCommentaryById($commentaryid) { //Retrouve un commentaire par rapport à son id
        return $this->createQueryBuilder('c')
            ->where('c.id = :id')
            ->setParameter('id', $commentaryid)
            ->getQuery()
            ->getResult();
    }

    public function deleteCommentary($commentaryId) { //Supprime un commentaire à l'aide de son id
        return $this->createQueryBuilder('c')
            ->delete()
            ->where('c.id = :id')
            ->setParameter('id', $commentaryId)
            ->getQuery()
            ->getResult();
    }

    public function deletePostCommentaries($postid) { //Supprime tous les commentaires d'un post
        return $this->createQueryBuilder('c')
            ->delete()
            ->where('c.post = :id')
            ->setParameter('id', $postid)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Commentary[] Returns an array of Commentary objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Commentary
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
