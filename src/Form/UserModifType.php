<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class UserModifType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) //Construction d'un formulaire de modification de profil
    {
        $builder
        ->add('img', FileType::class, [
            'mapped' => false,
            'required' => false,
            'constraints' => [
                new File([
                    'maxSize' => '5M',
                    'mimeTypes' => [
                        'image/jpeg',
                        'image/png',
                        'image/gif'
                    ],
                    'mimeTypesMessage' =>'Veuillez déposer une image au format jpeg, jpg, png ou gif.'
                ])
            ]
        ])
        ->add('statut', ChoiceType::class, [
            'required' => false,
            'choices' => [
                'Game Designer' => 'Game Designer',
                'Programmeur' => 'Programmeur',
                'Hobbyiste' => 'Hobbyiste',
                'Joueur' => 'Joueur',
                'Etudiant' => 'Étudiant',
                'Graphiste' => 'Graphiste',
                'Pas de statut' => ''
            ]
        ])
        ->add('bio', TextareaType::class, [
            'required' => false,
            'attr' => [
                'placeholder' => 'Description...'
            ]
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
