<?php

namespace App\Repository;

use App\Entity\DownloadUserPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DownloadUserPost|null find($id, $lockMode = null, $lockVersion = null)
 * @method DownloadUserPost|null findOneBy(array $criteria, array $orderBy = null)
 * @method DownloadUserPost[]    findAll()
 * @method DownloadUserPost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DownloadUserPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DownloadUserPost::class);
    }

    public function getDownloadUser($user, $post) { //Permets de trouver si un utilisateur a déjà download le lien d'un post ou non (pour le compteur)
        return $this->createQueryBuilder('dup')
            ->where('dup.user = :userid')
            ->andWhere('dup.post = :postid')
            ->setParameter('userid', $user)
            ->setParameter('postid', $post)
            ->getQuery()
            ->getResult();
    }

    public function getDownloads($post) { //Retrouve le nombre de downloads d'un post

        $query = $this->createQueryBuilder('dup')
            ->select('count(dup.id) as downloads')
            ->innerJoin('App\Entity\Post', 'p', 'WITH', 'p.id = dup.post')
            ->where('p.id = :postId')
            ->setParameter('postId', $post);
        return $query->getQuery()->getResult();
    }

    public function cleanPostDownloads($postid) { //Supprime les downloads associés à un post
        return $this->createQueryBuilder('dup')
            ->delete()
            ->where('dup.post = :postid')
            ->setParameter('postid', $postid)
            ->getQuery()
            ->getResult();
    }
    // /**
    //  * @return DownloadUserPost[] Returns an array of DownloadUserPost objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DownloadUserPost
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
