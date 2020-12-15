<?php

namespace App\Controller;

use App\Form\CommentaryType;
use App\Form\PostModifType;
use App\Form\PostType;
use App\Service\CommentaryHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\PostHelper;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class PostsController extends AbstractController
{

    /**
     * @Route("/", name="index")
     */
    public function indexSearch(Request $request) { //Fonction de la première page de recherche (celle avec juste une barre de recherche)
        if (!$this->getUser()) { //On vérifie si l'utilisateur est connecté et on le renvoie au login si besoin
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createFormBuilder() //Petit formulaire pour la barre de recherche
            ->setAction($this->generateUrl('searchresult'))
            ->setMethod('GET')
            ->add('filter', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher...'
                ]
            ])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $filter = htmlspecialchars($data['filter']);
            return $this->redirectToRoute('searchresult', ['filter' => $filter]);
        }
        return $this->render("searchFirstPage.html.twig", ['form' => $form->createView()]);
    }

    /**
     * @Route("/posts", name="searchresult")
     */
    public function searchResult(PostHelper $phelper, Request $request) { //Page principale qui affiche tous les posts et dispose d'une barre de recherche normale (associée à des tags) et d'une barre de recherche en utilisant une API (associée aussi à des tags)

        if (!$this->getUser()) { //Redirection au login si l'utilisateur n'est pas co
            return $this->redirectToRoute('app_login');
        }

        $filter = null;
        $posts = null;
        $categories = null;
        $lstCategories = $phelper->getCategories();
        $newRequest = $request->query->all();
        if(isset($newRequest['filter']))
        {
            $filter = htmlspecialchars($newRequest['filter']);
        }
        if(isset($newRequest['category'])) {
            $categories = $newRequest['category'];
        }
        $currentUser = $this->getUser();

        //$form = $this->createFormBuilder()
        //    ->setAction($this->generateUrl('searchresultfilter'))
        //    ->setMethod('GET')
        //    ->add('filter', TextType::class, [
        //        'required' => false,
        //        'attr' => [
        //            'placeholder' => 'API...',
        //            'id' => 'searchbar'
        //        ]
        //    ])
        //    ->getForm();
        //$form->handleRequest($request);
        //if ($form->isSubmitted() && $form->isValid()) {
        //    $data = $form->getData();
        //    $filter = htmlspecialchars($data['filter']);
        //    return $this->redirectToRoute('searchresultfilter', ['filter' => $filter]);
        //}

        if($filter == null) {
            if($categories == null) {
                $posts = $phelper->getAllPosts();
                return $this->render("searchResult.html.twig", ['posts' => $posts, 'user' => $currentUser, 'categories' => $lstCategories]);
            }
            else {
                $posts = $phelper->getPostsFiltered("", $categories);
                $tagsString = implode(", ", $categories);
                return $this->render("searchResult.html.twig", ['posts' => $posts, 'user' => $currentUser, 'categories' => $lstCategories, 'tagsString' => $tagsString]);
            }
        }
        else {
            if($categories == null) {
                $posts = $phelper->getPostsFiltered($filter, $categories);
                return $this->render("searchResult.html.twig", ['posts' => $posts, 'user' => $currentUser, 'categories' => $lstCategories, 'filter' => $filter]);
            }
            else {
                $posts = $phelper->getPostsFiltered($filter, $categories);
                $tagsString = implode(", ", $categories);
                return $this->render("searchResult.html.twig", ['posts' => $posts, 'user' => $currentUser, 'categories' => $lstCategories, 'filter' => $filter, 'tagsString' => $tagsString]);
            }
        }
        
    }


    /**
     * @Route("/posts/api/", name="searchresultfilter")
     */
    public function searchResultFilter(Request $request, PostHelper $phelper) { //Affichage d'un tableau Json à la suite d'une recherche via l'API
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $newRequest = $request->query->all();
        $recherche = null;
        $categories = null;
        $posts = null;
        if(isset($newRequest['filter'])) {
            $recherche = htmlspecialchars($newRequest['filter']);
        }
        if(isset($newRequest['category'])) {
            $categories = $newRequest['category'];
        }
        if($recherche == null) {
            $posts = $phelper->getPostsFilteredArray("", $categories);
        }
        else {
            $posts = $phelper->getPostsFilteredArray($recherche, $categories);
        }
        return new JsonResponse(['posts' => $posts]);
    }

    /**
     * @Route("/posts/create", name="createpost")
     */
    public function createPost(Request $request, PostHelper $phelper) { //Page de création de post

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(PostType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $download = $form->get('lien')->getData();
            $img = $form->get('img')->getData();
            $data = $form->getData();

            $title = $data->getTitle();
            $description = $data->getDescription();
            $category = $data->getCategory();
            $user = $this->getUser();
            $text = $data->getText();

            $phelper->createPost($title, $description, $category, $user, $download, $img, $text);

            return $this->redirectToRoute('searchresult', []); //On redirige vers la page principale après la création

        }

        return $this->render("postCreation.html.twig", ['form' => $form->createView()]);
    }

    /**
     * @Route("/posts/{postid}", name="displaypost")
     */
    public function displayPost(Request $request, $postid, PostHelper $ph, CommentaryHelper $ch) { //Page d'un seul post
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $currentUser = $this->getUser();

        $form = $this->createForm(CommentaryType::class); //Formulaire pour la création de commentaire
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $description = $data->getDescription();
            $ch->createCommentary($currentUser, $postid, $description);
            return $this->redirectToRoute('displaypost', ['postid' => $postid]);
        }

        $form2 = $this->createFormBuilder() //Formulaire pour le download (on augmente le compteur d'abord puis on renvoie vers le lien)
            ->add('lien', HiddenType::class)
            ->getForm();
        $form2->handleRequest($request);
        if($form2->isSubmitted() && $form2->isValid()) {
            $lien = $form2->getData()['lien'];
            $ph->downloadPost($currentUser->getId(), $postid, $currentUser);
            return $this->redirect($lien);
        }

        $post = $ph->getPostById($postid);
        if($post == null){
            throw $this->createNotFoundException();
        }
        $commentaries = $ch->getCommentariesByPost($postid);
        return $this->render("post.html.twig", ['form' => $form->createView(), 'form2' => $form2->createView(), 'post' => $post, 'commentaries' => $commentaries, 'currentuser' => $currentUser]);
    }

    /**
     * @Route("/posts/{postid}/like", name="postlike")
     */
    public function likePost(Request $request, $postid, PostHelper $ph) { //Like ou Unlike un post
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $currentUser = $this->getUser();
        $ph->likePost($currentUser->getId(), $postid, $currentUser);

        return $this->redirectToRoute('displaypost', ['postid' => $postid]);
    }

    /**
     * @Route("/posts/{postid}/commentaries/{commentaryid}/delete", name="deletecommentary")
     */
    public function deleteCommentary(Request $request, $postid, $commentaryid, CommentaryHelper $ch, PostHelper $ph) { //Suppression d'un commentaire sur un post
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $commentary = $ch->getCommentaryById($commentaryid);
        if($commentary == null) {
            throw $this->createNotFoundException();
        }
        if($commentary->getUser() != $this->getUser()) {
            return $this->redirectToRoute('displaypost', ['postid' => $postid]);
        }
        $ch->deleteCommentary($commentaryid);
        return $this->redirectToRoute('displaypost', ['postid' => $postid]);
    }
    
    /**
     * @Route("/posts/{postid}/delete", name="deletepost")
     */
    public function deletePost(Request $request, $postid, PostHelper $ph) { //Suppression d'un post
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $post = $ph->getPostById($postid);
        if($post == null){
            throw $this->createNotFoundException();
        }
        if($post->getUser() != $this->getUser()) {
            return $this->redirectToRoute('displaypost', ['postid' => $postid]);
        }
        $ph->deletePost($postid);
        return $this->redirectToRoute('searchresult', []);
    }

    /**
     * @Route("/posts/{postid}/edit", name="updatepost")
     */
    public function updatePost(Request $request, $postid, PostHelper $ph) { //Page de modification d'un post
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $post = $ph->getPostById($postid);
        if($post == null){
            throw $this->createNotFoundException();
        }
        if($post->getUser() != $this->getUser()) {
            return $this->redirectToRoute('displaypost', ['postid' => $postid]);
        }
        $form = $this->createForm(PostModifType::class); //Formulaire de modification
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $download = $form->get('lien')->getData();
            $img = $form->get('img')->getData();
            $data = $form->getData();
            $description = $data->getDescription();
            $title = $data->getTitle();
            $text = $data->getText();
            $category = $data->getCategory();
            $ph->updatePost($postid, $title, $description, $category, $img, $download, $text);
            return $this->redirectToRoute('displaypost', ['postid' => $postid]);
        }
        return $this->render('postModif.html.twig', ['form' => $form->createView(), 'post' => $post]);
    }

}