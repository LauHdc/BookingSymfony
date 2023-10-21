<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
	 * Permet d'afficher une page connexion
     * @Route("/login", name="account_login")
	 * @return Response
     */
	
    public function login(AuthenticationUtils $utils): Response
    {
		$error = $utils->getLastAuthenticationError();
		
		$username = $utils->getLastUsername();
		
        return $this->render('account/login.html.twig',[
			'hasError'=>$error!==null,
			'username'=>$username
		]);
		
    }
	
	/**
	 * Permet de se déconnecter
	 * @Route("/logout",name="account_logout")
	 *
	 * @return void
	 */
	
	public function logout() {
		
		// Tout se passe via le fichier security.yaml
		
	}
	
	/**
	 * Permet d'afficher une page s'inscrire
	 * @Route("/register",name="account_register")
	 *
	 * @return Response
	 */
	
	public function register(Request $request,UserPasswordEncoderInterface $encoder,EntityManagerInterface $entityManager) {
		
		$user = new User();
		
		$form = $this->createForm(RegistrationType::class,$user);
		
		$form->handleRequest($request);
		
		if($form->isSubmitted() && $form->isValid()) {
			
			$hash = $encoder->encodePassword($user,$user->getHash());
			
			// On modifie le mot de passe avec le setter
			
			$user->setHash($hash);
			
			$entityManager->persist($user);
			$entityManager->flush();
			
			$this->addFlash("success","Votre compte a bien été créé");
			
			return $this->redirectToRoute("account_login");
			
		}
		
		return $this->render("account/register.html.twig",[
			'form'=>$form->createView()
		]);
		
	}
	
	/**
	 * Modification du profil utilisateur
	 *
	 * @Route("/account/profile",name="account_profile")
	 * @IsGranted("ROLE_USER")
	 * @return Response
	*/
	
	public function profile(Request $request,EntityManagerInterface $entityManager) {
		
		$user = $this->getUser();
		
		$form=$this->createForm(AccountType::class,$user);
		$form->handleRequest($request);
		
		if($form->isSubmitted() && $form->isValid()) {
			
			$entityManager->persist($user);
			
			$entityManager->flush();
			
			$this->addFlash("success","Votre profil a bien été modifié");
			
		}
		
		return $this->render('account/profile.html.twig',[
			'form'=>$form->createView()
		]);
		
	}
	
	/**
	 * Permet la modification du mot de passe
	 * @Route("/account/password-update",name="account_password")
	 * @IsGranted("ROLE_USER")
	 * @return Response
	*/
	
	public function updatePassword(Request $request,UserPasswordEncoderInterface $encoder,EntityManagerInterface $entityManager) {
		
		$passwordUpdate = new PasswordUpdate();
		$user=$this->getUser();
		
		$form=$this->createForm(PasswordUpdateType::class,$passwordUpdate);
		
		$form->handleRequest($request);
		
		if($form->isSubmitted() && $form->isValid()) {
			
			// Si le mot de passe actuel n'est pas le bon
			
			if(!password_verify($passwordUpdate->getOldPassword(),$user->getHash())) {
				
				// Message d'erreur
				
				//$this->addFlash("warning","Mot de passe actuel incorrect");
				
				$form->get('oldPassword')->addError(new FormError("Le mot de passe inséré n'est pas votre mot de passe actuel"));
				
			} 
			
			else {
				
				// On récupère le nouveau mot de passe
				
				$newPassword = $passwordUpdate->getNewPassword();
			
				// On crypte le nouveau mot de passe
				
				$hash = $encoder->encodePassword($user,$newPassword);

				// Modifier le nouveau mot de passe dans le setter
				
				$user->setHash($hash);

				// On enregistre
				
				$entityManager->persist($user);
				
				$entityManager->flush();

				// On ajoute un message
				
				$this->addFlash("success","Votre nouveau mot de passe a bien été enregistré");

				// On redirige	
				
				return $this->redirectToRoute('account_profile');
				
			}
			
		}
		
		return $this->render('account/password.html.twig',[
			'form'=>$form->createView()
		]);
		
	}
	
	/**
	 * Permet d'afficher la page mon compte
	 * @Route("/account",name="account_home")
	 * @IsGranted("ROLE_USER")
	 * @return Response
	*/
	
	public function myAccount() {
		
		return $this->render("user/index.html.twig",['user'=>$this->getUser()]);	
		
	}
	
	/**
	 * Affiche la liste des réservations de l'utilisateur
	 * @Route("/account/bookings", name="account_bookings")
	 * @return Response
	*/
	
	public function bookings() {
		
		return $this->render('account/bookings.html.twig');
		
	}
	
}
