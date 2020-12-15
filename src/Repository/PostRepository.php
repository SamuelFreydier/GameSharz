<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function findRecentPosts() { //Retrouve tous les posts du plus récent au plus ancien
        return $this->createQueryBuilder('po')
            ->orderBy('po.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findPostById($id) { //Retrouve un post par rapport à son id
        return $this->createQueryBuilder('po')
            ->where('po.id = :id')
            ->orderBy('po.date', 'DESC')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }

    public function findPostsByUsername($username) { //Retrouve tous les posts d'un utilisateur du plus récent au plus ancien
        return $this->createQueryBuilder('po')
            ->innerJoin('App\Entity\User', 'u', 'WITH', 'po.user = u.id')
            ->where('u.username = :username')
            ->orderBy('po.date', 'DESC')
            ->setParameter('username', $username)
            ->getQuery()
            ->getResult();
    }

    public function findFilteredPosts($filter) { //Retrouve tous les posts, du plus récent au plus ancien, filtrés par une chaîne de caractères
        return $this->createQueryBuilder('po')
            ->orderBy('po.date', 'DESC')
            ->where('po.title LIKE :filter')
            ->orWhere('po.description LIKE :filter')
            ->setParameter('filter', '%'.$filter.'%')
            ->getQuery()
            ->getResult();
    }

    public function findFilteredCategoriesPosts($filter, $categories) { //Retrouve tous les posts, du plus récent au plus ancien, filtrés par une chaîne de caractères ET un ou plusieurs tag(s)
        return $this->createQueryBuilder('po')
            ->orderBy('po.date', 'DESC')
            ->where('po.title LIKE :filter')
            ->orWhere('po.description LIKE :filter')
            ->andWhere('po.category IN (:categories)')
            ->setParameter('filter', '%'.$filter.'%')
            ->setParameter('categories', array_values($categories))
            ->getQuery()
            ->getResult();
    }

    public function findFilteredPostsArray($filter) { //Retrouve tous les posts, du plus récent au plus ancien, filtrés par une chaîne de caractères renvoyés en tableau (pour Json)
        return $this->createQueryBuilder('po')
            ->orderBy('po.date', 'DESC')
            ->where('po.title LIKE :filter')
            ->orWhere('po.description LIKE :filter')
            ->setParameter('filter', '%'.$filter.'%')
            ->getQuery()
            ->getArrayResult();
    }

    public function findFilteredCategoriesPostsArray($filter, $categories) { //Retrouve tous les posts, du plus récent au plus ancien, filtrés par une chaîne de caractères ET un ou plusieurs tag(s) renvoyés en tableau (pour Json)
        return $this->createQueryBuilder('po')
            ->orderBy('po.date', 'DESC')
            ->where('po.title LIKE :filter')
            ->orWhere('po.description LIKE :filter')
            ->andWhere('po.category IN (:categories)')
            ->setParameter('filter', '%'.$filter.'%')
            ->setParameter('categories', array_values($categories))
            ->getQuery()
            ->getArrayResult();
    }

    public function findLastPost() { //Retrouve le dernier post créé
        return $this->createQueryBuilder('po')
            ->orderBy('po.id', 'DESC')
            ->getQuery()
            ->getFirstResult();
    }

    public function deletePost($postid) { //Supprime un post précis
        return $this->createQueryBuilder('po')
            ->delete()
            ->where('po.id = :id')
            ->setParameter('id', $postid)
            ->getQuery()
            ->getResult();
    }

    public function getCategories() { //Retrouve toutes les catégories existantes actuellement (et utilisées au moins une fois)
        return $this->createQueryBuilder('po')
            ->select('po.category')
            ->groupBy('po.category')
            ->getQuery()
            ->getResult();
    }

    

    // /**
    //  * @return Post[] Returns an array of Post objects
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
    public function findOneBySomeField($value): ?Post
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
