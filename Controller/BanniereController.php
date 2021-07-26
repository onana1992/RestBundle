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
use RestBundle\Document\Banniere;
use RestBundle\Document\Image;


class BanniereController extends Controller
{

	 /**
     * @POST("/banniere")
	 * body-parm: name(String), idLogo(String)
	 * created a new marque
     */
    public function postBaniereAction(Request $request)
    {
		
	  $dm= $this->get('doctrine_mongodb')->getManager();
	  $repository = $dm->getRepository('RestBundle:Banniere');
	  $repository1 = $dm->getRepository('RestBundle:Image');
	  
	   
	  //checking if the marque is already exist
	  $image = $repository1->findOneById($request->get('idImage'));  
	  $image->setIsUsed(true);
	  $dm->flush(); 
	   
	  $baniere = new Banniere();
	  $baniere->setIdImage($request->get('idImage'));
	  $baniere->setPage($request->get('page'));
	  $baniere->setPriority($request->get('priority'));
	  $dm->persist($baniere);
      $dm->flush();
	   
	   $formatted = [
           'statut' => '200'
       ];
	   
	   header('Access-Control-Allow-Origin: *');
	   return new JsonResponse($formatted);
	}
	
	/**
     * @POST("/banniere/modify")
	 * body-parm:idBaniere(String) page(string), idLogo(String)
	 * modify a new marque
     */
    public function modifyBanniereAction(Request $request)
    {
		
	  $dm= $this->get('doctrine_mongodb')->getManager();
	  $repository = $dm->getRepository('RestBundle:Banniere');
	  $repository1 = $dm->getRepository('RestBundle:Image');
	  

	  $baniere = $repository->findOneById($request->get('idBanniere'));
	  $image = $repository1->findOneById($request->get('idImage'));
	  if(empty($baniere)){

		$formatted = [
               'statut' => '404'
        ];	
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
	  }
	   
	   // else we can modify the mark
	   $oldImageId= $baniere->getImage();
	   if($oldImageId != $request->get('idImage')){
		    $oldImage = $repository1->findOneById($oldIdImage);
		    $dm->remove($oldImage);
			$dm->flush();
	   }
	   $image->setIsUsed(true);
	   $dm->flush(); 
	   
	   $baniere->setIdImage($request->get('idImage'));
	   $baniere->setPage($request->get('page'));
	   $baniere->setPriority($request->get('priority'));
       $dm->flush();
	   
	   $formatted = [
           'statut' => '200'
       ];
	   
	   header('Access-Control-Allow-Origin: *');
	   return new JsonResponse($formatted);
	}
	
	/**
     * @POST("/banniere/delete")
	 * body-parm:
	 * modify a new marque
     */
    public function deleteBaniereAction(Request $request)
    { 
	   
	  $dm= $this->get('doctrine_mongodb')->getManager();
	  $repository = $dm->getRepository('RestBundle:Banniere');
	  $repository1 = $dm->getRepository('RestBundle:Image');
	  
	  
	   
	  //checking if the baniere is already exist
	  $baniere = $repository->findOneById($request->get('idBanniere'));
	  if(empty($baniere)){
		
		$formatted = [
               'statut' => '404'
        ];
			
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
	  }
	  
	  $idImage= $baniere->getImage();
	  $image = $repository1->findOneById($idImage);
	  $dm->remove($image);
	  $dm->flush();
	  
	   
	  $dm->remove($baniere);
      $dm->flush();
	   
	  $formatted = [
           'statut' => '200'
      ];
	   
	   header('Access-Control-Allow-Origin: *');
	   return new JsonResponse($formatted);
	}
	
	/**
     * @GET("/baniere/all")
	 * return all the bannier
     */
    public function getAllBanniereAction(Request $request)
    { 
		
		$qb= $this->get('doctrine_mongodb')
						->getManager()
						->createQueryBuilder('RestBundle:Banniere')
						-> find()
						->sort('priority', 'desc');

		$query= $qb->getQuery();
		$banieres=$query->execute();
		
		$formatted = [];
		$response=[];
		foreach ($banieres as $baniere) {	
		
			$formatted[] = [
				'id' => $baniere->getId(),
				'page'=> $baniere->getPage(),
				'image'=> $baniere->getImage(),
				'priority'=> $baniere->getPriority()
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