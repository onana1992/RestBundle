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
use RestBundle\Document\ProductModel;
use RestBundle\Document\Caracteristic;
use RestBundle\Document\RetailSale;
use RestBundle\Document\WholeSale;
use RestBundle\Document\BuyWithMeSale;
use RestBundle\Document\Detail;
use RestBundle\Document\Product;
use RestBundle\Document\Marque;
use RestBundle\Document\MarqueEmbedded;
use RestBundle\Document\Notation;
use RestBundle\Document\Commentaire;


class ProductModelController extends Controller
{

  // connexion a la bd 
        
	/**
     * @POST("/product/model")
	 * body-parm: idProduit(String), name(String), description(string), id_category(String),id_scategory(String),id_sscategory(String),caracteristic(array)
	 * create a new product model
     */
    public function postProductModelAction(Request $request)
    { 
	  
		$dm= $this->get('doctrine_mongodb')->getManager();
		$repository1 = $dm->getRepository('RestBundle:ProductModel');
		$repository2 = $dm->getRepository('RestBundle:Product');
		$otherCaracteristics='';
	  
		// retreiving the product of the model
		$product1= $repository2->findOneByName($request->get('nameProduct'));
		if(empty($product1)){
			$formatted = [
               'statut' => '404'
            ];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}
		
		// else we can create a new model
		$model= new ProductModel();
		$model->setName($product1->getName());
		$model->setIdProduit($product1->getName());
		$model->setDescription($product1->getDescription());
	    $model->setNameCategory($product1->getNameCategory());
		$model->setNameScategory($product1->getNameScategory());
		$model->setNameSScategory($product1->getNameSScategory());
		
		
		//product caracteristics
		 $productCaracteristics = $product1->getProductCaracteristics();
			foreach($productCaracteristics as $productCaracteristic){
			$model->addProductCaracteristic($productCaracteristic);
		} 
		
		//model caracteristics
		$modelCaracteristics = json_decode($request->get('modelCar'),true);
		foreach ($modelCaracteristics as $caracteristic) {
	     $car= new Caracteristic();
		 $car->setName($caracteristic['name']);
		 $car->setUnity($caracteristic['unity']);
		 $car->setValue($caracteristic['value']);
		 $otherCaracteristics = $otherCaracteristics.' - '.$caracteristic['value'].$caracteristic['unity'];
	     $model->addModelCaracteristic($car);
		 $dm->persist($car);
		}   
		
		//images
		$model->setIdImage($request->get('idImage'));
		$model->setIdBigImage1($request->get('idBigImage1'));
		$model->setIdBigImage2($request->get('idBigImage2'));
		$model->setIdBigImage3($request->get('idBigImage3'));
		$model->setIdBigImage4($request->get('idBigImage4')); 
		$model->setWeight($request->get('weight'));
		$model->setIsActivated(true);

		$model->setIsVirtual($request->get('isVirtual'));
		$model->setPopularity($request->get('popularity'));
		$model->setQuantity($request->get('quantity'));
		$model->setIdSeller($request->get('idVendeur'));
		$model->setTaille($request->get('taille'));
		
		//insertion date
		$model->setInsertionDate(new \DateTime('now'));
		
		//details
		$modelDetails = json_decode($request->get('modelDetail'),true);
		foreach ($modelDetails as $modelDetail) {
	     $detail= new Detail();
		 $detail->setName($modelDetail['name']);
		 $detail->setValue($modelDetail['value']);  
	     $model->addDetail($detail);
		 $dm->persist($detail);
		} 
		
		//marque
		if(!empty($product1->getMarque())){
			$parmMarque = $product1->getMarque();
			$marque = new MarqueEmbedded();
			$marque->setName($parmMarque->getName());
			$marque->setIdLogo($parmMarque->getIdLogo());
			$model->setMarque($marque);
		
		} 
		
		//retailSale
		$parmRetailSale = json_decode($request->get('retailSale'),true);
		if($parmRetailSale!= null ){
			$retailSale = new RetailSale();
			$retailSale->setPrice($parmRetailSale['price']);
			$retailSale->setPromotionalPrice($parmRetailSale['promoPrice']);
			if($parmRetailSale['isInPromotion']){
				$retailSale->setIsinPromotion(true);
			}else{
				$retailSale->setIsinPromotion(false);
			}
			$retailSale->setEndPromotionDate($parmRetailSale['endPromotionDate']);
			$model->setRetailSale($retailSale);
		}
		
		
		
		//wholeSale
		$parmWholeSale = json_decode($request->get('wholeSale'),true);
		if($parmWholeSale!= null ){
			$wholeSale = new wholeSale();
			$wholeSale->setIsPersonalizable($parmWholeSale['isPersonalizable']);
			$wholeSale->setPrice($parmWholeSale['price']);
			$wholeSale->setPromotionalPrice($parmWholeSale['promoPrice']);
			$wholeSale->setIsinPromotion($parmWholeSale['isInPromotion']);
			$wholeSale->setLotQuantity($parmWholeSale['lotQuantity']);
			$wholeSale->setEndPromotionDate($parmWholeSale['endPromotionDate']);
			$model->setWholeSale($wholeSale);
		}
		
		//BuyWithSale
		$parmBuyWithSale = json_decode($request->get('BuyWithMeSale'),true);
		if($parmBuyWithSale!= null ){
			$buyWithSale = new BuyWithMesale();
			$buyWithSale->setIsPersonalizable($parmBuyWithSale['isPersonalizable']);
			$buyWithSale->setPrice($parmBuyWithSale['price']);
			$buyWithSale->setDuree($parmBuyWithSale['duree']);
			$buyWithSale->setLotQuantity($parmBuyWithSale['lotQuantity']);
			$model->setBuyWithMeSale($buyWithSale);
		}
		
		if($parmRetailSale!= null ){
			//actual price
			$model->setActualPrice($parmRetailSale['price']);
		}
		else{
			$model->setActualPrice($parmWholeSale['price']);
		}
		
		//edition of retail sale
		$model->setName($product1->getName().$otherCaracteristics);
		
		// checking if the model is already exist
		$model1 = $repository1->findOneByName($product1->getName().$otherCaracteristics);
		if(!empty($model1)){
			$formatted = [
               'statut' => '404'
            ];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}
		
		$dm->persist($model);
        $dm->flush();
		$formatted = [
			'statut' => '200'
		];
	   header('Access-Control-Allow-Origin: *');
	   return new JsonResponse($formatted);  
	}
	
	/**
     * @POST("/product/model/modify")
	 * body-parm: idProduit(String), name(String), description(string), id_category(String),id_scategory(String),id_sscategory(String),caracteristic(array)
	 * modify a new product model
     */
    public function modifyProductModelAction(Request $request)
    { 

	  	$dm= $this->get('doctrine_mongodb')->getManager();
		$repository1 = $dm->getRepository('RestBundle:ProductModel');
		$repository2 = $dm->getRepository('RestBundle:Product');
		$otherCaracteristics='';
	  
		
		$model= $repository1->findOneByName($request->get('old_name'));
		if(empty($model)){
			$formatted = [
               'statut' => '404'
            ];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}
		
		// else we can modify the model
		
		$model->setName($request->get('name'));
		$model->setDescription($request->get('description'));
		
	    // $model->setNameCategory($request->get('idCategory'));
		//$model->setNameScategory($request->get('idScategory'));
		//$model->setNameSScategory($request->get('idSScategory'));
		
		
		//product caracteristics
		$productCars  = $model->getProductCaracteristics();
		foreach ($productCars as $productCar) {
	      $model->removeProductCaracteristic($productCar);
		  $dm->flush();
		}
		$productCaracteristics = json_decode($request->get('productCar'),true);

		foreach ($productCaracteristics as $caracteristic){
	     $car= new Caracteristic();
		 $car->setName($caracteristic['name']);
		 $car->setUnity($caracteristic['unity']);
		 $car->setValue($caracteristic['value']);
	     $model->addProductCaracteristic($car);
		} 

		if(!empty($request->get('marque'))){
		 	$selectedMarque = json_decode($request->get('marque'),true);
			$marque= new MarqueEmbedded();
			$marque->setName($selectedMarque['name']);
			$marque->setIdLogo($selectedMarque['idLogo']);
			$model->setMarque($marque);
			$dm->flush();
	   }
		
		//model caracteristics
		
		$modelCars  = $model->getModelCaracteristics();
		foreach ($modelCars as $modelCar) {
	      $model->removeModelCaracteristic($modelCar);
		  $dm->flush();
		}
		$modelCaracteristics = json_decode($request->get('modelCar'),true);
		foreach ($modelCaracteristics as $caracteristic) {
	     $car1= new Caracteristic();
		 $car1->setName($caracteristic['name']);
		 $car1->setUnity($caracteristic['unity']);
		 $car1->setValue($caracteristic['value']);
	     $model->addModelCaracteristic($car1);
		}   
		
		
		$model->setIdImage($request->get('idImage'));
		$model->setIdBigImage1($request->get('idBigImage1'));
		$model->setIdBigImage2($request->get('idBigImage2'));
		$model->setIdBigImage3($request->get('idBigImage3'));
		$model->setIdBigImage4($request->get('idBigImage4')); 
		$model->setWeight($request->get('weight'));
		$model->setTaille($request->get('taille'));
		
		if($request->get('isActivated')=="true"){
				$model->setIsActivated(true);
		}else{
				$model->setIsActivated(false);
		}

		if($request->get('isVirtual')=="true"){
				$model->setIsVirtual(true);
		}else{
				$model->setIsVirtual(false);
		}


		
		$model->setDescription($request->get('description'));
		$model->setPopularity($request->get('popularity'));
		$model->setQuantity($request->get('quantity'));
		$model->setIdSeller($request->get('idVendeur'));
	
		
		//details
		$details  = $model->getDetails();
		foreach ($details as $detail) {
	      $model->removeDetail($detail);
		  $dm->flush();
		}
		
		$modelDetails = json_decode($request->get('modelDetail'),true);
		foreach ($modelDetails as $modelDetail) {
	     $detail= new Detail();
		 $detail->setName($modelDetail['name']);
		 $detail->setValue($modelDetail['value']);  
	     $model->addDetail($detail);
		} 
		
		
		
		//retailSale
		$parmRetailSale = json_decode($request->get('retailSale'),true);
		if($parmRetailSale!= null ){
			$retailSale = new RetailSale();
			$retailSale->setPrice($parmRetailSale['price']);
			$retailSale->setPromotionalPrice($parmRetailSale['promotionalPrice']);
			if($parmRetailSale['isInPromotion']){
				$retailSale->setIsinPromotion(true);
			}else{
				$retailSale->setIsinPromotion(false);
			}
			$retailSale->setEndPromotionDate($parmRetailSale['endPromotionDate']);
			$model->setRetailSale($retailSale);
		}
		
		
		//BuyWithSale
		$parmBuyWithSale = json_decode($request->get('BuyWithMeSale'),true);
		if($parmBuyWithSale!= null ){
			$buyWithSale = new BuyWithMesale();
			$buyWithSale->setIsPersonalizable($parmBuyWithSale['isPersonalizable']);
			$buyWithSale->setPrice($parmBuyWithSale['price']);
			$buyWithSale->setDuree($parmBuyWithSale['duree']);
			$buyWithSale->setLotQuantity($parmBuyWithSale['lotQuantity']);
			$model->setBuyWithMeSale($buyWithSale);
		}
		
        $dm->flush();
		$formatted = [
			'statut' => '200'
		];
	   header('Access-Control-Allow-Origin: *');
	   return new JsonResponse($formatted); 
	}
	
	/**
     * @POST("/product/model/delete")
	 * body-parm: idProduit(String)
	 * modify a new product model
     */
    public function deleteProductModelAction(Request $request)
    { 
		$dm= $this->get('doctrine_mongodb')->getManager();
		$repository1 = $dm->getRepository('RestBundle:ProductModel');
		
		//retrieving of the model
		$model =  $repository1->findOneById($request->get('idModel'));
		if(empty($model)){
			$formatted = [
               'statut' => '404'
            ];
			return new JsonResponse($formatted);
		}
		// else you can delete the Asccategory
		$dm->remove($model);
		$dm->flush();
		$formatted = [
			'statut' => '200'
		];
		header('Access-Control-Allow-Origin: *');
	   return new JsonResponse($formatted);
	}
	
	/**
     * @POST("/product/model/note")
	 * body-parm: idProduit(String)
	 * modify a new product model
     */
    public function notationAction(Request $request)
    { 
		$dm= $this->get('doctrine_mongodb')->getManager();
		$repository1 = $dm->getRepository('RestBundle:ProductModel');
		
		//retrieving of the model
		$model =  $repository1->findOneById($request->get('idModel'));
		if(empty($model)){
			$formatted = [
               'statut' => '404'
            ];
			return new JsonResponse($formatted);
		}
		
		$notation = $model->getNotation();
		$note= $request->get('note');
		if(empty($notation)){
			$newNotation = new Notation();
			if($note==1){
				$newNotation->setLevel1(1);
				$newNotation->setLevel2(0);
				$newNotation->setLevel3(0);
				$newNotation->setLevel4(0);
				$newNotation->setLevel5(0);
			}
			else if($note==2){
				$newNotation->setLevel1(0);
				$newNotation->setLevel2(1);
				$newNotation->setLevel3(0);
				$newNotation->setLevel4(0);
				$newNotation->setLevel5(0);
			}
			else if($note==3){
				$newNotation->setLevel1(0);
				$newNotation->setLevel2(0);
				$newNotation->setLevel3(1);
				$newNotation->setLevel4(0);
				$newNotation->setLevel5(0);
			}
			else if($note==4){
				$newNotation->setLevel1(0);
				$newNotation->setLevel2(0);
				$newNotation->setLevel3(0);
				$newNotation->setLevel4(1);
				$newNotation->setLevel5(0);
			}
			else if($note==5){
				$newNotation->setLevel1(0);
				$newNotation->setLevel2(0);
				$newNotation->setLevel3(0);
				$newNotation->setLevel4(0);
				$newNotation->setLevel5(1);
			}
			   
			$model->setNotation($newNotation);
			$dm->flush();
		}
		else{
			if($note==1){
				$notation->setLevel1($notation->getLevel1()+1);
			}
			else if($note==2){
				$notation->setLevel2($notation->getLevel2()+1);
			}
			else if($note==3){
				$notation->setLevel3($notation->getLevel3()+1);
			}
			else if($note==4){
				$notation->setLevel4($notation->getLevel4()+1);
			}
			else if($note==5){
				$notation->setLevel5($notation->getLevel5()+1);
			}
			
			$model->setNotation($notation);
			$dm->flush();
		}
		
		$formatted = [
			'statut' => '200'
		];
		header('Access-Control-Allow-Origin: *');
	    return new JsonResponse($formatted);
	}


	/**
     * @POST("/product/model/comment")
	 * body-parm: idProduit(String)
	 * modify a new product model
     */
    public function commentAction(Request $request)
    { 
		$dm= $this->get('doctrine_mongodb')->getManager();
		$repository1 = $dm->getRepository('RestBundle:ProductModel');
		
		//retrieving of the model
		$model =  $repository1->findOneById($request->get('idModel'));
		if(empty($model)){
			$formatted = [
               'statut' => '404'
            ];
			return new JsonResponse($formatted);
		}
		
		$comment= new Commentaire();
		$comment->setName($request->get('name'));
		$comment->setFirstName($request->get('firstName'));
		$comment->setLogin($request->get('login'));
		$comment->setValeur($request->get('valeur'));
		$comment->setDate(new \DateTime('now'));
		
		$model->addCommentaire($comment);
		$dm->flush();
		
		$formatted = [
			'statut' => '200'
		];
		header('Access-Control-Allow-Origin: *');
	    return new JsonResponse($formatted);
	}


/**********************************************************************************************//***************************************** get by category ************************************/	
    
    /**
     * @GET("/product/model/category/{nom_category}/{page}/{filter_option}/{min_price}/{max_price}/{marque}")
	 * url-parm: id_category:id de la categorie, sous categorie et sous sous categorie
	 * return of a product of a category oder by most recently added
     */
	 public function getProductByCategory1Action($nom_category,$page,$filter_option,$min_price,$max_price,$marque,Request $request)
     {  
	 
		$FILTERBYMOSTRESCENT="filter-by-most-rescent";
		$FILTERBYPRICEASC="filter-by-price-asc";
		$FILTERBYPRICEDESC="filter-by-price-desc";
		$FILTERBYPOPULARITY="filter-by-popularity";
		$FILTERBYNEW="filter-by-new";
		$formatted=[];
		$m=0;
		
		// select of all the marque
		$dm= $this->get('doctrine_mongodb')->getManager();
		$productModelCollection = $dm->getDocumentCollection('RestBundle:ProductModel')->getMongoCollection();
		$criteria = array("nameCategory" => $nom_category);
		$allMarquesName= $productModelCollection->distinct("marque.name", $criteria);
		$allMarquesIdLogo= $productModelCollection->distinct("marque.idLogo", $criteria);
		$formattedAllMarque=[];
		$formattedWholeSale=[];
		$formattedRetailSale=[];

		for($i=0; $i< sizeof($allMarquesName); $i++){

			$formattedAllMarque[$i]=[
				'name'=>$allMarquesName[$i],
				'idLogo'=> $allMarquesIdLogo[$i]
			];
		}
	
						
		$qb= $this->get('doctrine_mongodb')
						->getManager()
						->createQueryBuilder('RestBundle:ProductModel')
						->field('nameCategory')->equals($nom_category)
						->field('isActivated')->equals(true);
						
		if($filter_option==$FILTERBYMOSTRESCENT){
			$qb= $qb->sort('insertionDate', 'desc');
		}
		
		if($filter_option==$FILTERBYPRICEASC){
			$qb= $qb->sort('actualPrice', 'asc');
		}
		
		if($filter_option==$FILTERBYPRICEDESC){
			$qb= $qb->sort('actualPrice', 'desc');
		}
		
		if($filter_option==$FILTERBYPOPULARITY){
			$qb= $qb->sort('popularity', 'desc');
		}
	
		
		if($marque!="null"){
			$qb= $qb->field('marque.name')->equals($marque);
		}
		
		$n=0;
		$plusPetitPrix=0;
		$plusGrandPrix=0;
		$tour=1;
		$query1 = $qb->getQuery();
		$products1=$query1->execute();
		foreach($products1 as $product){
			if($tour == 1){
				$plusPetitPrix = $product->getActualPrice();
			}
			 if($product->getActualPrice() > $plusGrandPrix ){
				$plusGrandPrix = $product->getActualPrice();
			}
			
			if($product->getActualPrice() < $plusPetitPrix ){
				$plusPetitPrix = $product->getActualPrice();
			} 
			
			$tour++;
		}
		
		
		if($min_price!="null" && $max_price!="null" ){
			$qb= $qb->field('actualPrice')->range(floatval($min_price),floatval($max_price)+1);
		}
		
		//retieving of number of product with min and max price
		$products2 = $qb->getQuery()->execute();
		foreach($products2 as $product1){
		 $n++;
		}
		
		$query = $qb->skip(40*($page-1))
					->limit(40)
                    ->getQuery();
					
		$products = $query->execute();
		
		
		
		foreach($products as $product){
		    $productCaracteristics = $product->getProductCaracteristics();
			$modelCaracteristics = $product->getModelCaracteristics();
			$details = $product->getDetails();
			$retailSale= $product->getRetailSale();
			$formattedProductCar =[];
			$formattedModelCar =[];
			$formattedDetail=[];
			$formattedWholeSale=[];
			$formattedRetailSale=[];
			$formattedBWMSale=[];
			$formattedNotation=[];
			
			$marque= $product->getMarque();
			$formattedMarque =[
			 'name'=> $marque->getName(),
			 'idLogo'=> $marque->getIdLogo()
			];
			
			$retailSale = $product->getRetailSale();
			if($retailSale!= null){
				$date= $retailSale->getEndPromotionDate();
				$formattedRetailSale=[
					'price'=> $retailSale->getPrice(),
					'isInPromotion'=>$retailSale->getIsinPromotion(),
					'promotionalPrice'=> $retailSale->getPromotionalPrice(),
					'endPromotionDate'=> $date
				];
			}
			
			$wholeSale = $product->getWholeSale();
			if($wholeSale!= null){
				$date= $wholeSale->getEndPromotionDate();
				$formattedWholeSale=[
					'price'=> $wholeSale->getPrice(),
					'isInPromotion'=>$wholeSale->getIsinPromotion(),
					'promotionalPrice'=> $wholeSale->getPromotionalPrice(),
					'lotQuantity' => $wholeSale->getLotQuantity(),
					'endPromotionDate'=> $date
				];
			}
			
			$buyWithMeSale = $product->getBuyWithMeSale();
			if($buyWithMeSale!= null){
				$formattedBWMSale=[
					'price'=> $buyWithMeSale->getPrice(),
					'lotQuantity' => $buyWithMeSale->getLotQuantity(),
					'duree'=>  $buyWithMeSale->getDuree(),
					'ispersonnalizable'=> $buyWithMeSale->getIsinPersonalizable()
				];
			}
			
			$notation = $product->getNotation();
			if($notation!= null){
				$formattedNotation=[
					'level1'=> $notation->getLevel1(),
					'level2' => $notation->getLevel2(),
					'level3'=>  $notation->getLevel3(),
					'level4'=> $notation->getLevel4(),
					'level5'=> $notation->getLevel5(),
					'note'=> ($notation->getLevel1()*1 + $notation->getLevel2()*2 + $notation->getLevel3()*3 + $notation->getLevel4()*4 + $notation->getLevel5()*5)/
					($notation->getLevel1() + $notation->getLevel2() + $notation->getLevel3() + $notation->getLevel4()+ $notation->getLevel5()),
					'total'=> $notation->getLevel1() + $notation->getLevel2() + $notation->getLevel3() + $notation->getLevel4()+ $notation->getLevel5()
				];
			}
			else{
				$formattedNotation=[
					'level1'=> 0,
					'level2' => 0,
					'level3'=>  0,
					'level4'=> 0,
					'level5'=> 0,
					'note'=> 0,
					'total'=>0,
				];
			}
			
			foreach($productCaracteristics as $productCaracteristic ){
				$formattedProductCar[] =[
				'id' => $productCaracteristic->getId(),
				'name' => $productCaracteristic->getName(),
				'unity' => $productCaracteristic->getUnity(),
				'value' => $productCaracteristic->getValue()
				];
			}

			foreach($modelCaracteristics as $modelCaracteristic){
				$formattedModelCar[] =[
				'id' => $modelCaracteristic->getId(),
				'name' => $modelCaracteristic->getName(),
				'unity' => $modelCaracteristic->getUnity(),
				'value' => $modelCaracteristic->getValue()
				];
			}
			
			foreach($details as $detail){
				$formattedDetail[] =[
				'name' => $detail->getName(),
				'value' => $detail->getValue()
				];
			}
			 
			 
		     $formatted[] =[
			 'id'=>	$product->getId(),
             'name' => $product->getName(),
			 'productName'=>$product->getIdProduit(),
			 'description' => $product->getDescription(),
			 'quantity'=> $product->getQuantity(),
			 'taille'=> $product->getTaille(),
			 'nameCategory' => $product->getNameCategory(),
			 'idScategory' => $product->getNameScategory(),
			 'idSScategory' => $product->getNameSScategory(),
			 'idImage'=> $product->getIdImage(),
			 'idBigImage1'=> $product->getIdBigImage1(),
			 'idBigImage2'=> $product->getIdBigImage2(),
			 'idBigImage3'=> $product->getIdBigImage3(),
			 'idBigImage4'=> $product->getIdBigImage4(),
			 'isVirtual' => $product->getIsVirtual(),
			 'detail' => $formattedDetail,
			 'marque'=> $formattedMarque,
			 'productCar' => $formattedProductCar,
			 'modelCar' =>	$formattedModelCar,
			 'retailSale' => $formattedRetailSale,
			 'wholeSale'=> $formattedWholeSale,
			 'BWMSale'=> $formattedBWMSale,
			 'notation'=> $formattedNotation
           ]; 
		}
		
        $data=[
		'size'=> $n,
		'plusGrandPrix' => $plusGrandPrix,
		'plusPetitPrix' => $plusPetitPrix,
		'marque' => $formattedAllMarque,
		'products'=> $formatted
		];
		
		 $response = [
        'status' => '200',
		'data' => $data
		];
	   header('Access-Control-Allow-Origin: *');
	   return new JsonResponse($response);
	 }
	
/**************************************************************************************/	 
/***************************** get by sous scategory***********************************/	
    /**
     * @GET("/product/model/scategory/{nom_scategory}/{page}/{filter_option}/{min_price}/{max_price}/{marque}")
	 * url-parm: id_category:id de la categorie, sous categorie et sous sous categorie
	 * return of a product of a category oder by most recently added
     */
	 public function getProductBySCategory1Action($nom_scategory,$page,$filter_option,$min_price,$max_price,$marque,Request $request)
     {  
		$FILTERBYMOSTRESCENT="filter-by-most-rescent";
		$FILTERBYPRICEASC="filter-by-price-asc";
		$FILTERBYPRICEDESC="filter-by-price-desc";
		$FILTERBYPOPULARITY="filter-by-popularity";
		$FILTERBYNEW="filter-by-new";
		$formatted=[];
		$m=0;
		
		// select of all the marque
		$dm= $this->get('doctrine_mongodb')->getManager();
		$productModelCollection = $dm->getDocumentCollection('RestBundle:ProductModel')->getMongoCollection();
		$criteria = array("nameScategory" => $nom_scategory);
		$allMarquesName= $productModelCollection->distinct("marque.name", $criteria);
		$allMarquesIdLogo= $productModelCollection->distinct("marque.idLogo", $criteria);
		$formattedAllMarque=[];
		$formattedWholeSale=[];
		$formattedRetailSale=[];
		$formattedBWMSale=[];
		for($i=0; $i< sizeof($allMarquesName); $i++){
			 $formattedAllMarque[$i]=[
				'name'=>$allMarquesName[$i],
				'idLogo'=> $allMarquesIdLogo[$i]
			 ];
		}
						
		$qb= $this->get('doctrine_mongodb')
						->getManager()
						->createQueryBuilder('RestBundle:ProductModel')
						->field('nameScategory')->equals($nom_scategory)
						->field('isActivated')->equals(true);
						
		if($filter_option==$FILTERBYMOSTRESCENT){
			$qb= $qb->sort('insertionDate', 'desc');
		}
		
		if($filter_option==$FILTERBYPRICEASC){
			$qb= $qb->sort('actualPrice', 'asc');
		}
		
		if($filter_option==$FILTERBYPRICEDESC){
			$qb= $qb->sort('actualPrice', 'desc');
		}
		
		if($filter_option==$FILTERBYPOPULARITY){
			$qb= $qb->sort('popularity', 'desc');
		}
		
		
		if($marque!="null"){
			$qb= $qb->field('marque.name')->equals($marque);
		}
		
		$n=0;
		$plusPetitPrix=0;
		$plusGrandPrix=0;
		$tour=1;
		$query1 = $qb->getQuery();
		$products1=$query1->execute();
		foreach($products1 as $product){
			if($tour == 1){
				$plusPetitPrix = $product->getActualPrice();
			}
			 if($product->getActualPrice() > $plusGrandPrix ){
				$plusGrandPrix = $product->getActualPrice();
			}
			
			if($product->getActualPrice() < $plusPetitPrix ){
				$plusPetitPrix = $product->getActualPrice();
			} 
			
			$tour++;
		}
		
		
		if($min_price!="null" && $max_price!="null" ){
			$qb= $qb->field('actualPrice')->range(floatval($min_price),floatval($max_price)+1);
		}
		
		//retieving of number of product with min and max price
		$products2 = $qb->getQuery()->execute();
		foreach($products2 as $product1){
		 $n++;
		}
		
		$query = $qb->skip(40*($page-1))
					->limit(40)
                    ->getQuery();
					
		$products = $query->execute();
		
		
		
		foreach($products as $product){
		    $productCaracteristics = $product->getProductCaracteristics();
			$modelCaracteristics = $product->getModelCaracteristics();
			$details = $product->getDetails();
			$retailSale= $product->getRetailSale();
			$formattedProductCar =[];
			$formattedModelCar =[];
			$formattedDetail=[];
			$formattedWholeSale=[];
			$formattedRetailSale=[];
			$formattedBWMSale=[];
			$formattedNotation=[];
			
			$marque= $product->getMarque();
			$formattedMarque =[
			 'name'=> $marque->getName(),
			 'idLogo'=> $marque->getIdLogo()
			];
			
			$retailSale = $product->getRetailSale();
			if($retailSale!= null){
				$date= $retailSale->getEndPromotionDate();
				$formattedRetailSale=[
					'price'=> $retailSale->getPrice(),
					'isInPromotion'=>$retailSale->getIsinPromotion(),
					'promotionalPrice'=> $retailSale->getPromotionalPrice(),
					'endPromotionDate'=> $date
				];
			}
			
			$wholeSale = $product->getWholeSale();
			if($wholeSale!= null){
				$date= $wholeSale->getEndPromotionDate();
				$formattedWholeSale=[
					'price'=> $wholeSale->getPrice(),
					'isInPromotion'=>$wholeSale->getIsinPromotion(),
					'promotionalPrice'=> $wholeSale->getPromotionalPrice(),
					'lotQuantity' => $wholeSale->getLotQuantity(),
					'endPromotionDate'=> $date
				];
			}
			
			$buyWithMeSale = $product->getBuyWithMeSale();
			if($buyWithMeSale!= null){
				$formattedBWMSale=[
					'price'=> $buyWithMeSale->getPrice(),
					'lotQuantity' => $buyWithMeSale->getLotQuantity(),
					'duree'=>  $buyWithMeSale->getDuree(),
					'ispersonnalizable'=> $buyWithMeSale->getIsinPersonalizable()
				];
			}
			
			$notation = $product->getNotation();
			if($notation!= null){
				$formattedNotation=[
					'level1'=> $notation->getLevel1(),
					'level2' => $notation->getLevel2(),
					'level3'=>  $notation->getLevel3(),
					'level4'=> $notation->getLevel4(),
					'level5'=> $notation->getLevel5(),
					'note'=> ($notation->getLevel1()*1 + $notation->getLevel2()*2 + $notation->getLevel3()*3 + $notation->getLevel4()*4 + $notation->getLevel5()*5)/
					($notation->getLevel1() + $notation->getLevel2() + $notation->getLevel3() + $notation->getLevel4()+ $notation->getLevel5()),
					'total'=> $notation->getLevel1() + $notation->getLevel2() + $notation->getLevel3() + $notation->getLevel4()+ $notation->getLevel5()
				];
			}
			else{
				$formattedNotation=[
					'level1'=> 0,
					'level2' => 0,
					'level3'=>  0,
					'level4'=> 0,
					'level5'=> 0,
					'note'=> 0,
					'total'=>0,
				];
			}
			
			foreach($productCaracteristics as $productCaracteristic ){
				$formattedProductCar[] =[
				'id' => $productCaracteristic->getId(),
				'name' => $productCaracteristic->getName(),
				'unity' => $productCaracteristic->getUnity(),
				'value' => $productCaracteristic->getValue()
				];
			}

			foreach($modelCaracteristics as $modelCaracteristic){
				$formattedModelCar[] =[
				'id' => $modelCaracteristic->getId(),
				'name' => $modelCaracteristic->getName(),
				'unity' => $modelCaracteristic->getUnity(),
				'value' => $modelCaracteristic->getValue()
				];
			}
			
			foreach($details as $detail){
				$formattedDetail[] =[
				'name' => $detail->getName(),
				'value' => $detail->getValue()
				];
			}
			 
			 
		     $formatted[] =[
			 'id'=>	$product->getId(),
             'name' => $product->getName(),
			 'productName'=>$product->getIdProduit(),
			 'description' => $product->getDescription(),
			 'quantity'=> $product->getQuantity(),
			 'taille'=> $product->getTaille(),
			 'nameCategory' => $product->getNameCategory(),
			 'idScategory' => $product->getNameScategory(),
			 'idSScategory' => $product->getNameSScategory(),
			 'idImage'=> $product->getIdImage(),
			 'isVirtual' => $product->getIsVirtual(),
			 'idBigImage1'=> $product->getIdBigImage1(),
			 'idBigImage2'=> $product->getIdBigImage2(),
			 'idBigImage3'=> $product->getIdBigImage3(),
			 'idBigImage4'=> $product->getIdBigImage4(),
			 'detail' => $formattedDetail,
			 'marque'=> $formattedMarque,
			 'productCar' => $formattedProductCar,
			 'modelCar' =>	$formattedModelCar,
			 'retailSale' => $formattedRetailSale,
			 'wholeSale'=> $formattedWholeSale,
			 'BWMSale'=> $formattedBWMSale,
			 'notation'=> $formattedNotation
           ]; 
		}
		
        $data=[
		'size'=> $n,
		'plusGrandPrix' => $plusGrandPrix,
		'plusPetitPrix' => $plusPetitPrix,
		'marque' => $formattedAllMarque,
		'products'=> $formatted
		];
		
		 $response = [
        'status' => '200',
		'data' => $data
		];
	   header('Access-Control-Allow-Origin: *');
	   return new JsonResponse($response);
	 }
	 
	/**********************************************************************************/	 
    /************************ get by sscategory****************************************/	
    /**
     * @GET("/product/model/sscategory/{nom_sscategory}/{page}/{filter_option}/{min_price}/{max_price}/{marque}")
	 * url-parm: id_category:id de la categorie, sous categorie et sous sous categorie
	 * return of a product of a category oder by most recently added
     */
	 public function getProductBySSCategory1Action($nom_sscategory,$page,$filter_option,$min_price,$max_price,$marque,Request $request)
     {  
		$FILTERBYMOSTRESCENT="filter-by-most-rescent";
		$FILTERBYPRICEASC="filter-by-price-asc";
		$FILTERBYPRICEDESC="filter-by-price-desc";
		$FILTERBYPOPULARITY="filter-by-popularity";
		$FILTERBYNEW="filter-by-new";
		$formatted=[];
		$m=0;
		
		// select of all the marque
		$dm= $this->get('doctrine_mongodb')->getManager();
		$productModelCollection = $dm->getDocumentCollection('RestBundle:ProductModel')->getMongoCollection();
		$criteria = array("nameSScategory" => $nom_sscategory);
		$allMarquesName= $productModelCollection->distinct("marque.name", $criteria);
		$allMarquesIdLogo= $productModelCollection->distinct("marque.idLogo", $criteria);
		$formattedAllMarque=[];
		$formattedWholeSale=[];
		$formattedRetailSale=[];
		$formattedBWMSale=[];
		for($i=0; $i< sizeof($allMarquesName); $i++){
			 $formattedAllMarque[$i]=[
				'name'=>$allMarquesName[$i],
				'idLogo'=> $allMarquesIdLogo[$i]
			 ];
		}
						
		$qb= $this->get('doctrine_mongodb')
						->getManager()
						->createQueryBuilder('RestBundle:ProductModel')
						->field('nameSScategory')->equals($nom_sscategory)
						->field('isActivated')->equals(true);
						
		if($filter_option==$FILTERBYMOSTRESCENT){
			$qb= $qb->sort('insertionDate', 'desc');
		}
		
		if($filter_option==$FILTERBYPRICEASC){
			$qb= $qb->sort('actualPrice', 'asc');
		}
		
		if($filter_option==$FILTERBYPRICEDESC){
			$qb= $qb->sort('actualPrice', 'desc');
		}
		
		if($filter_option==$FILTERBYPOPULARITY){
			$qb= $qb->sort('popularity', 'desc');
		}
		
		
		if($marque!="null"){
			$qb= $qb->field('marque.name')->equals($marque);
		}
		
		$n=0;
		$plusPetitPrix=0;
		$plusGrandPrix=0;
		$tour=1;
		$query1 = $qb->getQuery();
		$products1=$query1->execute();
		foreach($products1 as $product){
			if($tour == 1){
				$plusPetitPrix = $product->getActualPrice();
			}
			 if($product->getActualPrice() > $plusGrandPrix ){
				$plusGrandPrix = $product->getActualPrice();
			}
			
			if($product->getActualPrice() < $plusPetitPrix ){
				$plusPetitPrix = $product->getActualPrice();
			} 
			
			$tour++;
		}
		
		
		if($min_price!="null" && $max_price!="null" ){
			$qb= $qb->field('actualPrice')->range(floatval($min_price),floatval($max_price)+1);
		}
		
		//retieving of number of product with min and max price
		$products2 = $qb->getQuery()->execute();
		foreach($products2 as $product1){
		 $n++;
		}
		
		$query = $qb->skip(40*($page-1))
					->limit(40)
                    ->getQuery();
					
		$products = $query->execute();
		
		
		
		foreach($products as $product){
		    $productCaracteristics = $product->getProductCaracteristics();
			$modelCaracteristics = $product->getModelCaracteristics();
			$details = $product->getDetails();
			$retailSale= $product->getRetailSale();
			$formattedProductCar =[];
			$formattedModelCar =[];
			$formattedDetail=[];
			$formattedWholeSale=[];
			$formattedRetailSale=[];
			$formattedBWMSale=[];
			$formattedNotation=[];
			
			$marque= $product->getMarque();
			$formattedMarque =[
			 'name'=> $marque->getName(),
			 'idLogo'=> $marque->getIdLogo()
			];
			
			$retailSale = $product->getRetailSale();
			if($retailSale!= null){
				$date= $retailSale->getEndPromotionDate();
				$formattedRetailSale=[
					'price'=> $retailSale->getPrice(),
					'isInPromotion'=>$retailSale->getIsinPromotion(),
					'promotionalPrice'=> $retailSale->getPromotionalPrice(),
					'endPromotionDate'=> $date
				];
			}
			
			$wholeSale = $product->getWholeSale();
			if($wholeSale!= null){
				$date= $wholeSale->getEndPromotionDate();
				$formattedWholeSale=[
					'price'=> $wholeSale->getPrice(),
					'isInPromotion'=>$wholeSale->getIsinPromotion(),
					'promotionalPrice'=> $wholeSale->getPromotionalPrice(),
					'lotQuantity' => $wholeSale->getLotQuantity(),
					'endPromotionDate'=> $date
				];
			}
			
			$buyWithMeSale = $product->getBuyWithMeSale();
			if($buyWithMeSale!= null){
				$formattedBWMSale=[
					'price'=> $buyWithMeSale->getPrice(),
					'lotQuantity' => $buyWithMeSale->getLotQuantity(),
					'duree'=>  $buyWithMeSale->getDuree(),
					'ispersonnalizable'=> $buyWithMeSale->getIsinPersonalizable()
				];
			}
			
			$notation = $product->getNotation();
			if($notation!= null){
				$formattedNotation=[
					'level1'=> $notation->getLevel1(),
					'level2' => $notation->getLevel2(),
					'level3'=>  $notation->getLevel3(),
					'level4'=> $notation->getLevel4(),
					'level5'=> $notation->getLevel5(),
					'note'=> ($notation->getLevel1()*1 + $notation->getLevel2()*2 + $notation->getLevel3()*3 + $notation->getLevel4()*4 + $notation->getLevel5()*5)/
					($notation->getLevel1() + $notation->getLevel2() + $notation->getLevel3() + $notation->getLevel4()+ $notation->getLevel5()),
					'total'=> $notation->getLevel1() + $notation->getLevel2() + $notation->getLevel3() + $notation->getLevel4()+ $notation->getLevel5()
				];
			}
			else{
				$formattedNotation=[
					'level1'=> 0,
					'level2' => 0,
					'level3'=>  0,
					'level4'=> 0,
					'level5'=> 0,
					'note'=> 0,
					'total'=>0,
				];
			}
			
			foreach($productCaracteristics as $productCaracteristic ){
				$formattedProductCar[] =[
				'id' => $productCaracteristic->getId(),
				'name' => $productCaracteristic->getName(),
				'unity' => $productCaracteristic->getUnity(),
				'value' => $productCaracteristic->getValue()
				];
			}

			foreach($modelCaracteristics as $modelCaracteristic){
				$formattedModelCar[] =[
				'id' => $modelCaracteristic->getId(),
				'name' => $modelCaracteristic->getName(),
				'unity' => $modelCaracteristic->getUnity(),
				'value' => $modelCaracteristic->getValue()
				];
			}
			
			foreach($details as $detail){
				$formattedDetail[] =[
				'name' => $detail->getName(),
				'value' => $detail->getValue()
				];
			}
			 
			 
		     $formatted[] =[
			 'id'=>	$product->getId(),
             'name' => $product->getName(),
			 'productName'=>$product->getIdProduit(),
			 'description' => $product->getDescription(),
			 'quantity'=> $product->getQuantity(),
			 'taille'=> $product->getTaille(),
			 'nameCategory' => $product->getNameCategory(),
			 'idScategory' => $product->getNameScategory(),
			 'idSScategory' => $product->getNameSScategory(),
			 'idImage'=> $product->getIdImage(),
			 'idBigImage1'=> $product->getIdBigImage1(),
			 'idBigImage2'=> $product->getIdBigImage2(),
			 'idBigImage3'=> $product->getIdBigImage3(),
			 'idBigImage4'=> $product->getIdBigImage4(),
			 'isVirtual' => $product->getIsVirtual(),
			 'detail' => $formattedDetail,
			 'marque'=> $formattedMarque,
			 'productCar' => $formattedProductCar,
			 'modelCar' =>	$formattedModelCar,
			 'retailSale' => $formattedRetailSale,
			 'wholeSale'=> $formattedWholeSale,
			 'BWMSale'=> $formattedBWMSale,
			 'notation'=> $formattedNotation
           ]; 
		}
		
        $data=[
		'size'=> $n,
		'plusGrandPrix' => $plusGrandPrix,
		'plusPetitPrix' => $plusPetitPrix,
		'marque' => $formattedAllMarque,
		'products'=> $formatted
		];
		
		 $response = [
        'status' => '200',
		'data' => $data
		];
	   header('Access-Control-Allow-Origin: *');
	   return new JsonResponse($response);
	} 
	
	/************************************************************************************************//******************************* get by marque**************************************************/	
    /**
     * @GET("/product/model/marque/{nom_marque}/{page}/{filter_option}/{min_price}/{max_price}")
	 * url-parm: id_category:id de la categorie, sous categorie et sous sous categorie
	 * return of a product of a category oder by most recently added
     */
	 public function getProductByMarqueAction($nom_marque,$page,$filter_option,$min_price,$max_price,Request $request)
     {  
		$FILTERBYMOSTRESCENT="filter-by-most-rescent";
		$FILTERBYPRICEASC="filter-by-price-asc";
		$FILTERBYPRICEDESC="filter-by-price-desc";
		$FILTERBYPOPULARITY="filter-by-popularity";
		$FILTERBYNEW="filter-by-new";
		$formatted=[];
		$m=0;
		
		$dm= $this->get('doctrine_mongodb')->getManager();
		$repository= $dm->getRepository('RestBundle:Marque');
		
		// retreiving the product of the mark
		$marque= $repository->findOneByName($request->get('nom_marque'));
		$formattedWholeSale=[];
		$formattedRetailSale=[];
		$formattedBWMSale=[];			
		$qb= $this->get('doctrine_mongodb')
						->getManager()
						->createQueryBuilder('RestBundle:ProductModel')
						->field('marque.name')->equals($nom_marque)
						->field('isActivated')->equals(true);
						
		if($filter_option==$FILTERBYMOSTRESCENT){
			$qb= $qb->sort('insertionDate', 'desc');
		}
		
		if($filter_option==$FILTERBYPRICEASC){
			$qb= $qb->sort('actualPrice', 'asc');
		}
		
		if($filter_option==$FILTERBYPRICEDESC){
			$qb= $qb->sort('actualPrice', 'desc');
		}

		if($filter_option==$FILTERBYPOPULARITY){
			$qb= $qb->sort('popularity', 'desc');
		}
		
		
		$n=0;
		$plusPetitPrix=0;
		$plusGrandPrix=0;
		$tour=1;
		$query1 = $qb->getQuery();
		$products1=$query1->execute();
		foreach($products1 as $product){
		    
			if($tour == 1){
				$plusPetitPrix = $product->getActualPrice();
			}
			 if($product->getActualPrice() > $plusGrandPrix ){
				$plusGrandPrix = $product->getActualPrice();
			}
			
			if($product->getActualPrice() < $plusPetitPrix ){
				$plusPetitPrix = $product->getActualPrice();
			} 
			
			$tour++;
		}
		
		
		if($min_price!="null" && $max_price!="null" ){
			$qb= $qb->field('actualPrice')->range(floatval($min_price),floatval($max_price)+1);
		}
		
		//retieving of number of product with min and max price
		$products2 = $qb->getQuery()->execute();
		foreach($products2 as $product1){
		 $n++;
		}
		
		$query = $qb->skip(40*($page-1))
					->limit(40)
                    ->getQuery();
					
		$products = $query->execute();
		
		foreach($products as $product){
		    
			
		    $productCaracteristics = $product->getProductCaracteristics();
			$modelCaracteristics = $product->getModelCaracteristics();
			$details = $product->getDetails();
			$retailSale= $product->getRetailSale();
			$formattedProductCar =[];
			$formattedModelCar =[];
			$formattedDetail=[];
			
			$marque= $product->getMarque();
			$formattedMarque =[
			 'name'=> $marque->getName()
			];
			
			$retailSale = $product->getRetailSale();
			if($retailSale!= null){
				$date= $retailSale->getEndPromotionDate();
				$formattedRetailSale=[
					'price'=> $retailSale->getPrice(),
					'isInPromotion'=>$retailSale->getIsinPromotion(),
					'promotionalPrice'=> $retailSale->getPromotionalPrice(),
					'endPromotionDate'=> $date
				];
			}
			
			$wholeSale = $product->getWholeSale();
			if($wholeSale!= null){
				$date= $wholeSale->getEndPromotionDate();
				$formattedWholeSale=[
					'price'=> $wholeSale->getPrice(),
					'isInPromotion'=>$wholeSale->getIsinPromotion(),
					'promotionalPrice'=> $wholeSale->getPromotionalPrice(),
					'lotQuantity' => $wholeSale->getLotQuantity(),
					'endPromotionDate'=> $date
				];
			}
			
			$buyWithMeSale = $product->getBuyWithMeSale();
			if($buyWithMeSale!= null){
				$formattedBWMSale=[
					'price'=> $buyWithMeSale->getPrice(),
					'lotQuantity' => $buyWithMeSale->getLotQuantity(),
					'duree'=>  $buyWithMeSale->getDuree(),
					'ispersonnalizable'=> $buyWithMeSale->getIsinPersonalizable()
				];
			}
			
			$notation = $product->getNotation();
			if($notation!= null){
				$formattedNotation=[
					'level1'=> $notation->getLevel1(),
					'level2' => $notation->getLevel2(),
					'level3'=>  $notation->getLevel3(),
					'level4'=> $notation->getLevel4(),
					'level5'=> $notation->getLevel5(),
					'note'=> ($notation->getLevel1()*1 + $notation->getLevel2()*2 + $notation->getLevel3()*3 + $notation->getLevel4()*4 + $notation->getLevel5()*5)/
					($notation->getLevel1() + $notation->getLevel2() + $notation->getLevel3() + $notation->getLevel4()+ $notation->getLevel5()),
					'total'=> $notation->getLevel1() + $notation->getLevel2() + $notation->getLevel3() + $notation->getLevel4()+ $notation->getLevel5()
				];
			}
			else{
				$formattedNotation=[
					'level1'=> 0,
					'level2' => 0,
					'level3'=>  0,
					'level4'=> 0,
					'level5'=> 0,
					'note'=> 0,
					'total'=>0,
				];
			}
			
			foreach($productCaracteristics as $productCaracteristic ){
				$formattedProductCar[] =[
				'id' => $productCaracteristic->getId(),
				'name' => $productCaracteristic->getName(),
				'unity' => $productCaracteristic->getUnity(),
				'value' => $productCaracteristic->getValue()
				];
			}

			foreach($modelCaracteristics as $modelCaracteristic){
				$formattedModelCar[] =[
				'id' => $modelCaracteristic->getId(),
				'name' => $modelCaracteristic->getName(),
				'unity' => $modelCaracteristic->getUnity(),
				'value' => $modelCaracteristic->getValue()
				];
			}
			
			foreach($details as $detail){
				$formattedDetail[] =[
				'name' => $detail->getName(),
				'value' => $detail->getValue()
				];
			}
			 
			 
		     $formatted[] =[
			 'id'=>	$product->getId(),
             'name' => $product->getName(),
			 'productName'=>$product->getIdProduit(),
			 'description' => $product->getDescription(),
			 'quantity'=> $product->getQuantity(),
			 'taille'=> $product->getTaille(),
			 'idCategory' => $product->getNameCategory(),
			 'idScategory' => $product->getNameScategory(),
			 'idSScategory' => $product->getNameSScategory(),
			 'idImage'=> $product->getIdImage(),
			 'idBigImage1'=> $product->getIdBigImage1(),
			 'idBigImage2'=> $product->getIdBigImage2(),
			 'idBigImage3'=> $product->getIdBigImage3(),
			 'idBigImage4'=> $product->getIdBigImage4(),
			 'isVirtual' => $product->getIsVirtual(),
			 'detail' => $formattedDetail,
			 'marque'=> $formattedMarque,
			 'productCar' => $formattedProductCar,
			 'modelCar' =>	$formattedModelCar,
			 'retailSale' => $formattedRetailSale,
			 'wholeSale' => $formattedWholeSale,
			 'BWMSale' => $formattedBWMSale,
			 'notation'=> $formattedNotation
           ]; 
		}
		
        $data=[
		'marque'=>$marque->getName(),
		'size'=> $n,
		'plusGrandPrix' => $plusGrandPrix,
		'plusPetitPrix' => $plusPetitPrix,
		'products'=> $formatted
		];
		
		 $response = [
        'status' => '200',
		'data' => $data
		];
	   header('Access-Control-Allow-Origin: *');
	   return new JsonResponse($response);
	} 

	/***************************************************************************************/	 
    /*********************************** get by seller**************************************/	
    /**
     * @GET("/product/model/seller/{nom_seller}/{page}/{filter_option}/{min_price}/{max_price}/{marque}")
	 * url-parm: id_category:id de la categorie, sous categorie et sous sous categorie
	 * return of a product of a category oder by most recently added
     */
	 public function getProductBySellerAction
	 ($nom_seller,$page,$filter_option,$min_price,$max_price,$marque,Request $request)

     {  
		$FILTERBYMOSTRESCENT="filter-by-most-rescent";
		$FILTERBYPRICEASC="filter-by-price-asc";
		$FILTERBYPRICEDESC="filter-by-price-desc";
		$FILTERBYPOPULARITY="filter-by-popularity";
		$FILTERBYNEW="filter-by-new";
		$formatted=[];
		$m=0;

		
		
		$dm= $this->get('doctrine_mongodb')->getManager();
		$repository= $dm->getRepository('RestBundle:Seller');
		$seller= $repository->findOneByName($request->get('nom_seller'));

		// select of all the marque
		$dm= $this->get('doctrine_mongodb')->getManager();
		$productModelCollection = $dm->getDocumentCollection('RestBundle:ProductModel')->getMongoCollection();
		$criteria = array("idSeller" => $seller->getId());
		$allMarquesName= $productModelCollection->distinct("marque.name", $criteria);
		$allMarquesIdLogo = $productModelCollection->distinct("marque.idLogo", $criteria);
		$allCategoriesName = $productModelCollection->distinct("nameCategory", $criteria);
		$formattedAllMarque=[];
		$formattedWholeSale=[];
		$formattedRetailSale=[];
		$formattedBWMSale=[];
		$formattedCategories=[];

		for($i=0; $i< sizeof($allMarquesName); $i++){
			 $formattedAllMarque[$i]=[
				'name'=>$allMarquesName[$i],
				'idLogo'=> $allMarquesIdLogo[$i]
			 ];
		}

		for($i=0; $i< sizeof($allCategoriesName); $i++){
			$formattedCategories[$i]=[
				'name'=>$allCategoriesName[$i]
				
			];
		}
		
		
		$formattedWholeSale=[];
		$formattedRetailSale=[];
		$formattedBWMSale=[];			
		$qb= $this->get('doctrine_mongodb')
						->getManager()
						->createQueryBuilder('RestBundle:ProductModel')
						->field('idSeller')->equals($seller->getId())
						->field('isActivated')->equals(true);
						
		if($filter_option==$FILTERBYMOSTRESCENT){
			$qb= $qb->sort('insertionDate', 'desc');
		}
		
		if($filter_option==$FILTERBYPRICEASC){
			$qb= $qb->sort('actualPrice', 'asc');
		}
		
		if($filter_option==$FILTERBYPRICEDESC){
			$qb= $qb->sort('actualPrice', 'desc');
		}

		if($filter_option==$FILTERBYPOPULARITY){
			$qb= $qb->sort('popularity', 'desc');
		}
		
		
		$n=0;
		$plusPetitPrix=0;
		$plusGrandPrix=0;
		$tour=1;
		$query1 = $qb->getQuery();
		$products1=$query1->execute();
		foreach($products1 as $product){
		    
			if($tour == 1){
				$plusPetitPrix = $product->getActualPrice();
			}
			 if($product->getActualPrice() > $plusGrandPrix ){
				$plusGrandPrix = $product->getActualPrice();
			}
			
			if($product->getActualPrice() < $plusPetitPrix ){
				$plusPetitPrix = $product->getActualPrice();
			} 
			
			$tour++;
		}
		
		if($marque!="null"){
			$qb= $qb->field('marque.name')->equals($marque);
		}

		if($min_price!="null" && $max_price!="null" ){
			$qb= $qb->field('actualPrice')->range(floatval($min_price),floatval($max_price)+1);
		}
		
		//retieving of number of product with min and max price
		$products2 = $qb->getQuery()->execute();
		foreach($products2 as $product1){
		 $n++;
		}
		
		$query = $qb->skip(40*($page-1))
					->limit(40)
                    ->getQuery();
					
		$products = $query->execute();
		
		foreach($products as $product){
		    
			
		    $productCaracteristics = $product->getProductCaracteristics();
			$modelCaracteristics = $product->getModelCaracteristics();
			$details = $product->getDetails();
			$retailSale= $product->getRetailSale();
			$formattedProductCar =[];
			$formattedModelCar =[];
			$formattedDetail=[];
			
			$marque= $product->getMarque();
			$formattedMarque =[
			 'name'=> $marque->getName()
			];
			
			$retailSale = $product->getRetailSale();
			if($retailSale!= null){
				$date= $retailSale->getEndPromotionDate();
				$formattedRetailSale=[
					'price'=> $retailSale->getPrice(),
					'isInPromotion'=>$retailSale->getIsinPromotion(),
					'promotionalPrice'=> $retailSale->getPromotionalPrice(),
					'endPromotionDate'=> $date
				];
			}
			
			$wholeSale = $product->getWholeSale();
			if($wholeSale!= null){
				$date= $wholeSale->getEndPromotionDate();
				$formattedWholeSale=[
					'price'=> $wholeSale->getPrice(),
					'isInPromotion'=>$wholeSale->getIsinPromotion(),
					'promotionalPrice'=> $wholeSale->getPromotionalPrice(),
					'lotQuantity' => $wholeSale->getLotQuantity(),
					'endPromotionDate'=> $date
				];
			}
			
			$buyWithMeSale = $product->getBuyWithMeSale();
			if($buyWithMeSale!= null){
				$formattedBWMSale=[
					'price'=> $buyWithMeSale->getPrice(),
					'lotQuantity' => $buyWithMeSale->getLotQuantity(),
					'duree'=>  $buyWithMeSale->getDuree(),
					'ispersonnalizable'=> $buyWithMeSale->getIsinPersonalizable()
				];
			}
			
			$notation = $product->getNotation();
			if($notation!= null){
				$formattedNotation=[
					'level1'=> $notation->getLevel1(),
					'level2' => $notation->getLevel2(),
					'level3'=>  $notation->getLevel3(),
					'level4'=> $notation->getLevel4(),
					'level5'=> $notation->getLevel5(),
					'note'=> ($notation->getLevel1()*1 + $notation->getLevel2()*2 + $notation->getLevel3()*3 + $notation->getLevel4()*4 + $notation->getLevel5()*5)/
					($notation->getLevel1() + $notation->getLevel2() + $notation->getLevel3() + $notation->getLevel4()+ $notation->getLevel5()),
					'total'=> $notation->getLevel1() + $notation->getLevel2() + $notation->getLevel3() + $notation->getLevel4()+ $notation->getLevel5()
				];
			}
			else{
				$formattedNotation=[
					'level1'=> 0,
					'level2' => 0,
					'level3'=>  0,
					'level4'=> 0,
					'level5'=> 0,
					'note'=> 0,
					'total'=>0,
				];
			}
			
			foreach($productCaracteristics as $productCaracteristic ){
				$formattedProductCar[] =[
				'id' => $productCaracteristic->getId(),
				'name' => $productCaracteristic->getName(),
				'unity' => $productCaracteristic->getUnity(),
				'value' => $productCaracteristic->getValue()
				];
			}

			foreach($modelCaracteristics as $modelCaracteristic){
				$formattedModelCar[] =[
				'id' => $modelCaracteristic->getId(),
				'name' => $modelCaracteristic->getName(),
				'unity' => $modelCaracteristic->getUnity(),
				'value' => $modelCaracteristic->getValue()
				];
			}
			
			foreach($details as $detail){
				$formattedDetail[] =[
				'name' => $detail->getName(),
				'value' => $detail->getValue()
				];
			}
			 
			 
		     $formatted[] =[
			 'id'=>	$product->getId(),
             'name' => $product->getName(),
			 'productName'=>$product->getIdProduit(),
			 'description' => $product->getDescription(),
			 'quantity'=> $product->getQuantity(),
			 'taille'=> $product->getTaille(),
			 'idCategory' => $product->getNameCategory(),
			 'idScategory' => $product->getNameScategory(),
			 'idSScategory' => $product->getNameSScategory(),
			 'idImage'=> $product->getIdImage(),
			 'idBigImage1'=> $product->getIdBigImage1(),
			 'idBigImage2'=> $product->getIdBigImage2(),
			 'idBigImage3'=> $product->getIdBigImage3(),
			 'idBigImage4'=> $product->getIdBigImage4(),
			 'isVirtual' => $product->getIsVirtual(),
			 'detail' => $formattedDetail,
			 'marque'=> $formattedMarque,
			 'productCar' => $formattedProductCar,
			 'modelCar' =>	$formattedModelCar,
			 'retailSale' => $formattedRetailSale,
			 'wholeSale' => $formattedWholeSale,
			 'BWMSale' => $formattedBWMSale,
			 'notation'=> $formattedNotation
           ]; 
		}
		
        $data=[
		'size'=> $n,
		'plusGrandPrix' => $plusGrandPrix,
		'plusPetitPrix' => $plusPetitPrix,
		'categories'=> $formattedCategories,
		'marques'=> $formattedAllMarque,
		'products'=> $formatted
		];
		
		 $response = [
        'status' => '200',
		'data' => $data
		];
	   header('Access-Control-Allow-Origin: *');
	   return new JsonResponse($response);
	}
	
	/**
     * @GET("/product/models/{name}")
	 * url-parm: id(String) 
	 * return of the model corresponding to the product name
     */
	 public function getOneProductModelAction($name,Request $request)
     { 
		$dm= $this->get('doctrine_mongodb')->getManager();
	    $repository = $dm->getRepository('RestBundle:ProductModel');
	    $products = $repository->findBy(array('idProduit' => $name));
	    
	    if(empty($products)){
			$formatted = [
               'statut' => '404'
            ];
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
	   } 
	   
	  // var_dump($product);
	    foreach($products as $product){
			$productCaracteristics = $product->getProductCaracteristics();
			$modelCaracteristics = $product->getModelCaracteristics();
			$details = $product->getDetails();
			$retailSale= $product->getRetailSale();
			$formattedProductCar =[];
			$formattedModelCar =[];
			$formattedDetail=[];
			$formattedBoutique=[];
			$marque= $product->getMarque();

			
	  		$repository1 = $dm->getRepository('RestBundle:Seller');
	  		if(!empty($product->getIdSeller())){
		  		$boutique1 = $repository1->findOneById($product->getIdSeller());
		  		if(!empty($boutique1)){
		  				 $formattedBoutique = [
					'id' => $boutique1->getId(),
					'name'=> $boutique1->getName(),
					'tel1'=> $boutique1->getTel1(),
					'tel2'=> $boutique1->getTel2(),
					'adresse'=> $boutique1->getAdresse(),
						];
		  		}
		  	}
	  		
           
			$formattedMarque =[
				'name'=> $marque->getName(),
				'idLogo'=> $marque->getIdLogo()
			];
			
			$retailSale = $product->getRetailSale();
			$formattedRetailSale=[];
			if($retailSale!= null){
				$date= $retailSale->getEndPromotionDate();
				$formattedRetailSale=[
					'price'=> $retailSale->getPrice(),
					'isInPromotion'=>$retailSale->getIsinPromotion(),
					'promotionalPrice'=> $retailSale->getPromotionalPrice(),
					'endPromotionDate'=> $date
				];
			}
			
			$wholeSale = $product->getWholeSale();
			$formattedWholeSale=[];
			if($wholeSale!= null){
				$date= $wholeSale->getEndPromotionDate();
				$formattedWholeSale=[
					'price'=> $wholeSale->getPrice(),
					'isInPromotion'=>$wholeSale->getIsinPromotion(),
					'promotionalPrice'=> $wholeSale->getPromotionalPrice(),
					'lotQuantity' => $wholeSale->getLotQuantity(),
					'endPromotionDate'=> $date
				];
			}
			
			$buyWithMeSale = $product->getBuyWithMeSale();
			$formattedBWMSale=[];
			if($buyWithMeSale!= null){
				$formattedBWMSale=[
					'price'=> $buyWithMeSale->getPrice(),
					'lotQuantity' => $buyWithMeSale->getLotQuantity(),
					'duree'=>  $buyWithMeSale->getDuree(),
					'ispersonnalizable'=> $buyWithMeSale->getIsinPersonalizable()
				];
			}
			
			foreach($productCaracteristics as $productCaracteristic ){
				$formattedProductCar[] =[
					'id' => $productCaracteristic->getId(),
					'name' => $productCaracteristic->getName(),
					'unity' => $productCaracteristic->getUnity(),
					'value' => $productCaracteristic->getValue()
				];
			}

			foreach($modelCaracteristics as $modelCaracteristic){
				$formattedModelCar[] =[
				'id' => $modelCaracteristic->getId(),
				'name' => $modelCaracteristic->getName(),
				'unity' => $modelCaracteristic->getUnity(),
				'value' => $modelCaracteristic->getValue()
				];
			}
			
			foreach($details as $detail){
				$formattedDetail[] =[
				'name' => $detail->getName(),
				'value' => $detail->getValue()
				];
			}
			 
			 
			$formatted[]=[
			 'id'=>	$product->getId(),
             'name' => $product->getName(),
			 'productName'=>$product->getIdProduit(),
			 'description' => $product->getDescription(),
			 'quantity'=> $product->getQuantity(),
			 'taille'=> $product->getTaille(),
			 'idCategory' => $product->getNameCategory(),
			 'idScategory' => $product->getNameScategory(),
			 'idSScategory' => $product->getNameSScategory(),
			 'boutique'=>$formattedBoutique,
			 'idImage'=> $product->getIdImage(),
			 'idBigImage1'=> $product->getIdBigImage1(),
			 'idBigImage2'=> $product->getIdBigImage2(),
			 'idBigImage3'=> $product->getIdBigImage3(),
			 'idBigImage4'=> $product->getIdBigImage4(),
			 'popularity'=> $product->getPopularity(),
			 'weight' => $product->getWeight(),
			 'isActivated'=> $product->getIsActivated(),
			 'isVirtual' => $product->getIsVirtual(),
			 'detail' => $formattedDetail,
			 'marque'=> $formattedMarque,
			 'productCar' => $formattedProductCar,
			 'modelCar' =>	$formattedModelCar,
			 'retailSale' => $formattedRetailSale,
			 'wholeSale'=> $formattedWholeSale,
			 'BWMSale'=>$formattedBWMSale
			]; 
		}
		
		$data=[
			'product'=> $formatted
		];
		
		 $response = [
        	'statut' => '200',
			'data' => $data
		];
	   header('Access-Control-Allow-Origin: *');
	   return new JsonResponse($response);
	}
	
	/**
     * @GET("/product/model/{name}")
	 *  
	 * return of the model corresponding to the name
     */
	 public function getProductModelByNameAction($name,Request $request)
     { 
		$dm= $this->get('doctrine_mongodb')->getManager();
	    $repository = $dm->getRepository('RestBundle:ProductModel');
	    $repository2 = $dm->getRepository('RestBundle:Seller');

	    $model= $repository->findOneBy(array('name' => $name));
	    if (!empty($model->getIdSeller())){
	    	 $boutique = $repository2->findOneById($model->getIdSeller());
	    }
	  
	     if(empty($model)){
			$formatted = [
               'statut' => '404'
            ];
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
	   } 
	   
	   $productCaracteristics = $model->getProductCaracteristics();
			$modelCaracteristics = $model->getModelCaracteristics();
			$details = $model->getDetails();
			$retailSale= $model->getRetailSale();
			$formattedProductCar =[];
			$formattedModelCar =[];
			$formattedDetail=[];
			$formattedWholeSale=[];
			$formattedRetailSale=[];
			$formattedBWMSale=[];
			$formattedNotation=[];
			$formattedBoutique=[];
			
			$marque= $model->getMarque();
			$formattedMarque =[
			 'name'=> $marque->getName(),
			 'idLogo'=> $marque->getIdLogo()
			];
			
			$retailSale = $model->getRetailSale();
			if($retailSale!= null){
				$date= $retailSale->getEndPromotionDate();
				$formattedRetailSale=[
					'price'=> $retailSale->getPrice(),
					'isInPromotion'=>$retailSale->getIsinPromotion(),
					'promotionalPrice'=> $retailSale->getPromotionalPrice(),
					'endPromotionDate'=> $date
				];
			}
			
			$wholeSale = $model->getWholeSale();
			if($wholeSale!= null){
				$date= $wholeSale->getEndPromotionDate();
				$formattedWholeSale=[
					'price'=> $wholeSale->getPrice(),
					'isInPromotion'=>$wholeSale->getIsinPromotion(),
					'promotionalPrice'=> $wholeSale->getPromotionalPrice(),
					'lotQuantity' => $wholeSale->getLotQuantity(),
					'endPromotionDate'=> $date
				];
			}
			
			$buyWithMeSale = $model->getBuyWithMeSale();
			$formattedBWMSale=[];
			if($buyWithMeSale!= null){
				$formattedBWMSale=[
					'price'=> $buyWithMeSale->getPrice(),
					'lotQuantity' => $buyWithMeSale->getLotQuantity(),
					'duree'=>  $buyWithMeSale->getDuree(),
					'ispersonnalizable'=> $buyWithMeSale->getIsinPersonalizable()
				];
			}
			
			foreach($productCaracteristics as $productCaracteristic ){
				$formattedProductCar[] =[
				'id' => $productCaracteristic->getId(),
				'name' => $productCaracteristic->getName(),
				'unity' => $productCaracteristic->getUnity(),
				'value' => $productCaracteristic->getValue()
				];
			}

			foreach($modelCaracteristics as $modelCaracteristic){
				$formattedModelCar[] =[
				'id' => $modelCaracteristic->getId(),
				'name' => $modelCaracteristic->getName(),
				'unity' => $modelCaracteristic->getUnity(),
				'value' => $modelCaracteristic->getValue()
				];
			}
			
			foreach($details as $detail){
				$formattedDetail[] =[
				'name' => $detail->getName(),
				'value' => $detail->getValue()
				];
			}
			
			$notation = $model->getNotation();
			if($notation!= null){
				$formattedNotation=[
					'level1'=> $notation->getLevel1(),
					'level2' => $notation->getLevel2(),
					'level3'=>  $notation->getLevel3(),
					'level4'=> $notation->getLevel4(),
					'level5'=> $notation->getLevel5(),
					'note'=> ($notation->getLevel1()*1 + $notation->getLevel2()*2 + $notation->getLevel3()*3 + $notation->getLevel4()*4 + $notation->getLevel5()*5)/
					($notation->getLevel1() + $notation->getLevel2() + $notation->getLevel3() + $notation->getLevel4()+ $notation->getLevel5()),
					'total'=> $notation->getLevel1() + $notation->getLevel2() + $notation->getLevel3() + $notation->getLevel4()+ $notation->getLevel5()
				];
			}
			else{
				$formattedNotation=[
					'level1'=> 0,
					'level2' => 0,
					'level3'=>  0,
					'level4'=> 0,
					'level5'=> 0,
					'note'=> 0,
					'total'=>0,
				];
			}
			 
			if(!empty($boutique)){

				$formattedBoutique=[
				 		'id' => $boutique->getId(),
						'nom' => $boutique->getName(),
						'tel1' => $boutique->getTel1(),
						'tel2' => $boutique->getTel2(),
						'adresse' => $boutique->getAdresse()
				];
			}
			 
		     $formatted =[
				 'id'=>	$model->getId(),
	             'name' => $model->getName(),
				 'productName'=>$model->getIdProduit(),
				 'description' => $model->getDescription(),
				 'quantity'=> $model->getQuantity(),
				 'taille'=> $model->getTaille(),
				 'idCategory' => $model->getNameCategory(),
				 'idScategory' => $model->getNameScategory(),
				 'idSScategory' => $model->getNameSScategory(),
				 'idImage'=> $model->getIdImage(),
				 'idBigImage1'=> $model->getIdBigImage1(),
				 'idBigImage2'=> $model->getIdBigImage2(),
				 'idBigImage3'=> $model->getIdBigImage3(),
				 'idBigImage4'=> $model->getIdBigImage4(),
				 'isActivated'=> $model->getIsActivated(),
				 'isVirtual' => $model->getIsVirtual(),
				 'detail' => $formattedDetail,
				 'marque'=> $formattedMarque,
				 'boutique'=> $formattedBoutique,
				 'productCar' => $formattedProductCar,
				 'modelCar' =>	$formattedModelCar,
				 'retailSale' => $formattedRetailSale,
				 'wholeSale'=> $formattedWholeSale,
				 'BWMSale'=>$formattedBWMSale,
				 'notation'=> $formattedNotation
          	 ]; 
	   
	    
		
			$data=[
				'product'=> $formatted
			];
		
		 $response = [
        	'statut' => '200',
			'data' => $data
		 ]; 

		   header('Access-Control-Allow-Origin: *');
		   return new JsonResponse($response);
	}
	
/******************************************************************************************/	 
/*********************** get model  by text ***********************************************/	
    /**
     * @GET("/product/model/search/{text}/{page}/{filter_option}/{min_price}/{max_price}/{marque}/{category}")
	 * url-parm: id_category:id de la categorie, sous categorie et sous sous categorie
	 * return of a product of a category oder by most recently added
     */
	 public function getProductBytextAction($text,$page,$filter_option,$min_price,$max_price,$marque,$category,Request $request)
     {  
		$FILTERBYMOSTRESCENT="filter-by-most-rescent";
		$FILTERBYPRICEASC="filter-by-price-asc";
		$FILTERBYPRICEDESC="filter-by-price-desc";
		$FILTERBYPOPULARITY="filter-by-popularity";
		$FILTERBYNEW="filter-by-new";
		$formatted=[];
		$formattedWholeSale=[];
		$formattedRetailSale=[];
		$formattedMarque=[];
		$formattedSearchMarque=[] ;
		$formattedCat =[];
		
		$qb= $this->get('doctrine_mongodb')
						->getManager()
						->createQueryBuilder('RestBundle:ProductModel') 
						->field('name')->equals(new \MongoRegex('/'.$text.'/i'))
						->field('isActivated')->equals(true);
						
						
		$qb1= $this->get('doctrine_mongodb')
						->getManager()
						->createQueryBuilder('RestBundle:ProductModel') 
						->field('name')->equals(new \MongoRegex('/'.$text.'/i'))
						->field('isActivated')->equals(true);
						
		$qb2= $this->get('doctrine_mongodb')
						->getManager()
						->createQueryBuilder('RestBundle:ProductModel') 
						->field('name')->equals(new \MongoRegex('/'.$text.'/i'))
						->field('isActivated')->equals(true);
						
		//retrieving of the mark	corresponding to the search	: a reevoir

			
		$allMarques = $qb1->distinct("marque")->getQuery()->execute();
		 	foreach($allMarques as $marque1 ){
				$formattedSearchMarque[] =[
				'name' => $marque1['name']
			];
		}
		
		//retrieving of the categories 		
		$allCategories= $qb2->distinct('nameCategory')->getQuery()->execute();
		 foreach($allCategories as $cat ){
		 
			$catProducts = $this->get('doctrine_mongodb')
						  ->getManager()
						  ->createQueryBuilder('RestBundle:ProductModel') 
						  ->field('name')->equals(new \MongoRegex('/'.$text.'/i'))
						  ->field('nameCategory')->equals($cat)->getQuery()->execute();
				
			$formattedCat[] =[
				'name'=> $cat,
				'size'=> sizeof($catProducts)
			] ;
		}
						
		if($filter_option==$FILTERBYMOSTRESCENT){
			$qb= $qb->sort('insertionDate', 'desc');
		}
		
		if($filter_option==$FILTERBYPRICEASC){
			$qb= $qb->sort('actualPrice', 'asc');
		}
		
		if($filter_option==$FILTERBYPRICEDESC){
			$qb= $qb->sort('actualPrice', 'desc');
		}

		if($filter_option==$FILTERBYPOPULARITY){
			$qb= $qb->sort('popularity', 'desc');
		}
		
		if($marque!="null"){
			$qb= $qb->field('marque.name')->equals($marque);
		}
		
		if($category!="null"){
			$qb= $qb->field('nameCategory')->equals($category);
		}
		
		$n=0;
		$plusPetitPrix=0;
		$plusGrandPrix=0;
		$tour=1;
		$query1 = $qb->getQuery();
		$products1=$query1->execute();
		foreach($products1 as $product){
		    
			if($tour == 1){
				$plusPetitPrix = $product->getActualPrice();
			}
			 if($product->getActualPrice() > $plusGrandPrix ){
				$plusGrandPrix = $product->getActualPrice();
			}
			
			if($product->getActualPrice() < $plusPetitPrix ){
				$plusPetitPrix = $product->getActualPrice();
			} 
			
			$tour++;
		}
		$n=0;
		
		
		if($min_price!="null" && $max_price!="null" ){
			$qb= $qb->field('actualPrice')->range(floatval($min_price),floatval($max_price));
		}
		
		//retieving of number of product without min and max price
		$products2 = $qb->getQuery()->execute();
		foreach($products2 as $product2){
		 $n++;
		}
		
		$query = $qb->skip(40*($page-1))
					->limit(40)
                    ->getQuery();
					
		$products = $query->execute();
		
	
		
		foreach($products as $product){
		    
		    $productCaracteristics = $product->getProductCaracteristics();
			$modelCaracteristics = $product->getModelCaracteristics();
			$details = $product->getDetails();
			$retailSale= $product->getRetailSale();
			$formattedProductCar =[];
			$formattedModelCar =[];
			$formattedDetail=[];
			$formattedBWMSale=[];
			
			$marque= $product->getMarque();
			$formattedMarque =[
			 'name'=> $marque->getName(),
			 'idLogo'=> $marque->getIdLogo()
			];
			
			$retailSale = $product->getRetailSale();
			if($retailSale!= null){
				$date= $retailSale->getEndPromotionDate();
				$formattedRetailSale=[
					'price'=> $retailSale->getPrice(),
					'isInPromotion'=>$retailSale->getIsinPromotion(),
					'promotionalPrice'=> $retailSale->getPromotionalPrice(),
					'endPromotionDate'=> $date
				];
			}
			
			$wholeSale = $product->getWholeSale();
			if($wholeSale!= null){
				$date= $wholeSale->getEndPromotionDate();
				$formattedWholeSale=[
					'price'=> $wholeSale->getPrice(),
					'isInPromotion'=>$wholeSale->getIsinPromotion(),
					'promotionalPrice'=> $wholeSale->getPromotionalPrice(),
					'lotQuantity' => $wholeSale->getLotQuantity(),
					'endPromotionDate'=> $date
				];
			}
			
			$buyWithMeSale = $product->getBuyWithMeSale();
			$formattedBWMSale=[];
			if($buyWithMeSale!= null){
				$formattedBWMSale=[
					'price'=> $buyWithMeSale->getPrice(),
					'lotQuantity' => $buyWithMeSale->getLotQuantity(),
					'duree'=>  $buyWithMeSale->getDuree(),
					'ispersonnalizable'=> $buyWithMeSale->getIsinPersonalizable()
				];
			}
			
			$notation = $product->getNotation();
			if($notation!= null){
				$formattedNotation=[
					'level1'=> $notation->getLevel1(),
					'level2' => $notation->getLevel2(),
					'level3'=>  $notation->getLevel3(),
					'level4'=> $notation->getLevel4(),
					'level5'=> $notation->getLevel5(),
					'note'=> ($notation->getLevel1()*1 + $notation->getLevel2()*2 + $notation->getLevel3()*3 + $notation->getLevel4()*4 + $notation->getLevel5()*5)/
					($notation->getLevel1() + $notation->getLevel2() + $notation->getLevel3() + $notation->getLevel4()+ $notation->getLevel5()),
					'total'=> $notation->getLevel1() + $notation->getLevel2() + $notation->getLevel3() + $notation->getLevel4()+ $notation->getLevel5()
				];
			}
			else{
				$formattedNotation=[
					'level1'=> 0,
					'level2' => 0,
					'level3'=>  0,
					'level4'=> 0,
					'level5'=> 0,
					'note'=> 0,
					'total'=>0,
				];
			}
			
			foreach($productCaracteristics as $productCaracteristic ){
				$formattedProductCar[] =[
				'id' => $productCaracteristic->getId(),
				'name' => $productCaracteristic->getName(),
				'unity' => $productCaracteristic->getUnity(),
				'value' => $productCaracteristic->getValue()
				];
			}

			foreach($modelCaracteristics as $modelCaracteristic){
				$formattedModelCar[] =[
				'id' => $modelCaracteristic->getId(),
				'name' => $modelCaracteristic->getName(),
				'unity' => $modelCaracteristic->getUnity(),
				'value' => $modelCaracteristic->getValue()
				];
			}
			
			foreach($details as $detail){
				$formattedDetail[] =[
				'name' => $detail->getName(),
				'value' => $detail->getValue()
				];
			}
			 
			 
		     $formatted[] =[
			 'id'=>	$product->getId(),
             'name' => $product->getName(),
			 'productName'=>$product->getIdProduit(),
			 'description' => $product->getDescription(),
			 'quantity'=> $product->getQuantity(),
			 'taille'=> $product->getTaille(),
			 'idCategory' => $product->getNameCategory(),
			 'idScategory' => $product->getNameScategory(),
			 'idSScategory' => $product->getNameSScategory(),
			 'idImage'=> $product->getIdImage(),
			 'idBigImage1'=> $product->getIdBigImage1(),
			 'idBigImage2'=> $product->getIdBigImage2(),
			 'idBigImage3'=> $product->getIdBigImage3(),
			 'idBigImage4'=> $product->getIdBigImage4(),
			 'isActivated'=> $product->getIsActivated(),
			 'isVirtual' => $product->getIsVirtual(),
			 'detail' => $formattedDetail,
			 'marque'=> $formattedMarque,
			 'productCar' => $formattedProductCar,
			 'modelCar' =>	$formattedModelCar,
			 'retailSale' => $formattedRetailSale,
			 'wholeSale'=> $formattedWholeSale,
			 'BWMSale'=>$formattedBWMSale,
			 'notation'=> $formattedNotation
           ]; 
		}
		
        $data=[
			'size'=> $n,
			'marques'=> $formattedSearchMarque,
			'categories'=> $formattedCat,
			'plusGrandPrix' => $plusGrandPrix,
			'plusPetitPrix' => $plusPetitPrix,
			'products'=> $formatted
		];
		
		$response = [
			'status' => '200',
			'data' => $data
		];
		
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($response);
	}

	/**************************get by best category *********************************/	
    /**
     * @GET("/product/model/category/best/{nom_category}")
	 * url-parm: id_category:id de la categorie, sous categorie et sous sous categorie
	 * return of a product of a category oder by most recently added
     */
	 public function getBestProductByCategory1Action($nom_category,Request $request)
     {  
	 
		$formatted=[];
		$m=0;	
		$qb= $this->get('doctrine_mongodb')
						->getManager()
						->createQueryBuilder('RestBundle:ProductModel')
						->field('nameCategory')->equals($nom_category)
						->field('isActivated')->equals(true)
						->sort('popularity', 'desc');
						
		$query = $qb->limit(6)
                    ->getQuery();
		$products = $query->execute();
		
		foreach($products as $product){

			$formattedRetailSale=[];
			$retailSale = $product->getRetailSale();
			if($retailSale!= null){
				$date= $retailSale->getEndPromotionDate();
				$formattedRetailSale=[
					'price'=> $retailSale->getPrice(),
					'isInPromotion'=>$retailSale->getIsinPromotion(),
					'promotionalPrice'=> $retailSale->getPromotionalPrice(),
					'endPromotionDate'=> $date
				];
			}
			
			 
		     $formatted[] =[
			 'id'=>	$product->getId(),
             'name' => $product->getName(),
			 'productName'=>$product->getIdProduit(),
			 'idImage'=> $product->getIdImage(),
			 'retailSale' => $formattedRetailSale,
           ]; 
		}
		
		
		$response = [
        'status' => '200',
		'data' => $formatted
		];

	   header('Access-Control-Allow-Origin: *');
	   return new JsonResponse($response);
	}

	/**
     * @GET("/products/best")
	 * return all the products
     */
    public function getBestProductAction(Request $request)
    { 
		
		$qb= $this->get('doctrine_mongodb')
						->getManager()
						->createQueryBuilder('RestBundle:ProductModel')
						->find()
						->sort('popularity', 'desc')
						->limit(6);

		$query= $qb->getQuery();
		$products=$query->execute();
		$formatted = [];
		$response=[];
		$formattedRetailSale=[];

		foreach ($products as $product) {
			$formattedRetailSale=[];
			$retailSale = $product->getRetailSale();
			if($retailSale!= null){
				$date= $retailSale->getEndPromotionDate();
				$formattedRetailSale=[
					'price'=> $retailSale->getPrice(),
					'isInPromotion'=>$retailSale->getIsinPromotion(),
					'promotionalPrice'=> $retailSale->getPromotionalPrice(),
					'endPromotionDate'=> $date
				];
			}	
		
			$formatted[] = [
				'id'=>	$product->getId(),
             	'name' => $product->getName(),
			 	'idImage'=> $product->getIdImage(),
			 	'isVirtual' => $product->getIsVirtual(),
			 	'quantity'=> $product->getQuantity(),
			 	'retailSale' => $formattedRetailSale,
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
     * @GET("/product/model1/{name}")
	 *  
	 * return of the model corresponding to the name
     */
	 public function getProductModelByName1Action($name,Request $request)
     { 
		$dm= $this->get('doctrine_mongodb')->getManager();
	    $repository = $dm->getRepository('RestBundle:ProductModel');
	    $repository2 = $dm->getRepository('RestBundle:Seller');

	    $model= $repository->findOneBy(array('name' =>urldecode($name)));
	    if (!empty($model->getIdSeller())){
	    	 $boutique = $repository2->findOneById($model->getIdSeller());
	    }
	  
	     if(empty($model)){
			$formatted = [
               'statut' => '404'
            ];
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
	   } 
	   
	   $productCaracteristics = $model->getProductCaracteristics();
			$modelCaracteristics = $model->getModelCaracteristics();
			$details = $model->getDetails();
			$retailSale= $model->getRetailSale();
			$formattedProductCar =[];
			$formattedModelCar =[];
			$formattedDetail=[];
			$formattedWholeSale=[];
			$formattedRetailSale=[];
			$formattedBWMSale=[];
			$formattedNotation=[];
			$formattedBoutique=[];
			
			$marque= $model->getMarque();
			$formattedMarque =[
			 'name'=> $marque->getName(),
			 'idLogo'=> $marque->getIdLogo()
			];
			
			$retailSale = $model->getRetailSale();
			if($retailSale!= null){
				$date= $retailSale->getEndPromotionDate();
				$formattedRetailSale=[
					'price'=> $retailSale->getPrice(),
					'isInPromotion'=>$retailSale->getIsinPromotion(),
					'promotionalPrice'=> $retailSale->getPromotionalPrice(),
					'endPromotionDate'=> $date
				];
			}
			
			$wholeSale = $model->getWholeSale();
			if($wholeSale!= null){
				$date= $wholeSale->getEndPromotionDate();
				$formattedWholeSale=[
					'price'=> $wholeSale->getPrice(),
					'isInPromotion'=>$wholeSale->getIsinPromotion(),
					'promotionalPrice'=> $wholeSale->getPromotionalPrice(),
					'lotQuantity' => $wholeSale->getLotQuantity(),
					'endPromotionDate'=> $date
				];
			}
			
			$buyWithMeSale = $model->getBuyWithMeSale();
			$formattedBWMSale=[];
			if($buyWithMeSale!= null){
				$formattedBWMSale=[
					'price'=> $buyWithMeSale->getPrice(),
					'lotQuantity' => $buyWithMeSale->getLotQuantity(),
					'duree'=>  $buyWithMeSale->getDuree(),
					'ispersonnalizable'=> $buyWithMeSale->getIsinPersonalizable()
				];
			}
			
			foreach($productCaracteristics as $productCaracteristic ){
				$formattedProductCar[] =[
				'id' => $productCaracteristic->getId(),
				'name' => $productCaracteristic->getName(),
				'unity' => $productCaracteristic->getUnity(),
				'value' => $productCaracteristic->getValue()
				];
			}

			foreach($modelCaracteristics as $modelCaracteristic){
				$formattedModelCar[] =[
				'id' => $modelCaracteristic->getId(),
				'name' => $modelCaracteristic->getName(),
				'unity' => $modelCaracteristic->getUnity(),
				'value' => $modelCaracteristic->getValue()
				];
			}
			
			foreach($details as $detail){
				$formattedDetail[] =[
				'name' => $detail->getName(),
				'value' => $detail->getValue()
				];
			}
			
			$notation = $model->getNotation();
			if($notation!= null){
				$formattedNotation=[
					'level1'=> $notation->getLevel1(),
					'level2' => $notation->getLevel2(),
					'level3'=>  $notation->getLevel3(),
					'level4'=> $notation->getLevel4(),
					'level5'=> $notation->getLevel5(),
					'note'=> ($notation->getLevel1()*1 + $notation->getLevel2()*2 + $notation->getLevel3()*3 + $notation->getLevel4()*4 + $notation->getLevel5()*5)/
					($notation->getLevel1() + $notation->getLevel2() + $notation->getLevel3() + $notation->getLevel4()+ $notation->getLevel5()),
					'total'=> $notation->getLevel1() + $notation->getLevel2() + $notation->getLevel3() + $notation->getLevel4()+ $notation->getLevel5()
				];
			}
			else{
				$formattedNotation=[
					'level1'=> 0,
					'level2' => 0,
					'level3'=>  0,
					'level4'=> 0,
					'level5'=> 0,
					'note'=> 0,
					'total'=>0,
				];
			}
			 
			if(!empty($boutique)){

				$formattedBoutique=[
				 		'id' => $boutique->getId(),
						'nom' => $boutique->getName(),
						'tel1' => $boutique->getTel1(),
						'tel2' => $boutique->getTel2(),
						'adresse' => $boutique->getAdresse()
				];
			}
			 
		     $formatted =[
				 'id'=>	$model->getId(),
	             'name' => $model->getName(),
				 'productName'=>$model->getIdProduit(),
				 'description' => $model->getDescription(),
				 'quantity'=> $model->getQuantity(),
				 'taille'=> $model->getTaille(),
				 'idCategory' => $model->getNameCategory(),
				 'idScategory' => $model->getNameScategory(),
				 'idSScategory' => $model->getNameSScategory(),
				 'idImage'=> $model->getIdImage(),
				 'idBigImage1'=> $model->getIdBigImage1(),
				 'idBigImage2'=> $model->getIdBigImage2(),
				 'idBigImage3'=> $model->getIdBigImage3(),
				 'idBigImage4'=> $model->getIdBigImage4(),
				 'isActivated'=> $model->getIsActivated(),
				 'isVirtual' => $model->getIsVirtual(),
				 'detail' => $formattedDetail,
				 'marque'=> $formattedMarque,
				 'boutique'=> $formattedBoutique,
				 'productCar' => $formattedProductCar,
				 'modelCar' =>	$formattedModelCar,
				 'retailSale' => $formattedRetailSale,
				 'wholeSale'=> $formattedWholeSale,
				 'BWMSale'=>$formattedBWMSale,
				 'notation'=> $formattedNotation
          	 ]; 
	   
	    
		
			$data=[
				'product'=> $formatted
			];
		
		 $response = [
        	'statut' => '200',
			'data' => $data
		 ]; 

		   header('Access-Control-Allow-Origin: *');
		   return new JsonResponse($response);
	}
}
