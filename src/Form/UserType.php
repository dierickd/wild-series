<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class, [
                'label' => 'Email',
            ])
            ->add('avatar', TextType::class, [
                'label' => 'Avatar',
                'attr' => ['class' => 'watch-js']
            ])
            ->add('createdAt', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Membre depuis le',
                'attr' => [
                    'disabled' => 'disabled',
                    'required' => false,
                ]
            ])
            ->add('username', TextType::class, [
                'label' => 'Username',
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
