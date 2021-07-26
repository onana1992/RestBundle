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
use RestBundle\Document\Marque;
use RestBundle\Document\Image;


class MarqueController extends Controller
{

	 /**
     * @POST("/marque")
	 * body-parm: name(String), idLogo(String)
	 * created a new marque
     */
    public function postMarqueAction(Request $request)
    {
		
	  $dm= $this->get('doctrine_mongodb')->getManager();
	  $repository = $dm->getRepository('RestBundle:Marque');
	  $repository1 = $dm->getRepository('RestBundle:Image');
	  
	   
	  //checking if the marque is already exist
	  $marque1 = $repository->findOneByName($request->get('name'));
	  $image = $repository1->findOneById($request->get('idLogo'));
	  if(!empty($marque1)){
		$formatted = [
               'statut' => '404'
        ];
			
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
	  }
	   
	   // else we can add the mark
	   
	   $image->setIsUsed(true);
	   $dm->flush(); 
	   
	   $marque = new Marque();
	   $marque->setName($request->get('name'));
	   $marque->setIdLogo($request->get('idLogo'));
	   $marque->setPopularity($request->get('popularity'));
	   $dm->persist($marque);
       $dm->flush();
	   
	   $formatted = [
           'statut' => '200'
       ];
	   
	   header('Access-Control-Allow-Origin: *');
	   return new JsonResponse($formatted);
	}
	
	/**
     * @POST("/marque/modify")
	 * body-parm:idMarque(String) name(String), idLogo(String)
	 * modify a new marque
     */
    public function modifyMarqueAction(Request $request)
    {
		
	  $dm= $this->get('doctrine_mongodb')->getManager();
	  $repository = $dm->getRepository('RestBundle:Marque');
	  $repository1 = $dm->getRepository('RestBundle:Image');
	  
	   
	  //checking if the marque is already exist
	  $marque = $repository->findOneById($request->get('idMarque'));
	  $image = $repository1->findOneById($request->get('idLogo'));
	  if(empty($marque)){
		$formatted = [
               'statut' => '404'
        ];	
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
	  }
	   
	   // else we can modify the mark
	   $oldLogoId= $marque->getIdLogo();
	   if($oldLogoId != $request->get('idLogo')){
		    $oldImage = $repository1->findOneById($oldLogoId);
		    $dm->remove($oldImage);
			$dm->flush();
	   }
	   $image->setIsUsed(true);
	   $dm->flush(); 
	   
	   $marque->setName($request->get('name'));
	   $marque->setIdLogo($request->get('idLogo'));
	   $marque->setPopularity($request->get('popularity'));
       $dm->flush();
	   
	   $formatted = [
           'statut' => '200'
       ];
	   
	   header('Access-Control-Allow-Origin: *');
	   return new JsonResponse($formatted);
	}
	
	/**
     * @POST("/marque/delete")
	 * body-parm:idMarque(String) name(String), idLogo(String)
	 * modify a new marque
     */
    public function deleteMarqueAction(Request $request)
    { 
	   
	  $dm= $this->get('doctrine_mongodb')->getManager();
	  $repository = $dm->getRepository('RestBundle:Marque');
	  $repository1 = $dm->getRepository('RestBundle:Image');
	  
	  
	   
	  //checking if the marque is already exist
	  $marque = $repository->findOneById($request->get('idMarque'));
	  if(empty($marque)){
		$formatted = [
               'statut' => '404'
        ];
			
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
	  }
	  
	  $idLogo= $marque->getIdLogo();
	  $image = $repository1->findOneById($idLogo);
	  $dm->remove($image);
	  $dm->flush();
	  
	   
	  $dm->remove($marque);
      $dm->flush();
	   
	  $formatted = [
           'statut' => '200'
      ];
	   
	   header('Access-Control-Allow-Origin: *');
	   return new JsonResponse($formatted);
	}
	
	/**
     * @GET("/marque/all")
	 * return all the categories
     */
    public function getAllMarqueAction(Request $request)
    { 
		
		$qb= $this->get('doctrine_mongodb')
						->getManager()
						->createQueryBuilder('RestBundle:Marque')
						-> find()
						->sort('popularity', 'desc');

		$query= $qb->getQuery();
		$marques=$query->execute();
		
		$formatted = [];
		$response=[];
		foreach ($marques as $marque) {	
		
			$formatted[] = [
				'id' => $marque->getId(),
				'name'=> $marque->getName(),
				'idLogo'=> $marque->getIdLogo(),
				'popularity'=> $marque->getPopularity()
			];
		}
		
		$response=[
			'statut' => '200',
			'data'=> $formatted
		];
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($response);
	}

	/**
     * @GET("/marque/best")
	 * return all the categories
     */
    public function getBestMarqueAction(Request $request)
    { 
		
		$qb= $this->get('doctrine_mongodb')
						->getManager()
						->createQueryBuilder('RestBundle:Marque')
						-> find()
						->sort('popularity', 'desc')
						->limit(20);

		$query= $qb->getQuery();
		$marques=$query->execute();
		$formatted = [];
		$response=[];
		foreach ($marques as $marque) {	
		
			$formatted[] = [
				'id' => $marque->getId(),
				'name'=> $marque->getName(),
				'idLogo'=> $marque->getIdLogo(),
				'popularity'=> $marque->getPopularity()
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

?>