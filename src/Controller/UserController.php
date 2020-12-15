<?php

namespace App\Controller;

use App\Form\PostType;
use App\Form\UserModifType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\PostHelper;
use App\Service\UserHelper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class UserController extends AbstractController
{

    /**
     * @Route("/user/{username}", name="profil")
     */
    public function displayProfile(Request $request, $username, UserHelper $uh, PostHelper $ph) {  //Page de profil d'un utilisateur
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $user = $uh->findOneUser($username);
        if($user == null) {
            throw $this->createNotFoundException();
        }
        $posts = $ph->getPostsByUser($username);
        $currentUser = $this->getUser();
        return $this->render('profilPage.html.twig', ['user' => $user, 'posts' => $posts, 'currentUser' => $currentUser]);
    }

    /**
     * @Route("/user/{username}/edit", name="editprofil")
     */
    public function editProfile(Request $request, $username, UserHelper $uh) { //Page de modification de profil d'un utilisateur
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        if($this->getUser()->getUsername() != $username) {
            return $this->redirectToRoute('profil', ['username' => $username]);
        }

        $currentUser = $this->getUser();

        $form = $this->createForm(UserModifType::class); //Formulaire de modification de profil

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $img = $form->get('img')->getData();
            $data = $form->getData();
            $statut = $data->getStatut();
            $biographie = $data->getBio();
            $uh->updateUser($img, $statut, $biographie, $currentUser->getUsername());
            return $this->redirectToRoute('profil', ['username' => $currentUser->getUsername()]);
        }
        return $this->render('profilePageModif.html.twig', ['form' => $form->createView(), 'user' => $currentUser]);
    }

}