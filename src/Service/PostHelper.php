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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PostHelper
{

    private $em;
    private $slugger;

    public function __construct(EntityManagerInterface $em, SluggerInterface $slugger)
    {
        $this->em = $em;
        $this->slugger = $slugger;
    }

    public function createPost($title, $description, $category, $user, $link, $img, $text) { //Fonction de création d'un post
        if ($title === null || $title === "" || $category === "") {
            return "Un des champs requis est vide.";
        }

        if(!$link) {
            return "Veuillez déposer un fichier.";
        }

        date_default_timezone_set('UTC');
        $now = new DateTime(date("Y/m/d H:i"));
        $post = new Post();
        $post->setTitle(htmlspecialchars($title));
        $post->setDescription(htmlspecialchars($description));
        $post->setUser($user);
        $post->setCategory($category);
        $post->setDate($now);
        if($link != null) {
            $post->setLien($link);
        }
        $newId = uniqid();
        if($img != null) {

            //On enregistre l'image de façon sécurisée dans le serveur
            $originalImage = pathinfo($img->getClientOriginalName(), PATHINFO_FILENAME);
            $safeImage = $this->slugger->slug($originalImage);
            $pathImg="img/posts/";
            $pathImagename = $pathImg.$safeImage.'-'.$newId.'.'.$img->guessExtension();
            $newImagename = $safeImage.'-'.$newId.'.'.$img->guessExtension();

            $img->move('img/posts', $newImagename);

            $post->setImg($pathImagename);
        }
        if($text != null) {
            $post->setText(htmlspecialchars($text));
        }

        //$newId = uniqid();

        //$originalFilename = pathinfo($link->getClientOriginalName(), PATHINFO_FILENAME);
        //$safeFilename = $this->slugger->slug($originalFilename);
        //$path = "files/games/";
        //$pathFilename = $path.$safeFilename.'-'.$newId.'.'.$link->guessExtension();
        //$newFilename = $safeFilename.'-'.$newId.'.'.$link->guessExtension();

        //$link->move('files/games', $newFilename);

        $post->setLien($link);

        $this->em->persist($post);
        $this->em->flush();


        return "";
    }

    public function updatePost($postId, $title, $resume, $category, $img, $link, $description) { //Modification d'un post
        $repository = $this->em->getRepository(Post::class);
        $post = $repository->findPostById($postId)[0];

        //Beaucoup de if pour ne pas obliger l'utilisateur à remplir tous les champs du formulaire à chaque petite modif
        if($title != null && $title != "") {
            $post->setTitle(htmlspecialchars($title));
        }
        if($resume != null && $resume != "") {
            $post->setDescription(htmlspecialchars($resume));
        }
        if($description != null && $description != "") {
            $post->setText(htmlspecialchars($description));
        }
        if($category != null && $category != "") {
            $post->setCategory($category);
        }
        if($img != null) {
            $newId = uniqid();
            
            $originalImage = pathinfo($img->getClientOriginalName(), PATHINFO_FILENAME);
            $safeImage = $this->slugger->slug($originalImage);
            $pathImg="img/posts/";
            $pathImagename = $pathImg.$safeImage.'-'.$newId.'.'.$img->guessExtension();
            $newImagename = $safeImage.'-'.$newId.'.'.$img->guessExtension();

            $filesystem = new Filesystem();
            $filesystem->remove($post->getImg());

            $img->move('img/posts', $newImagename);

            $post->setImg($pathImagename);
        }
        if($link != null) {
            //$newId = uniqid();

            //$originalFilename = pathinfo($link->getClientOriginalName(), PATHINFO_FILENAME);
            //$safeFilename = $this->slugger->slug($originalFilename);
            //$path = "files/games/";
            //$pathFilename = $path.$safeFilename.'-'.$newId.'.'.$link->guessExtension();
            //$newFilename = $safeFilename.'-'.$newId.'.'.$link->guessExtension();

            //$filesystem = new Filesystem();
            //$filesystem->remove($post->getLien());

            //$link->move('files/games', $newFilename);

            $post->setLien($link);
        }

        $this->em->flush();
    }

    public function getAllPosts() { //Fonction qui retourne tous les posts (du plus récent au plus ancien)
        $repository = $this->em->getRepository(Post::class);
        $posts = $repository->findRecentPosts();
        foreach($posts as $post) {
            $postId = $post->getId();
            $nbLikes = $this->postLikes($postId);
            $post->setLikes($nbLikes);
            $nbDownloads = $this->postDownloads($postId);
            $post->setDownloads($nbDownloads);
        }
        return $posts;
    }

    public function getPostsByUser($username) { //Tous les posts d'un utilisateur
        $repository = $this->em->getRepository(Post::class);
        $posts = $repository->findPostsByUsername($username);
        foreach($posts as $post) {
            $postId = $post->getId();
            $nbLikes = $this->postLikes($postId);
            $post->setLikes($nbLikes);
            $nbDownloads = $this->postDownloads($postId);
            $post->setDownloads($nbDownloads);
        }
        return $posts;
    }

    public function getPostsFiltered($filter, $categories) { //Posts filtrés par une chaîne de caractères ET/OU des tags
        $repository = $this->em->getRepository(Post::class);
        $posts = null;
        if($categories == null) {
            $posts = $repository->findFilteredPosts($filter);
        }
        else {
            $posts = $repository->findFilteredCategoriesPosts($filter, $categories);
        }
        foreach($posts as $post) {
            $postId = $post->getId();
            $nbLikes = $this->postLikes($postId);
            $post->setLikes($nbLikes);
            $nbDownloads = $this->postDownloads($postId);
            $post->setDownloads($nbDownloads);
        }

        return $posts;
    }

    public function getPostsFilteredArray($filter, $categories) { //Posts filtrés par une chaîne de caractères ET/OU des tags dans un array pour le Json
        $repository = $this->em->getRepository(Post::class);
        $posts = null;
        if($categories == null) {
            $posts = $repository->findFilteredPostsArray($filter);
        }
        else {
            $posts = $repository->findFilteredCategoriesPostsArray($filter, $categories);
        }
        foreach($posts as $id => $post) {
            $postId = $post["id"];
            $nbLikes = $this->postLikes($postId);
            $post["likes"] = $nbLikes;
            $nbDownloads = $this->postDownloads($postId);
            $post["downloads"] = $nbDownloads;
            $posts[$id] = $post;
        }

        return $posts;
    }

    public function likePost($userId, $postId, $user) { //Un utilisateur like ou unlike un post
        $repository = $this->em->getRepository(LikeUserPost::class);
        if($repository->getLikeUser($userId, $postId) == null) {
            $postrepository = $this->em->getRepository(Post::class);
            $post = $postrepository->findPostById($postId)[0];
            $like = new LikeUserPost();
            $like->setUser($user);
            $like->setPost($post);
            $this->em->persist($like);
            $this->em->flush();
        }
        else {
            $repository->unlike($userId, $postId);
        }
    }

    public function downloadPost($userId, $postId, $user) { //Un utilisateur télécharge le lien d'un post (le compteur se met à jour)
        $repository = $this->em->getRepository(DownloadUserPost::class);
        if($repository->getDownloadUser($userId, $postId) == null) {
            $postrepository = $this->em->getRepository(Post::class);
            $post = $postrepository->findPostById($postId)[0];
            $dl = new DownloadUserPost();
            $dl->setUser($user);
            $dl->setPost($post);
            $this->em->persist($dl);
            $this->em->flush();
        }
    }

    public function postLikes($postId) { //Nombre de likes d'un post
        $repository = $this->em->getRepository(LikeUserPost::class);

        return $repository->getLikes($postId)[0]['likes'];
    }

    public function postDownloads($postId) { //Nombre de downloads d'un post
        $repository = $this->em->getRepository(DownloadUserPost::class);
        return $repository->getDownloads($postId)[0]['downloads'];
    }
    
    public function getPostById($postId) { //Retourne un post précis selon son id
        $repository = $this->em->getRepository(Post::class);

        $post = $repository->findPostById($postId);
        if($post == null) {
            return null;
        }
        $post = $post[0];

        $postId = $post->getId();

        $datestring = date_format($post->getDate(), 'd-m-Y H:i');
        $post->setDateString($datestring);

        $nbLikes = $this->postLikes($postId);
        $post->setLikes($nbLikes);

        $nbDownloads = $this->postDownloads($postId);
        $post->setDownloads($nbDownloads);

        return $post;
    }

    public function deletePost($postId) { //Supprime un post de façon propre pour clean la database correctement
        $repository = $this->em->getRepository(Post::class);
        $commentaryRepository = $this->em->getRepository(Commentary::class);
        $likesRepository = $this->em->getRepository(LikeUserPost::class);
        $downloadsRepository = $this->em->getRepository(DownloadUserPost::class);
        $commentaryRepository->deletePostCommentaries($postId); //On supprime d'abord les commentaires associés
        $likesRepository->cleanPostLikes($postId); //Puis les likes associés
        $downloadsRepository->cleanPostDownloads($postId); //Puis les downloads associés
        $repository->deletePost($postId); //Enfin on supprime le post
    }

    public function getCategories() { //Retourne toutes les catégories utilisées au moins une fois
        $repository = $this->em->getRepository(Post::class);
        $categories = $repository->getCategories();
        $listCategories = [];
        foreach($categories as $category) {
            array_push($listCategories, $category['category']);
        }
        return $listCategories;
    }
}