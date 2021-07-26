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
use RestBundle\Document\SCategory;
use RestBundle\Document\Product;
use RestBundle\Document\ASCCaracteristic;
use RestBundle\Document\Caracteristic;
use RestBundle\Document\Marque;
use RestBundle\Document\MarqueEmbedded;
use RestBundle\Document\ProductModel;


class ProductController extends Controller
{
	/**
     * @POST("/product")
	 * body-parm: name(String), description(string), id_category(String),id_scategory(String),id_sscategory(String),asc_caracteristics_id(array(string))
	 * product_car(array({name,unite,valeur})),_car(array({name,unite,valeur}))
	 * create a new product
     */
    public function postProductAction(Request $request)
    { 
	  $dm= $this->get('doctrine_mongodb')->getManager();
	  $repository = $dm->getRepository('RestBundle:Product');
	  
	  
	  // checking if the product is already exist
	   $product1 = $repository->findOneByName($request->get('name'));
	   if(!empty( $product1 )){
			$formatted = [
               'statut' => '404'
            ];
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
	   }
	      
	   //else we can created this product
	   $product= new Product();
	   $product->setName($request->get('name'));
	   $product->setDescription($request->get('description'));
	   $product->setNameCategory($request->get('name_category'));
	   $product->setNameScategory($request->get('name_scategory'));
	   $product->setNameSScategory($request->get('name_sscategory')); 
	  
	   $productCaracteristics = $request->get('product_car');
		
		$selectedMarque = json_decode($request->get('marque'),true);
		$marque= new MarqueEmbedded();
		$marque->setName($selectedMarque['name']);
		$marque->setIdLogo($selectedMarque['idLogo']);
		$product->setMarque($marque);
		$dm->persist($product);
		
		
	    foreach ( json_decode($productCaracteristics,true) as $caracteristic) {
	     $car= new Caracteristic();
		 $car->setName($caracteristic['name']);
		 $car->setUnity($caracteristic['unity']);
		 $car->setValue($caracteristic['value']);  
	     $product->addProductCaracteristic($car);
		 $dm->persist($car);
	   }
	
	   $dm->persist($product);
       $dm->flush();
	   
	   $formatted = [
          'statut' => '200'
       ];

	   header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
	}
	
	/**
     * @POST("/product/modify")
	 * body-parm: name(String), description(string), id_category(String),id_scategory(String),id_sscategory(String),asc_caracteristics_id(array(string))
	 * product_car(array({name,unite,valeur})),_car(array({name,unite,valeur}))
	 * create a new product
     */
    public function modifyProductAction(Request $request)
    { 
	  $dm= $this->get('doctrine_mongodb')->getManager();
	  $repository = $dm->getRepository('RestBundle:Product');
	  
	  
	  // retrieving the product
	   $product = $repository->findOneById($request->get('id_product'));
	   if(empty( $product )){
			$formatted = [
               'statut' => '404'
            ];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
	   }
	      
	   
	   if(!empty($request->get('name'))){
		$product->setName($request->get('name'));	
	   }

	   if(!empty($request->get('description'))){
		 $product->setDescription($request->get('description'));	
	   }

	   if(!empty($request->get('description'))){
		 $product->setDescription($request->get('description'));	
	   }

	   if(!empty($request->get('name_category'))){
		 $product->setNameCategory($request->get('name_category'));	
	   }

	   if(!empty($request->get('name_scategory'))){
		 $product->setNameScategory($request->get('name_scategory'));	
	   }

	   if(!empty($request->get('name_sscategory'))){
		 $product->setNameSScategory($request->get('name_sscategory')); 	
	   }
	  
	   if(!empty($request->get('marque'))){
		 	$selectedMarque = json_decode($request->get('marque'),true);
			$marque= new MarqueEmbedded();
			$marque->setName($selectedMarque['name']);
			$marque->setIdLogo($selectedMarque['idLogo']);
			$product->setMarque($marque);
			$dm->flush();
	   }


	    if(!empty($request->get('product_car'))){
	    	//product caracteristics
			$productCars  = $product->getProductCaracteristics();
			foreach ($productCars as $productCar) {
		      $product->removeProductCaracteristic($productCar);
			  $dm->flush();
			}

	    	$productCaracteristics = $request->get('product_car');
		    foreach ( json_decode($productCaracteristics,true) as $caracteristic) {
		     $car= new Caracteristic();
			 $car->setName($caracteristic['name']);
			 $car->setUnity($caracteristic['unity']);
			 $car->setValue($caracteristic['value']);  
		     $product->addProductCaracteristic($car);
			 $dm->persist($car);
		    }
	   }
	  
	   
	   /*$actualmodelCars = $product->getModelCaracteristics();
			foreach($actualmodelCars as $actualmodelCar){
			$product->removeModelCaracteristic($actualmodelCar);
		}*/
	   
       $dm->flush();
	   $formatted = [
               'statut' => '200'
       ];
	   
	   header('Access-Control-Allow-Origin: *');
	   return new JsonResponse($formatted);
	}
	
	/**
     * @POST("/product/delete")
	 * body-parm: idProduit(String)
	 * modify a new product model
     */
    public function deleteProductAction(Request $request)
    { 
		$dm= $this->get('doctrine_mongodb')->getManager();
		$repository1 = $dm->getRepository('RestBundle:Product');
		
		//retrieving of the model
		$product =  $repository1->findOneById($request->get('id_product'));
		if(empty($product)){
			$formatted = [
               'statut' => '404'
            ];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}
		// else you can delete the Asccategory
		$dm->remove($product);
		$dm->flush();
		$formatted = [
			'statut' => '200'
		];
		header('Access-Control-Allow-Origin: *');
	   return new JsonResponse($formatted);
	}
	
	/**
     * @GET("/product/all")
	 * return all the categories
     */
    public function getAllProductAction(Request $request)
    {
	
	 $dm= $this->get('doctrine_mongodb')->getManager();
	 $repository = $dm->getRepository('RestBundle:Product');
	 $repository1 = $dm->getRepository('RestBundle:Category');
	 $repository2 = $dm->getRepository('RestBundle:Seller');
	 $products = $repository->findAll();
	 $formatted = [];
	 $response=[];
	 $formattedBoutique=[];
	 $formattedMarque =[];
	 $formattedProductCar =[];
	 
     foreach ($products as $product) {
		 
		
		$marque= $product->getMarque();
			$formattedMarque =[
			 'name'=> $marque->getName(),
			 'idLogo'=> $marque->getIdLogo()
		];
		
		$productCaracteristics = $product->getProductCaracteristics();
		$formattedProductCar=[];
		foreach($productCaracteristics as $productCaracteristic ){
				$formattedProductCar[] = [
				'id' => $productCaracteristic->getId(),
				'name' => $productCaracteristic->getName(),
				'unity' => $productCaracteristic->getUnity(),
				'value' => $productCaracteristic->getValue()
				];
		}
		
		$formatted[] = [
			'id' => $product->getId(),
			'name'=> $product->getName(),
			'idCategory' => $product->getNameCategory(),
			'idScategory' => $product->getNameScategory(),
			'idSScategory' => $product->getNameSScategory(),
			'marque' =>  $formattedMarque,
			'description' => $product->getDescription(),
			'productCar' => $formattedProductCar
			
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
     * @GET("/product/{id}")
	 * url-parm: id(String) 
	 * return of a product
     */
	 public function getOneProductAction($id,Request $request)
     { 
		$dm= $this->get('doctrine_mongodb')->getManager();
	    $repository = $dm->getRepository('RestBundle:Product');
	    $repository1 = $dm->getRepository('RestBundle:ASCCaracteristic');
		
	    $product = $repository->findOneById($id);
	    if(empty( $product )){
			$formatted = [
               'statut' => '404'
            ];
		return new JsonResponse($formatted);
	   }
	   
	    // else you can return a response
	    $id_AscCats= $product->getAscCaracteristicsIds();
		$ascCATs = [];
		foreach( $id_AscCats as $id_AscCat) {
		 $cat= $repository1->findOneById($id_AscCat);
		 $ascCATs[] =[ 'id' => $cat->getId(),
						'name' => $cat->getName(),
						'unities' => $cat->getUnities()
					];
		}
		
		
	    $payload=[
		   'id' =>  $product->getId(),
		   'name' => $product->getName(),
		   'description' => $product->getDescription(),
		   'id_category' => $product->getIdCategory(),
		   'id_scategory' => $product->getIdScategory(),
		   'id_sscategory'=> $product-> getIdSScategory(),
		   'asc_cat' => $ascCATs
		];
	   
	   $formatted = [
			'statut' => '200',
			'response' => $payload
	   ];
		return new JsonResponse($formatted);
	}


	/**
     * @GET("/model/activate/all")
	 * return all the categories
     */
    public function getActivateAction(Request $request)
    {
	
	 	$dm= $this->get('doctrine_mongodb')->getManager();
	 	$repository = $dm->getRepository('RestBundle:ProductModel');
	 	$products = $repository->findAll();
	 
     	foreach ($products as $product) {
		 	$product->setIsActivated(true);
		  	$dm->flush();
	 	}

		$response=[
			'statut' => '200',
		];

	  	header('Access-Control-Allow-Origin: *');
	  	return new JsonResponse($response);
	}


	/**
     * @GET("/product/cat/change")
	 * return all the categories
     */
    public function getchangeCatAction(Request $request)
    {
		$dm= $this->get('doctrine_mongodb')->getManager();
	 	$products = $this->get('doctrine_mongodb')
						  ->getManager()
						  ->createQueryBuilder('RestBundle:ProductModel') 
						  ->field('nameCategory')->equals("telephonie et Accesoires")->getQuery()->execute();
	 
     	foreach ($products as $product) {
		 	$product->setNameCategory("Telephonie et accessoires");
		  	$dm->flush();
	 	}

		$response=[
			'statut' => '200',
		];

	  	header('Access-Control-Allow-Origin: *');
	  	return new JsonResponse($response);
	}


	/**
     * @GET("/model/virtual/all")
	 * return all the categories
     */
    public function getVirtualAction(Request $request)
    {
	
	 	$dm= $this->get('doctrine_mongodb')->getManager();
	 	$repository = $dm->getRepository('RestBundle:ProductModel');
	 	$products = $repository->findAll();
	 
     	foreach ($products as $product) {
		 	$product->setIsVirtual(false);
		  	$dm->flush();
	 	}

		$response=[
			'statut' => '200',
		];

	  	header('Access-Control-Allow-Origin: *');
	  	return new JsonResponse($response);
	}


	/**
     * @GET("/model/poids")
	 * return all the categories
     */
    public function getsetPoidAction(Request $request)
    {
	
	 	$dm= $this->get('doctrine_mongodb')->getManager();
	 	$repository = $dm->getRepository('RestBundle:ProductModel');
	 	$products = $repository->findAll();
	 
     	foreach ($products as $product) {
		 	$product->setWeight(1);
		  	$dm->flush();
	 	}

		$response=[
			'statut' => '200',
		];

	  	header('Access-Control-Allow-Origin: *');
	  	return new JsonResponse($response);
	}


	/**
     * @GET("/ounkoun/version")
	 * return all the categories
     */
    public function getVersionAction(Request $request)
    {
	
	 

		$response=[
			'version' => '2',
			'statut' => '200',
		];

	  	header('Access-Control-Allow-Origin: *');
	  	return new JsonResponse($response);
	}
}
