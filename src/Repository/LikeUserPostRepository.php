<?php

namespace App\Repository;

use App\Entity\LikeUserPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LikeUserPost|null find($id, $lockMode = null, $lockVersion = null)
 * @method LikeUserPost|null findOneBy(array $criteria, array $orderBy = null)
 * @method LikeUserPost[]    findAll()
 * @method LikeUserPost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LikeUserPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LikeUserPost::class);
    }

    public function unlike($user, $post) { //Supprime le like d'un utilisateur précis sur un post précis
        return $this->createQueryBuilder('lup')
            ->delete()
            ->where('lup.user = :userid')
            ->andWhere('lup.post = :postid')
            ->setParameter('userid', $user)
            ->setParameter('postid', $post)
            ->getQuery()
            ->getResult();
    }

    public function getLikeUser($user, $post) { //Permets de savoir si un utilisateur a déjà like le post ou non (compteur de likes)
        return $this->createQueryBuilder('lup')
            ->where('lup.user = :userid')
            ->andWhere('lup.post = :postid')
            ->setParameter('userid', $user)
            ->setParameter('postid', $post)
            ->getQuery()
            ->getResult();
    }

    public function getLikes($post) { //Retrouve tous les likes d'un post précis

        $query = $this->createQueryBuilder('lup')
            ->select('count(lup.id) as likes')
            ->innerJoin('App\Entity\Post', 'p', 'WITH', 'p.id = lup.post')
            ->where('p.id = :postId')
            ->setParameter('postId', $post);
        return $query->getQuery()->getResult();
    }

    public function cleanPostLikes($postid) { //Supprime tous les likes d'un post
        return $this->createQueryBuilder('lup')
            ->delete()
            ->where('lup.post = :postid')
            ->setParameter('postid', $postid)
            ->getQuery()
            ->getResult();
    }
    
    // /**
    //  * @return LikeUserPost[] Returns an array of LikeUserPost objects
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
    public function findOneBySomeField($value): ?LikeUserPost
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
