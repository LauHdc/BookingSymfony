<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RegistrationType extends ApplicationType
{
	
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname',TextType::class,$this->getConfiguration("Prénom","Votre prénom..."))
            ->add('lastname',TextType::class,$this->getConfiguration("Nom","Votre nom..."))
            ->add('email',EmailType::class,$this->getConfiguration("Email","Un email valide"))
            ->add('avatar',UrlType::class,$this->getConfiguration("Avatar","Url de votre avatar"))
            ->add('hash',PasswordType::class,$this->getConfiguration("Mot de passe","Choisissez un mot de passe solide"))
			->add('passwordConfirm',PasswordType::class,$this->getConfiguration("Confirmation du mot de passe","Confirmez votre mot de passe"))
            ->add('introduction',TextType::class,$this->getConfiguration("Introduction","Description courte pour vous présenter"))
            ->add('description',TextareaType::class,$this->getConfiguration("Description","Description détaillée"))
            ->add('description',TextareaType::class,$this->getConfiguration("Description","Description détaillée"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
