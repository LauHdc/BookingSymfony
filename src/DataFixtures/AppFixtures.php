<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Booking;
use App\Entity\Comment;
use Bluemmb\Faker\PicsumPhotosProvider;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;
	
	public function __construct(UserPasswordEncoderInterface $encoder) {
		
		$this->encoder=$encoder;		
		
	}
	
	public function load(ObjectManager $manager): void
    {
        
		$faker = Factory::create('FR-fr');
		$faker->addProvider(new PicsumPhotosProvider($faker));
		
		// Gestion des rôles
		
		$adminRole = new Role();
		
		$adminRole->setTitle("ROLE_ADMIN");
		$manager->persist($adminRole);
		
		// Création d'un utilisateur spécial avec un rôle admin
		
		$adminUser = new User();
		
		$adminUser->setFirstName('Haddouche')
				  ->setLastName('Laura')
				  ->setEmail('laurahdc@gmail.com')
				  ->setPassword($this->encoder->encodePassword($adminUser,'password'))
			 	  ->setAvatar('https://randomuser.me/api/portraits/women/55.jpg')
				  ->setIntroduction($faker->sentence())
			      ->setDescription("<p>".join("</p><p>",$faker->paragraphs(5)). "</p>")
				  ->addUserRole($adminRole);
								   
		$manager->persist($adminUser);
		
		$users=[];
		$genres=['male','female'];
		
		// Utilisateurs
		
		for($i=1;$i<=10;$i++) {
			
			$user = new User();
			$genre = $faker->randomElement($genres);
			$avatar = 'https://randomuser.me/api/portraits/';
			$avatarId = $faker->numberBetween(1,99).'.jpg';
			$avatar .= ($genres == 'male' ? 'men/' : 'women/') . $avatarId;
			$password = $this->encoder->encodePassword($user,'password');
			
			$description="<p>".join("<p></p>",$faker->paragraphs(5)). "</p>";
			$user->setDescription($description)
				 ->setFirstname($faker->firstname)
				 ->setLastname($faker->lastname)
				 ->setEmail($faker->email)
				 ->setIntroduction($faker->sentence())
				 ->setPassword($password)
				 ->setAvatar($avatar);
			
			$manager->persist($user);
			$users[]=$user;
			
		}
		
		// Annonces
		
		for($i=1; $i<=30;$i++) {
			
			$ad = new Ad();
			
			$title = $faker->sentence();
			$coverImage = $faker->imageUrl(1000,350);
			$introduction = $faker->paragraph(2);
			$content = "<p>".join("<p></p>",$faker->paragraphs(5)). "</p>";
			$user = $users[mt_rand(0,count($users)-1)];
		
			$ad->setTitle($title)
		   	   ->setCoverImage("$coverImage")
		   	   ->setIntroduction("$introduction")
		   	   ->setContent("$content")
		   	   ->setPrice(mt_rand(30,200))
		   	   ->setRooms(mt_rand(1,5))
			   ->setAuthor($user);
		
			$manager ->persist($ad);
			
			for($j=1;$j<=mt_rand(2,5);$j++) {
				
				// On crée une nouvelle instance de l'entité Image
				
				$image = new Image();
				$image->setUrl($faker->imageUrl())
					  ->setCaption($faker->sentence())
					  ->setAd($ad);
				
				// On sauvegarde
				
				$manager->persist($image);
				
			}
			
			// Gestion des réservations
			
			for($k=1;$k <= mt_rand(0,5);$k++) {
				
				$booking = new Booking();
				$createdAt = $faker->dateTimeBetween('-6 months');
				$startDate = $faker->dateTimeBetween('-3 months');
				$duration = mt_rand(3,10);
				$endDate = (clone $startDate)->modify("+ $duration days");
				$amount = $ad->getPrice() * $duration;
				
				// Trouver le booker
				
				$booker = $users[mt_rand(0,count($users)-1)];
				$comment = $faker->paragraph();
				
				// Configuration de la réservation
				
				$booking->setBooker($booker)
						->setAd($ad)
						->setStartDate($startDate)
						->setEndDate($endDate)
						->setCreatedAt($createdAt)
						->setAmount($amount)
						->setComment($comment)
					;
				
						$manager->persist($booking);
				
				// Gestion des commentaires
				
				if(mt_rand(0,1)) {
				
				$comment = new Comment();
				
				$comment->setContent($faker->paragraph())
						->setRating(mt_rand(1,5))
						->setAuthor($booker)
						->setAd($ad)
					;
					
					$manager->persist($comment);
				
				}
				
			}
			
		}
		
        $manager->flush();
    }
	
}