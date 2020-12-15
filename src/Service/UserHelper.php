<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserHelper
{
    private $passwordEncoder;
    private $manager;
    private $slugger;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em, SluggerInterface $slugger)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->manager = $em;
        $this->slugger = $slugger;
    }

    public function createUser($email, $username, $password, $roles) //Création d'un compte
    {
        $user = new User();
        $user->setUsername($username);
        $user->setRoles($roles);
        $user->setPassword($this->passwordEncoder->encodePassword($user, $password)); //Mot de passe crypté
        $user->setEmail($email);
        $user->setImg("img/logo/gamesharz.png"); //Image de base -> logo du site (changeable dans la modification de profil)
        $this->manager->persist($user);
        $this->manager->flush();
    }

    public function findOneUser($username) { //Retourne un utilisateur selon son nom
        $repository = $this->manager->getRepository(User::class);
        $user = $repository->findOneByUsername($username);
        if($user == null) {
            return null;
        }
        return $user[0];
    }

    public function updateUser($img, $statut, $bio, $username) { //Modification de profil
        $user = $this->findOneUser($username);
        if($user == null) {
            return null;
        }
        if($img != null) {
            $newId = uniqid();
            $originalImage = pathinfo($img->getClientOriginalName(), PATHINFO_FILENAME);
            $safeImage = $this->slugger->slug($originalImage);
            $pathImg="img/profile/";
            $pathImagename = $pathImg.$safeImage.'-'.$newId.'.'.$img->guessExtension();
            $newImagename = $safeImage.'-'.$newId.'.'.$img->guessExtension();

            $filesystem = new Filesystem();
            $filesystem->remove($user->getImg());

            $img->move('img/profile', $newImagename);

            $user->setImg($pathImagename);
        }
        if($bio != null) {
            $newBio = htmlspecialchars($bio);
            $user->setBio($newBio);
        }
        if($statut != null && $statut != "") {
            $user->setStatut($statut);
        }
        $this->manager->flush();
    }

}