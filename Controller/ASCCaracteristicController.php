p<?php

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
use RestBundle\Document\ASCCaracteristic;


class ASCCaracteristicController extends Controller
{
    /**
     * @POST("/ASCCaracteristic")
	 * post-parm: name(String) ,unitie(array of string)
	 * create a new associated category
     */
    public function postASCCategoryAction(Request $request)
    {
	  $dm= $this->get('doctrine_mongodb')->getManager();
	  $repository = $dm->getRepository('RestBundle:ASCCaracteristic');
	  
	  
	  // checking if the category is already exist
	  $caracteristic1 = $repository->findOneByName($request->get('name'));
	  if(!empty($caracteristic1 )){
			$formatted = [
               'statut' => '404'
            ];
		return new JsonResponse($formatted);
	   }
	   
	   // else we can add the caracteristic
	   $caracteristic= new ASCCaracteristic();
	   $caracteristic->setName($request->get('name'));
	   $caracteristic->setUnities($request->get('unities'));
	   $dm->persist($caracteristic);
       $dm->flush();
	   
	   $formatted = [
           'statut' => '200'
       ];
	   header('Access-Control-Allow-Origin: *');
	   return new JsonResponse($formatted);
	}
	
	
	
	/**
     * @POST("/ASCCaracteristic/modify")
	 * post-parm: idCar(String)  name(string), unities(array of string)
	 * modify an associated category
     */
	 public function modifyASCCaracteristicAction(Request $request){
	 
		$dm = $this->get('doctrine_mongodb')->getManager();
	    //check if the caracteristic exist
		$repository = $dm->getRepository('RestBundle:ASCCaracteristic');
		$caracteristic = $repository->findOneById($request->get('idCar'));
		if(empty($caracteristic)){
			 $formatted = [
                'statut' => '404'
             ];
			 return new JsonResponse($formatted);
		}
		
		//if it exist
		$caracteristic->setName($request->get('name'));
		$caracteristic->setUnities($request->get('unities'));
		$dm->flush();
		
	    $formatted = [
			'statut' => '200'
		];
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
	}
	
	/**
     * @POST("/ASCCaracteristic/delete")
	 * post-parm: idCar, name(string), unities(array of string)
	 * modify an associated category
     */
	 public function deleteASCCaracteristicAction(Request $request){
	 
		$dm= $this->get('doctrine_mongodb')->getManager();
	    $repository = $dm->getRepository('RestBundle:ASCCaracteristic');
		//check if the user exist
		$caracteristic = $repository->findOneById($request->get('idCar'));
		if(empty($caracteristic)){
			$formatted = [
				'statut' => '404'
			];	
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}
		// else you can delete the Asccategory
		$dm->remove($caracteristic);
		$dm->flush();
		$formatted = [
               'statut' => '200'
        ];
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
	}
	
	/**
     * @GET("/ASCCaracteristic/all")
	 * return all the categories
     */
    public function getAllCategoryAction(Request $request)
    {
		$dm= $this->get('doctrine_mongodb')->getManager();
		$repository = $dm->getRepository('RestBundle:ASCCaracteristic');
		$caracteristics = $repository->findAll();
		$formatted = [];
		$response=[];
		
		if(empty($caracteristics)){
			$formatted = [
				'statut' => '404'
			];	
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		} 
		
		//else we cann
		foreach ($caracteristics as $caracteristic) {
			$formatted[] = [
				'id' => $caracteristic->getId(),
				'name' => $caracteristic->getName(),
				'unities' => $caracteristic->getUnities()
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