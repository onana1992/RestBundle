<?php

namespace RestBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use RestBundle\Document\Relais;
use RestBundle\Document\RelaisEmbedded;

class RelaisController extends Controller
{
	
	/**
     * @GET("/relais/all")
	 * return all the relais
     */
    public function getAllRelaisAction(Request $request)
    {
	 
	 $dm= $this->get('doctrine_mongodb')->getManager();
	 $repository = $dm->getRepository('RestBundle:Relais');
	 $relais = $repository->findAll();
	 $formatted = [];
	 $response=[];
	 
    foreach ($relais as $relai) {	 
		$rlais= $relai->getRelais();
		$formattedRelais = [];
		foreach ($rlais as $rlai) {
			$formattedRelais [] =[
				 'nom' => $rlai->getNom(),
				 'quartier' => $rlai->getQuartier(),
				 'emplacement'=> $rlai->getEmplacement(),
				 'prix_small'=> $rlai->getPrix_small(),
				 'prix_medium' => $rlai->getPrix_medium(),
				 'prix_big' => $rlai->getPrix_big()
			];
		}
		
		
		$formatted[] = [
			'id' => $relai->getId(),
			'ville'=> $relai->getVille(),
			'delai'=> $relai->getDelai(),
			'relais'=> $formattedRelais
		];
	}
		
		$response=[
			'statut' => '200',
			'data'=> $formatted
		];
		
	  header('Access-Control-Allow-Origin: *');
	  return new JsonResponse($response);
	}
		
}