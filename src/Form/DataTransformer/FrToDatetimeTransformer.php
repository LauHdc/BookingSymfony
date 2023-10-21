<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class FrToDatetimeTransformer implements DataTransformerInterface {
	
	// Transforme les données originelles pour qu'elles puissent s'afficher dans un formulaire

	public function transform($date) {
		
		if($date === null) {
			
			return '';
			
		}
		
		// On retourne une date en FR
		
		return $date->format('d/m/Y');
		
	}
	
	// A l'inverse, prend la donnée qui arrive dans le formulaire et la remet dans le format qu'on attend
	
	public function reverseTransform($datefr) {
		
		// Date en FR
		
		if($datefr === null) {
			
			// Exception
			
			throw new TransformationFailedException("Fournir une date");
			
		}
		
		$date = \DateTime::createFromFormat('d/m/Y',$datefr);
		
		if($date === false) {
			
			// Exception
			
			throw new TransformationFailedException("Le format de la date n'est pas correct");
			
		}
		
		return $date;
		
	}

}