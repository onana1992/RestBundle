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
use RestBundle\Document\Seller;



class SellerController extends Controller
{
	 /**
     * @POST("/boutique")
	 * body-parm: name(String), idLogo(String)
	 * created a new marque
     */
    public function postSellertAction(Request $request)
    {
		
	  $dm= $this->get('doctrine_mongodb')->getManager();
	  $repository = $dm->getRepository('RestBundle:Seller');
	 
	     
	  //checking if the seller is already exist
	  $boutique1 = $repository->findOneByName($request->get('name'));
	  
	  if(!empty($boutique)){
		$formatted = [
               'statut' => '404'
        ];
			
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
	  }
	   
	   
	   $boutique = new Seller();
	   $boutique->setName($request->get('name'));
	   $boutique->setTel1($request->get('tel1'));
	   $boutique->setTel2($request->get('tel2'));
	   $boutique->setAdresse($request->get('adresse'));
	   $boutique->setIdImage($request->get('idImage'));
	   $dm->persist($boutique);
       $dm->flush();
	   
	   $formatted = [
           'statut' => '200'
       ];
	   
	   header('Access-Control-Allow-Origin: *');
	   return new JsonResponse($formatted);
	}
	
	/**
     * @POST("/boutique/modify")
	 * body-parm:
	 * modify boutique
     */
    public function modifySellerAction(Request $request)
    {
		
	  $dm= $this->get('doctrine_mongodb')->getManager();
	  $repository = $dm->getRepository('RestBundle:Seller');
	
	  
	   
	  //checking if the marque is already exist
	  $boutique = $repository->findOneById($request->get('idBoutique'));
	  
	  if(empty($boutique)){
		$formatted = [
               'statut' => '404'
        ];	
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
	  }
	   
	  
	   $boutique->setName($request->get('name'));
	   $boutique->setTel1($request->get('tel1'));
	   $boutique->setTel2($request->get('tel2'));
	   $boutique->setAdresse($request->get('adresse'));
	   $boutique->setIdImage($request->get('idImage'));
       $dm->flush();
	   
	   $formatted = [
           'statut' => '200'
       ];
	   
	   header('Access-Control-Allow-Origin: *');
	   return new JsonResponse($formatted);
	}
	
	/**
     * @POST("/boutique/delete")
	 * body-parm:idMarque(String) name(String), idLogo(String)
	 * modify a new seller
     */
    public function deleteBoutiqueAction(Request $request)
    { 
	   
	  $dm= $this->get('doctrine_mongodb')->getManager();
	  $repository = $dm->getRepository('RestBundle:Seller');
	
	  //checking if the seller is already exist
	  $boutique= $repository->findOneById($request->get('idBoutique'));
	  if(empty($boutique)){
		$formatted = [
             'statut' => '404'
        ];
			
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
	  }
	  
	 
	   
	  $dm->remove($boutique);
      $dm->flush();
	   
	  $formatted = [
           'statut' => '200'
      ];
	   
	   header('Access-Control-Allow-Origin: *');
	   return new JsonResponse($formatted);
	}

	
	/**
     * @GET("/boutique/all")
	 * return all the categories
     */
    public function getAllBoutiqueAction(Request $request)
    { 
		
		$qb= $this->get('doctrine_mongodb')
						->getManager()
						->createQueryBuilder('RestBundle:Seller')
						->find();

		$query= $qb->getQuery();
		$boutiques=$query->execute();
		
		$formatted = [];
		$response=[];
		foreach ($boutiques as $boutique) {	
		
			$formatted[] = [
				'id' => $boutique->getId(),
				'name'=> $boutique->getName(),
				'tel1'=> $boutique->getTel1(),
				'tel2'=> $boutique->getTel2(),
				'adresse'=> $boutique->getAdresse(),
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
     * @GET("/boutique/one/{name}")
	 * return all the categories
     */
    public function getOneBoutiqueAction($name,Request $request)
    { 
		$dm= $this->get('doctrine_mongodb')->getManager();
	    $repository = $dm->getRepository('RestBundle:Seller');

		$boutique= $repository->findOneByName(urldecode($name));
		if(empty($boutique)){

			$formatted = [
				'statut' => '404'
			];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}

		
		$formatted = [
			'id' => $boutique->getId(),
			'name'=> $boutique->getName(),
			'tel1'=> $boutique->getTel1(),
			'tel2'=> $boutique->getTel2(),
			'adresse'=> $boutique->getAdresse(),
			'idImage'=> $boutique->getIdImage(),
		];
		
		
		$response=[
			'statut' => '200',
			'data'=> $formatted
		];

		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($response);
	}

}

?>