<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) //Construction d'un formulaire de création de post
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => [
                    'placeholder' => 'Titre...'
                ]
            ])
            ->add('description', TextType::class, [
                'attr' => [
                    'placeholder' => "Résumé..."
                ]
            ])
            ->add('category', ChoiceType::class, [
                'choices' => [
                    'Action' => 'Action',
                    'Aventure' => 'Aventure',
                    'Plateforme' => 'Plateforme',
                    'Stratégie' => 'Stratégie',
                    'Horreur' => 'Horreur',
                    'Divers/Autre' => 'Divers'
                ]
            ])
            //->add('lien', FileType::class, [
            //    'mapped' => false,
            //    'required' => true,
            //    'constraints' => [
            //        new File([
            //            'maxSize' => '100M',
            //            'mimeTypes' => [
            //                'application/zip'
            //            ],
            //            'mimeTypesMessage' => 'Veuillez déposer un fichier zip.'
            //        ])
            //    ]
            //])
            ->add('lien', UrlType::class, [
                'mapped' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => "Insérer un lien..."
                ]
            ])
            ->add('img', FileType::class, [
                'mapped' => false,
                'required'=>false,
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
            ->add('text', TextareaType::class, [
                'required'=>false,
                'attr' => [
                    'placeholder' => 'Description...'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
