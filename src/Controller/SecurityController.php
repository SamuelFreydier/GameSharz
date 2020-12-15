<?php

namespace App\Controller;

use App\Form\UserType;
use App\Service\UserHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response //Connexion d'un utilisateur
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('index');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout() //Déconnexion
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/signup", name="signup")
     */
    public function signup(Request $request, UserHelper $helper) { //Inscription d'un utilisateur

        if ($this->getUser()) {
            return $this->redirectToRoute('index');
        }

        $form = $this->createForm(UserType::class); //Formulaire d'inscription

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $confirmpassword = $form->get('confirmpassword')->getData();
            $data = $form->getData();
            $email = $data->getEmail();
            $username = $data->getUsername();
            if($helper->findOneUser($username) != null) {
                return $this->redirectToRoute('index');
            }
            $password = $data->getPassword();
            if($confirmpassword != $password) {
                return $this->render('security/signup.html.twig', ['form' => $form->createView()]);
            }
            $helper->createUser($email, $username, $password, ['ROLE_USER']);
            return $this->redirectToRoute('index');
        }

        return $this->render('security/signup.html.twig', ['form' => $form->createView()]);
    }

}
