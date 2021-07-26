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
use RestBundle\Document\CommandProduct;
use RestBundle\Document\Commande;
use RestBundle\Document\Livraison;
use RestBundle\Document\LivraisonAdress;
use RestBundle\Document\ProductModel;
use RestBundle\Document\Relais;
use RestBundle\Document\RelaisEmbedded;

class CommandController extends Controller
{
    /**
     * @POST("/command")
	 * create a command
     */
    public function postCommandAction(Request $request)
    {   
		  function fakeip()  
          {  
					return long2ip( mt_rand(0, 65537) * mt_rand(0, 65535) );   
		  } 
	   
		
		$dm= $this->get('doctrine_mongodb')->getManager();
		$repository = $dm->getRepository('RestBundle:ProductModel');
		$repository1 = $this->get('doctrine_mongodb')->getManager()->getRepository('RestBundle:User');
		
	    //check if the user exist and retreive 
	    $user= $repository1->findOneByLogin($request->get('login'));
		if(empty($user)){
			$formatted = [
				'statut' => '404'
			];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}
		
		// delete the current panier
		$products = $user->getPanierProducts();
		foreach ($products as $product) {
	       $user->removePanierProduct($product);
			$dm->flush();
		}
		
	 
		$reference = "OUN-".date("Y").date("m").date("d").date("h").date("i").date("s")."-".strtoupper(str_split($request->get('login'),3)[0]);
		$command= new Commande();
		$command->setReference($reference);
		$command->setLogin($request->get('login'));
		$command->setCommandDate(new \DateTime('now'));
		$command->setIsPaid(false);
		$command->setIsShipped(false);
		$command->setIsTreated(false);
		$command->setIsCancel(false);
		
		$adresseLivraison= new LivraisonAdress();
		$adress = json_decode($request->get('adressL'),true);
		$adresseLivraison->setNameReceptionist($adress['name']);
		$adresseLivraison->setSecondNameReceptionist($adress['firstName']);
		$adresseLivraison->setTelephone1Receptionist($adress['tel1']);
		$adresseLivraison->setTelephone2Receptionist($adress['tel2']);
		$adresseLivraison->setRegion($adress['region']);
		$adresseLivraison->setTown($adress['town']);
		$adresseLivraison->setAdresse($adress['adresse']);

		$livraison= new Livraison();
		$liv = json_decode($request->get('livraison'),true);
		$livraison->setType($liv['type']);
		$livraison->setPrice($liv['price']);
		$livraison->setDelais($liv['delais']);
		

		$adresseRelais= new RelaisEmbedded();
		if($liv['type']==2){
			$relais = json_decode($request->get('relais'),true);
			$adresseRelais->setNom($relais['nom']);
			$adresseRelais->setQuartier($relais['quartier']);
			$adresseRelais->setEmplacement($relais['emplacement']);
			$command->setRelaisAdress($adresseRelais);
		}
			
		
		$products= json_decode($request->get('products'),true);
		foreach ($products as $prod) {
			$item= new CommandProduct();
			$item->setName($prod['name']);
			$item->setPrice($prod['price']);
			$item->setQuantity($prod['quantity']);
			$item->setIsCancel(false);
			$command->addCommandProduct($item);
		}
		
		
		$command->setLivraisonAdress($adresseLivraison);
		$command->setLivraison($livraison);
		$dm = $this->get('doctrine_mongodb')->getManager();
		$dm->persist($command);
		$dm->flush();
		
		// modification des quantite des produits
		
		/* foreach ($products as $product) {
			$myProduct= $repository->findOneByName($product['name']);
			$nb= $myProduct->getQuantity();
			$myProduct-> setQuantity($nb - $product['quantity']);
			$dm->flush();
		} */
		
		// envois de l'email de notification
		// send of the activation inside the email or a sms
		if(preg_match("#^([A-Za-z0-9])+\@([A-Za-z0-9])+\.([A-Za-z]{2,4})$#", $request->get('login'))){
			$message = \Swift_Message::newInstance()
		   ->setSubject('Confirmation de la commande')
		   ->setFrom('mail@ounkoun.com')
		   ->setTo($request->get('login'))
		   ->setBody($this->renderView(
				// app/Resources/views/Emails/registration.html.twig
				'RestBundle:Emails:confirmCommand.html.twig',array('name' => $user->getPseudo())
			),'text/html');
			
			$this->get('mailer') ->send($message);
			
		}else{
			
			// GlobexCamSMS's POST URL
			$postUrl = "http://193.105.74.59/api/sendsms/xml";
			// XML-formatted data
			$xmlString =
			"<SMS>
			<authentification>
			<username>nanojunior</username>
			<password>Nanojunior92</password>
			</authentification>
			<message>
			<sender>OUNKOUN</sender>
			<text> Merci ".$user->getPseudo()." Votre commande a bien été enregistrée, vous serez contacter dans les instants qui
			suivent  pour la confirmation.
			</text>
			
			</message>
			<recipients>
			<gsm>237".$request->get('login')."</gsm>
			</recipients>
			</SMS>";
			
			// previously formatted XML data becomes value of "XML" POST variable
			$fields = "XML=" . urlencode($xmlString);
			
			// in this example, POST request was made using PHP's CURL
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $postUrl);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("REMOTE_ADDR: ".fakeip(),"X-Client-IP: ".fakeip(),"Client-IP: ".fakeip(),"HTTP_X_FORWARDED_FOR: ".fakeip(),"X-Forwarded-For: ".fakeip()));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			// response of the POST request
			$smsResponse = curl_exec($ch);
			curl_close($ch);
		}
		

		$data=[
		'reference'=> $reference
		];
		
		$response = [
		 'statut' => '200',
		 'data'=> $data
		];

		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($response);	
	}
	
	/**
     * @GET("/command/all")
	 * create a command
     */
    public function getAllCommandAction(Request $request)
    {
		$i=0;
		$dm= $this->get('doctrine_mongodb')->getManager();		
		$repository2 = $dm->getRepository('RestBundle:ProductModel');
		$qb= $this->get('doctrine_mongodb')
						->getManager()
						->createQueryBuilder('RestBundle:Commande')
				        ->sort('commandDate', 'asc');
		$commandes = $qb->getQuery()->execute();
		
		foreach($commandes as $command){
		
			// retreving of livraison adresse
			$adresseDeLivraison = $command->getLivraisonAdress();
			if(!empty($adresseDeLivraison)){
				$formattedAdresse=[
				'name' => $adresseDeLivraison->getNameReceptionist() ,
				'firstName' => $adresseDeLivraison->getSecondNameReceptionist(),
				'tel1'=>$adresseDeLivraison-> getTelephone1Receptionist(),
				'tel2'=>$adresseDeLivraison->getTelephone2Receptionist(),
				'region'=>$adresseDeLivraison->getRegion(),
				'town'=>$adresseDeLivraison->getTown(),
				'adresse'=>$adresseDeLivraison-> getAdresse()
				];
			}else{
			$formattedAdresse='null';
			}
			
			$productCommands= $command->getCommandProduct();
			$formattedProducts =[];
			if(empty($productCommands)){
				$formattedProducts =[];
			}else
			{
				foreach ($productCommands as $productCommand) {
			
					$product = $repository2->findOneByName($productCommand->getName());
					if(!empty($product))
					{
					$productCaracteristics = $product->getProductCaracteristics();
					$modelCaracteristics = $product->getModelCaracteristics();
					$details = $product->getDetails();
					$retailSale= $product->getRetailSale();
					$formattedProductCar =[];
					$formattedModelCar =[];
					$formattedDetail=[];
				
					$marque= $product->getMarque();
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
					 
					 
					$formatted=[
					 'id'=>	$product->getId(),
					 'name' => $product->getName(),
					 'description' => $product->getDescription(),
					 'idProduct'=> $product->getIdProduit(),
					 'idCategory' => $product->getNameCategory(),
					 'idScategory' => $product->getNameScategory(),
					 'idSScategory' => $product->getNameSScategory(),
					 'quantity'=> $product->getQuantity(),
					 'idImage'=> $product->getIdImage(),
					 'idBigImage1'=> $product->getIdBigImage1(),
					 'idBigImage2'=> $product->getIdBigImage2(),
					 'idBigImage3'=> $product->getIdBigImage3(),
					 'idBigImage4'=> $product->getIdBigImage4(),
					 'detail' => $formattedDetail,
					 'marque'=> $formattedMarque,
					 'productCar' => $formattedProductCar,
					 'modelCar' =>	$formattedModelCar,
					 'retailSale' => $formattedRetailSale
					]; 
					
					    $formattedProducts[] = [
							'quantity'=>$productCommand->getQuantity(),
							'price'=>$productCommand->getPrice(),
							'product'=>$formatted
					   ];	
				   }
				   			
				}
		    }
			
			$formattedCommand[]=[
			   'reference'=> $command->getReference(),
			   'dateCreation'=> $command->getCommandDate(),
			   'isTreated'=> $command->getIsTreated(),
			   'isCancel'=> $command->getIsCancel(),
			   'isPaided'=> $command->getIsPaid(),
			   'isShipped'=> $command->getIsShipped(),
			   'livraisonAdress'=> $formattedAdresse,
			   'products'=> $formattedProducts
			];
		 
			$i++;
		}
		
		 $response = [
        'statut' => '200',
		'data' =>  $formattedCommand
		];
		
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($response);
	}
	
	/**
     * @GET("/command/condition/{parm}")
	 * create a command
     */
    public function getByConditionCommandAction($parm, Request $request)
    {
		
		$i=0;
		$dm= $this->get('doctrine_mongodb')->getManager();	
	    $formattedCommand=[];
		$repository2 = $dm->getRepository('RestBundle:ProductModel');
		$qb= $this->get('doctrine_mongodb')
						->getManager()
						->createQueryBuilder('RestBundle:Commande');
				       
						
		if($parm == "traiter"){
			$qb= $qb->field('isTreated')->equals(true);
		}
		
		else if($parm == "nontraiter"){
			$qb= $qb->field('isTreated')->equals(false);
		}
		
		else if($parm == "payer"){
			$qb= $qb->field('isPaid')->equals(true);
		}
		
		else if($parm == "nonpayer"){
			$qb= $qb->field('isPaid')->equals(false);
		}
		
		else if($parm == "livrer"){
		  $qb= $qb->field('isShipped')->equals(true);
		}
		
		else if($parm == "nonlivrer"){
			$qb= $qb->field('isShipped')->equals(false);
		}
		
		
		
		$qb= $qb ->sort('commandDate', 'desc');
						
						
						
		$commandes = $qb->getQuery()->execute();
		
		foreach($commandes as $command){
		
			// retreving of livraison adresse
			$adresseDeLivraison = $command->getLivraisonAdress();
			if(!empty($adresseDeLivraison)){
				$formattedAdresse=[
				'name' => $adresseDeLivraison->getNameReceptionist() ,
				'firstName' => $adresseDeLivraison->getSecondNameReceptionist(),
				'tel1'=>$adresseDeLivraison-> getTelephone1Receptionist(),
				'tel2'=>$adresseDeLivraison->getTelephone2Receptionist(),
				'region'=>$adresseDeLivraison->getRegion(),
				'town'=>$adresseDeLivraison->getTown(),
				'adresse'=>$adresseDeLivraison-> getAdresse()
				];
			}else{
			$formattedAdresse='null';
			}
			
			$productCommands= $command->getCommandProduct();
			$formattedProducts =[];
			if(empty($productCommands)){
				$formattedProducts =[];
			}else
			{
				foreach ($productCommands as $productCommand) {
			
					$product = $repository2->findOneByName($productCommand->getName());
					if(!empty($product))
					{
					$productCaracteristics = $product->getProductCaracteristics();
					$modelCaracteristics = $product->getModelCaracteristics();
					$details = $product->getDetails();
					$retailSale= $product->getRetailSale();
					$formattedProductCar =[];
					$formattedModelCar =[];
					$formattedDetail=[];
				
					$marque= $product->getMarque();
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
					 
					 
					$formatted=[
					 'id'=>	$product->getId(),
					 'name' => $product->getName(),
					 'description' => $product->getDescription(),
					 'idProduct'=> $product->getIdProduit(),
					 'idCategory' => $product->getNameCategory(),
					 'idScategory' => $product->getNameScategory(),
					 'idSScategory' => $product->getNameSScategory(),
					 'quantity'=> $product->getQuantity(),
					 'idImage'=> $product->getIdImage(),
					 'idBigImage1'=> $product->getIdBigImage1(),
					 'idBigImage2'=> $product->getIdBigImage2(),
					 'idBigImage3'=> $product->getIdBigImage3(),
					 'idBigImage4'=> $product->getIdBigImage4(),
					 'detail' => $formattedDetail,
					 'marque'=> $formattedMarque,
					 'productCar' => $formattedProductCar,
					 'modelCar' =>	$formattedModelCar,
					 'retailSale' => $formattedRetailSale
					]; 
					
					    $formattedProducts[] = [
							'quantity'=>$productCommand->getQuantity(),
							'price'=>$productCommand->getPrice(),
							'product'=>$formatted
					   ];	
				   }
				   			
				}
		    }
			
			$formattedCommand[]=[
			   'reference'=> $command->getReference(),
			   'dateCreation'=> $command->getCommandDate(),
			   'isTreated'=> $command->getIsTreated(),
			   'isCancel'=> $command->getIsCancel(),
			   'isPaided'=> $command->getIsPaid(),
			   'isShipped'=> $command->getIsShipped(),
			   'livraisonAdress'=> $formattedAdresse,
			   'products'=> $formattedProducts
			];
		 
			$i++;
		}
		
		 $response = [
        'statut' => '200',
		'data' =>  $formattedCommand
		];
		
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($response);
	}
	
	
	/**
     * @GET("/command/one/{reference}")
	 * create a command
     */
    public function getOneCommandAction($reference,Request $request)
    {
		$dm= $this->get('doctrine_mongodb')->getManager();		
		$repository = $dm->getRepository('RestBundle:Commande');
		$repository2 = $dm->getRepository('RestBundle:ProductModel');
		$command = $repository->findOneByReference($reference);
		
		// retreving of livraison adresse
		$adresseDeLivraison = $command->getLivraisonAdress();
		if(!empty($adresseDeLivraison)){
			$formattedAdresse=[
				'name' => $adresseDeLivraison->getNameReceptionist() ,
				'firstName' => $adresseDeLivraison->getSecondNameReceptionist(),
				'tel1'=>$adresseDeLivraison-> getTelephone1Receptionist(),
				'tel2'=>$adresseDeLivraison->getTelephone2Receptionist(),
				'region'=>$adresseDeLivraison->getRegion(),
				'town'=>$adresseDeLivraison->getTown(),
				'adresse'=>$adresseDeLivraison-> getAdresse()
			];
		}
		else{
			$formattedAdresse='null';
		}
			
		$productCommands= $command->getCommandProduct();
		$formattedProducts =[];
		if(empty($productCommands)){
			$formattedProducts =[];
		}
		else
		{
			foreach ($productCommands as $productCommand) {
			
				$product = $repository2->findOneByName($productCommand->getName());
				if(!empty($product))
				{
					$productCaracteristics = $product->getProductCaracteristics();
					$modelCaracteristics = $product->getModelCaracteristics();
					$details = $product->getDetails();
					$retailSale= $product->getRetailSale();
					$formattedProductCar =[];
					$formattedModelCar =[];
					$formattedDetail=[];
				
					$marque= $product->getMarque();
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
					 
					 
					$formatted=[
					 'id'=>	$product->getId(),
					 'name' => $product->getName(),
					 'description' => $product->getDescription(),
					 'idProduct'=> $product->getIdProduit(),
					 'idCategory' => $product->getNameCategory(),
					 'idScategory' => $product->getNameScategory(),
					 'idSScategory' => $product->getNameSScategory(),
					 'quantity'=> $product->getQuantity(),
					 'idImage'=> $product->getIdImage(),
					 'idBigImage1'=> $product->getIdBigImage1(),
					 'idBigImage2'=> $product->getIdBigImage2(),
					 'idBigImage3'=> $product->getIdBigImage3(),
					 'idBigImage4'=> $product->getIdBigImage4(),
					 'detail' => $formattedDetail,
					 'marque'=> $formattedMarque,
					 'productCar' => $formattedProductCar,
					 'modelCar' =>	$formattedModelCar,
					 'retailSale' => $formattedRetailSale
					]; 
					
					    $formattedProducts[] = [
							'quantity'=>$productCommand->getQuantity(),
							'price'=>$productCommand->getPrice(),
							'isCancel'=> $productCommand->getIsCancel(),
							'product'=>$formatted
					   ];	
				}
				   			
			}
		}
			
		$formattedCommand =[
			'reference'=> $command->getReference(),
			'dateCreation'=> $command->getCommandDate(),
			'isTreated'=> $command->getIsTreated(),
			'isCancel'=> $command->getIsCancel(),
			'isPaided'=> $command->getIsPaid(),
			'isShipped'=> $command->getIsShipped(),
			'livraisonAdress'=> $formattedAdresse,
			'products'=> $formattedProducts
		];
		
		
		 $response = [
        'statut' => '200',
		'data' =>  $formattedCommand
		];
		
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($response);
	}
	
	
	/**
     * @POST("/command/article/cancel")
	 * 
     */
    public function cancelCommandArticleAction(Request $request)
    {   
		$dm= $this->get('doctrine_mongodb')->getManager();
		$repository = $dm->getRepository('RestBundle:Commande');
		
	    //check if the user exist
	    $commande= $repository->findOneByReference($request->get('reference'));
		if(empty($commande)){
			
			$formatted = [
				'statut' => '404'
			];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}
		
		$articles= $commande->getCommandProduct();
		foreach ($articles as $article) {
			if($article->getName() == $request->get('name')){
				$myArticle=$article;
			}
		}
		
		$newArticle= new CommandProduct();
		$newArticle->setQuantity($myArticle->getQuantity());
		$newArticle->setPrice($myArticle->getPrice());
		$newArticle->setName($myArticle->getName());
		$newArticle->setIsCancel(true);
		
		$commande->removeCommandProduct($myArticle);
		$commande->addCommandProduct($newArticle);
		$dm->flush();
		
		$formatted = [
			'statut' => '200'
		];
		
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
		
	}
	
	
	/**
     * @POST("/command/cancel")
	 * 
     */
    public function cancelCommandAction(Request $request)
    {   
		$dm= $this->get('doctrine_mongodb')->getManager();
		$repository = $dm->getRepository('RestBundle:Commande');
		
	    //check if the command exist
	    $commande= $repository->findOneByReference($request->get('reference'));
		if(empty($commande)){
			$formatted = [
				'statut' => '404'
			];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}
		
		$articles= $commande->getCommandProduct();
		foreach ($articles as $article){
			$newArticle= new CommandProduct();
			$newArticle->setQuantity($article->getQuantity());
			$newArticle->setPrice($article->getPrice());
			$newArticle->setName($article->getName());
			$newArticle->setIsCancel(true);
			$commande->removeCommandProduct($article);
			$commande->addCommandProduct($newArticle);
			$dm->flush();
		}
		
		$commande->setIsCancel(true);
		$dm->flush();
		
		$formatted = [
			'statut' => '200'
		];
		
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
		
	}
	
	
}