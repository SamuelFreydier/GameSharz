<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) //Construction d'un formulaire de création de compte
    {
        $builder
            ->add('username', TextType::class, [
                'required'=>true,
            ])
            ->add('email', EmailType::class, [
                'required'=>true,
            ])
            ->add('password', PasswordType::class, [
                'required'=>true,
            ])
            ->add('confirmpassword', PasswordType::class, [
                'required'=>true,
                'mapped' => false
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
