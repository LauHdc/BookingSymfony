<?php

// namespace : chemin du controller

namespace App\Controller;

use App\Repository\AdRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


// Pour créer une page : - une fonction publique (classe) / - une route / - une réponse

class HomeController extends AbstractController {
	
	/**
	* Création de notre 1ère route
	* @Route("/",name="homepage")
	*
	*/
	
	public function home(AdRepository $adRepo, UserRepository $userRepo) {
		
		
		return $this->render('home.html.twig',
							 ['ads'=>$adRepo->findBestAds(6),
							  'users'=>$userRepo->findBestUsers()
							 ]);
		
	}
	
	/**
	 * Montre la page qui salut l'utilisateur
	 * 
	 * @Route("/hello/{nom}",name="hello")
	 * @Route("/profil",name="hello-base")
	 * @Route("/profil/{nom}/acces/{acces}", name="hello-profil")
	 * @return void
	 */
	
	public function hello($nom="anonyme",$acces="visiteur") {
		
		return $this->render('hello.html.twig',['title'=>'Page de profil','nom'=>$nom,'acces'=>$acces]);
		
	}
	
}