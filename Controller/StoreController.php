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
use RestBundle\Document\Store;
use RestBundle\Document\Coordinates;
use RestBundle\Document\RetailSale;
use RestBundle\Document\WholeSale;
use RestBundle\Document\Catalogue;

class StoreController extends Controller
{
    /**
     * @POST("/store")
	 * body-parm: name(String), description(String), presentation(String), country(String), town(country), id_logo(String)
	 * created a new category
     */
    public function postStoreAction(Request $request)
    {
		$dm= $this->get('doctrine_mongodb')->getManager();
	    $repository = $dm->getRepository('RestBundle:Store');
		$store1 = $repository->findOneByName($request->get('name'));
		 if(!empty($store1)){
			$formatted = [
               'statut' => '404'
            ];
		return new JsonResponse($formatted);
	   }
	  
	      
	   //else we can created this product model
	   $store= new Store();
	   $store->setName($request->get('name'));
	   $store->setDescription($request->get('description'));
	   $store->setPresentation($request->get('presentation'));
	   $store->setCountry($request->get('country'));
	   $store->setTown($request->get('town'));
	   $store->setIdLogo($request->get('id_logo'));
	   $coordinate = new Coordinates();
	   $coordinate->setLatitude($request->get('latitude'));
	   $coordinate->setLongitude($request->get('longitude'));
	   $store->setCoordinates($coordinate);
	   
	   $dm->persist($coordinate);
	   $dm->persist($store);
       $dm->flush();
	   $formatted = [
        'status' => '200'
       ];
	   return new JsonResponse($formatted);
	
	}
	
	/**
     * @DELETE("/store/{id}")
	 * url-parm: id(string)
	 * create a new product model
     */
	public function deleteProductModelAction($id , Request $request)
    {
		$dm= $this->get('doctrine_mongodb')->getManager();
	    $repository = $dm->getRepository('RestBundle:Store');
		
	   // checking if the category is already exist
	   $product = $repository->findOneById($id);
	   if(empty($product)){
			$formatted = [
               'statut' => '404'
            ];
		return new JsonResponse($formatted);
	   }

	   // else you can delete the product
	   // no forget deleting the associated photo
		$dm->remove($product);
		$dm->flush();
		
			$formatted = [
               'statut' => '200'
            ];
		return new JsonResponse($formatted);
	   
	}
	
	/**
     * @PUT("/store/{id}")
	 * body-parm: name(String), description(String), presentation(String), country(String), town(country), id_logo(String)
	 * url-parm: id(string)
	 * create a new product model
     */
    public function updateStoreAction($id, Request $request)
    { 
	  $dm= $this->get('doctrine_mongodb')->getManager();
	  $repository = $dm->getRepository('RestBundle:Store');
	  
	  // checking if the category is exist
	   $store = $repository->findOneById($id);
	   if(empty($store)){
			$formatted = [
               'statut' => '404'
            ];
		return new JsonResponse($formatted);
	   }
	      
	   //else we can created this product model
	   
	   
	   $name= $request->get('name');
		if($name!=null){
		 $store->setName($name);
		}
		
		$description= $request->get('description');
		if($description!=null){
		 $store->setDescription($description);
		}
		
		$presentation= $request->get('presentation');
		if($presentation!=null){
		 $store->setPresentation($presentation);
		}
		
		$country= $request->get('country');
		if($country!=null){
		$store->setCountry($country);
		}
		
		$town= $request->get('town');
		if($town!=null){
		 $store->setTown($town);
		}
		
		$idLogo= $request->get('id_logo');
		if($idLogo!=null){
		 $store->setIdLogo($idLogo);
		}
	   
       $dm->flush();
	   $formatted = [
        'statut' => '200'
       ];
	   header('Access-Control-Allow-Origin: *');
	   return new JsonResponse($formatted);
	
	}
	
	/**
     * @POST("/store/product")
	 * body-parm: 
	 * created a new category
     */
    public function addProductAction(Request $request)
    {
		$dm= $this->get('doctrine_mongodb')->getManager();
	    $repository = $dm->getRepository('RestBundle:Store');
		$store2 = $repository->findOneById($request->get('id_store'));
		// if the store do not exist
		if(empty($store2)){
			$formatted = [
               'statut' => '404'
            ];
		return new JsonResponse($formatted);
	   }
	   
	   // else we can add
	   $id_product= $request->get('id_product');
	   $retailSaleParm= $request->get('retailSale');
	   $wholeSaleParm= $request->get('wholeSale');
	   $catalogue= new Catalogue();
	   
	   $catalogue->setIdProduct($id_product); 
	   if(!empty($wholeSaleParm)){
	    $wholeSale= new WholeSale();
	   }
	   
	    
	   
	   if(!empty($retailSaleParm)){
	     $retailSale= new RetailSale();
		 $retailSale->setPrice($retailSaleParm['price']);
		 $retailSale->setQuantity($retailSaleParm['quantity']);
		 $retailSale->setIsinPromotion($retailSaleParm['isInpromotion']);
		 $retailSale->setPromotionalPrice($retailSaleParm['promotionalPrice']);
		 $retailSale->setEndPromotionDate($retailSaleParm['endPromotionDate']);
		 $retailSale->setCurrency($retailSaleParm['currency']);
		 $catalogue->setRetailSale($retailSale);
	   }
	   
	   
	
	   $store2->setLocality("mendong1");
	   $store2->addCatalogue($catalogue);
	   $dm->persist($retailSale);
	   $dm->persist($catalogue);
	   //$dm->persist($retailSale); 
       $dm->flush();
	   
	   
	   
	   $formatted = [
               'statut' =>  '200'
       ];
		return new JsonResponse($formatted);
	}
	
}