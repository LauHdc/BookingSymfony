<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminUsersController extends AbstractController
{
    /**
     * @Route("/admin/users", name="admin_users_list")
     */
	
    public function index(UserRepository $repo): Response
    {
        return $this->render('admin/users/index.html.twig', [
            'user' => $repo->findAll(),
        ]);
    }
	
	/**
	 * Suppression d'un utilisateur
	 * @Route("/admin/user/{id}/delete", name="admin_user_delete")
	 * @param User $user
	 * @param EntityManagerInterface $entityManager
	 * @return Response
	*/
	
	public function delete(User $user,EntityManagerInterface $entityManager) {
		
		$entityManager->remove($user);
		$entityManager->flush();
		
		$this->addFlash('success',"L'utilisateur {$user->getId()} a bien été supprimé");
		
		return $this->redirectToRoute('admin_users_list');
		
	}
}
