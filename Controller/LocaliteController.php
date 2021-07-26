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
use RestBundle\Document\Ville;

class LocaliteController extends Controller
{
	
	
	
	
	/**
     * @GET("/localite/all")
	 * return all the categories
     */
    public function getAllLocaliteAction(Request $request)
    {
	 
	 $dm= $this->get('doctrine_mongodb')->getManager();
	 $repository = $dm->getRepository('RestBundle:Localite');
	 $localites = $repository->findAll();
	 $formatted = [];
	 $response=[];
	 
    foreach ($localites as $localite) {
		 
		$villes= $localite->getVilles();
		$formattedVille = [];
		foreach ($villes as $ville) {
			$formattedVille [] =[
				 'name' => $ville->getName()
			];
		}
		
		
		$formatted[] = [
			'id' => $localite->getId(),
			'region'=> $localite->getRegion(),
			'villes'=> $formattedVille,
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