<?php

namespace App\Service;

use App\Entity\Commentary;
use App\Entity\DownloadUserPost;
use App\Entity\LikeUserPost;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Post;
use DateTime;
use Symfony\Component\String\Slugger\SluggerInterface;

class CommentaryHelper
{

    private $em;
    private $slugger;

    public function __construct(EntityManagerInterface $em, SluggerInterface $slugger)
    {
        $this->em = $em;
        $this->slugger = $slugger;
    }

    public function createCommentary($user, $postId, $text) { //Fonction de création d'un commentaire
        $repository = $this->em->getRepository(Post::class);
        $post = $repository->findPostById($postId)[0];
        $commentary = new Commentary();
        $now = new DateTime(date("Y/m/d H:i"));
        $commentary->setUser($user);
        $commentary->setPost($post);
        $commentary->setDescription(htmlspecialchars($text));
        $commentary->setDate($now);
        $this->em->persist($commentary);
        $this->em->flush();
    }

    public function getCommentariesByPost($postid) { //Fonction qui renvoie tous les commentaires d'un post
        $repository = $this->em->getRepository(Commentary::class);
        $commentaries = $repository->findCommentariesByPost($postid);
        foreach($commentaries as $commentary) {
            $datestring = date_format($commentary->getDate(), 'd-m-Y H:i');
            $commentary->setDateString($datestring);
        }
        return $commentaries;
    }

    public function getCommentaryById($commentaryId) { //Fonction qui renvoie un commentaire grâce à son id
        $repository = $this->em->getRepository(Commentary::class);
        $commentary = $repository->findCommentaryById($commentaryId);
        if($commentary == null) {
            return null;
        }
        return $commentary[0];
    }

    public function deleteCommentary($commentaryid) { //Fonction qui supprime un commentaire
        $repository = $this->em->getRepository(Commentary::class);
        $repository->deleteCommentary($commentaryid);
    }
}