<?php

namespace App\Service;

use Twig\Environment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class Pagination {
	
	private $entityClass;
	private $limit=10;
	private $currentPage=1;
	private $entityManager;
	private $twig;
	private $route;
	private $templatePath;
	
	public function __construct(EntityManagerInterface $entityManager,Environment $twig,RequestStack $request,$templatePath) {
		
		$this->route = $request->getCurrentRequest()->attributes->get('_route');
		
		$this->entityManager = $entityManager;
		
		$this->twig = $twig;
		
		$this->templatePath = $templatePath;
		
	}
	
	public function display() {
		
		// Appelle le moteur twig et on précise quel template on veut utiliser
		
		$this->twig->display($this->templatePath,[
			// Options nécessaires à l'affichage des données
			
			// Variables : route / page / pages
			
			'page'=>$this->currentPage,
			'pages'=>$this->getPages(),
			'route'=>$this->route
		]);
		
	}
	
	// 1- Utiliser la pagination à partir de n'importe quelle entité / Préciser l'entité concernée
	
	public function setEntityClass($entityClass) {
		
		// Donnée entityClass = donnée qui va être envoyée
		
		$this->entityClass = $entityClass;
		
		return $this;
		
	}
	
	public function getEntityClass() {
		
		return $this->entityClass;
		
	}
	
	// 2- Quelle est la limite ?
	
	public function getLimit() {
		
		return $this->limit;
		
	}
	
	public function setLimit($limit) {
		
		$this->limit = $limit;
		
		return $this;
		
	}
	
	// 3- Sur quelle page on se trouve actuellement
	
	public function getPage() {
		
		return $this->currentPage;
		
	}
	
	public function setPage($page) {
		
		$this->currentPage = $page;
		
		return $this;
		
	}
	
	// 4- On va chercher le nombre de pages au total
	
	public function getData() {
		
		if(empty($this->entityClass)) {
			
			throw new \Exception("setEntityClass n'a pas été renseigné dans le controller correspondant");
			
		}
		
		// Calculer l'offset
		
		$offset = $this->currentPage * $this->limit - $this->limit;
		
		// Demander au repository de trouver les éléments
		
		// On va chercher le bon repository
		
		$repo = $this->entityManager->getRepository($this->entityClass);
		
		// On construit notre requête
		
		$data = $repo->findBy([],[],$this->limit,$offset);
		
		return $data;
		
	}
	
	public function getPages() {
		
		$repo = $this->entityManager->getRepository($this->entityClass);
		
		$total = count($repo->findAll());
			
		$pages = ceil($total/$this->limit);
		
		return $pages;
		
	}
	
	public function getRoute() {
		
		return $this->route;
		
	}
	
	public function setRoute($route) {
		
		$this->route = $route;
		
		return $this;
		
	}
	
	public function getTemplatePath() {
		
		return $this->templatePath;
		
	}
	
	public function setTemplatePath($templatePath) {
		
		$this->templatePath = $templatePath;
		
		return $this;
		
	}
	
}