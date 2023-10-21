<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Image;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController {
	
    /**
	 * Permet d'afficher une liste d'annonces
     * @Route("/ads", name="ads_list")
     */
	
    public function index(AdRepository $repo) {
		
		$repo = $this->getDoctrine()->getRepository(Ad::class);
		
		// Via $repo, on va aller chercher toutes les annonces via la méthode findAll
		
		$ads = $repo->findAll();
		
        return $this->render('ad/index.html.twig', [
            'controller_name' => 'Nos annonces',
			'ads'=>$ads
        ]);
    }
	
	/**
	 * Permet de créer une annonce
	 * @Route("/ads/new",name="ads_create")
	 * @IsGranted("ROLE_USER")
	 * @return Response
	*/
	
	public function create(Request $request,EntityManagerInterface $entityManager) {
		
		// Fabriquant de formulaires : FORMBUILDER
		
		$ad = new Ad();
		
		// On lance la fabrication et la configuration de notre formulaire
		
		$form = $this->createForm(AnnonceType::class,$ad);
		
		// Récupération des données du formulaire
		
		$form -> handleRequest($request); 
		
		if($form->isSubmitted() && $form->isValid()) {
			
			// Si le formulaire est soumis ET si le formulaire est valide, on demande à Doctrine de sauvegarder ces données dans l'objet $manager
			
			// Pour chaque image supplémentaire ajoutée
			
			foreach($ad->getImages() as $image) {
				
				// On relie l'image à l'annonce et on modifie l'annonce
				
				$image->setAd($ad);
				
				// On sauvegarde les images
				
				$manager->persist($image);
				
			}
			
			$ad->setAuthor($this->getUser());
			$entityManager->persist($ad);
			$entityManager->flush();
			
			$this->addFlash('success',"Annonce <strong>{$ad->getTitle()}</strong> créee avec succès");
			
			return $this->redirectToRoute('ads_single',['slug'=>$ad->getSlug()]);
			
		}
		
		return $this->render('ad/new.html.twig',['form'=>$form->createView()]);
		
	}
	
	/**
	 * Permet d'afficher une seule annonce
	 * @Route("/ads/{slug}", name="ads_single")
	 *
	 * @return Response
	 */
	
	public function show($slug,Ad $ad) {
		
		// On récupère l'annonce qui correspond au slug
		// X = 1 champ de la table, à préciser à la place de X
		// findByX = renvoi un tableau d'annonces (plusieurs éléments)
		// findOneByX = renvoi un élément
		
		//$ad = $repo->findOneBySlug($slug);
		
		return $this->render('ad/show.html.twig',['ad'=>$ad]);
		
	}
	
	/**
	 * Permet d'éditer et de modifier un article
	 * @Route("/ads/{slug}/edit", name="ads_edit")
	 * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()",message="Cette annonce ne vous apprtient pas, vous ne pouvez pas la modifier")
	 * @return Response
	*/
	
	public function edit(Ad $ad,Request $request,EntityManagerInterface $entityManager) {
		
		$form = $this->createForm(AnnonceType::class,$ad);
		$form->handleRequest($request);
		
		if($form->isSubmitted() && $form->isValid()) {
			
			foreach($ad->getImages() as $image) {
				
				$image -> setAd($ad);
				
				$entityManager->persist($image);
				
			}
			
			$entityManager->persist($ad);
			$entityManager->flush();
			
			$this->addFlash("success","Les modifications ont été prises en compte");
			
			return $this->redirectToRoute('ads_single',['slug'=>$ad->getSlug()]);
			
		}
		
		return $this->render('ad/edit.html.twig',['form'=>$form->createView(),'ad'=>$ad]);
		
	}
	
	/**
	 * Suppression d'une annonce
	 * @Route("/ads/{slug}/delete",name="ads_delete")
	 * @Security("is_granted('ROLE_USER') and user == ad.getAuthor()",message="Vous n'avez pas le droit d'accéder à cette page")
	 * @param Ad $ad
	 * @param ObjectManager $manager
	 * @return Response
	*/
	
	public function delete(Ad $ad,EntityManagerInterface $entityManager) {
		
		$entityManager->remove($ad);
		$entityManager->flush();
		$this->addFlash("success","L'annonce <em>{$ad->getTitle()}</em> a bien été supprimée");
		
		return $this->redirectToRoute("ads_list");
		
	}
	
}
