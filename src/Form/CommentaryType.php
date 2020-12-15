<?php

namespace App\Form;

use App\Entity\Commentary;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentaryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) //Construction d'un formulaire de création de commentaire
    {
        $builder
            ->add('description', TextareaType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Écrivez votre commentaire...'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Commentary::class,
        ]);
    }
}
