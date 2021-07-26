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
use RestBundle\Document\User;
use RestBundle\Document\Favorite;
use RestBundle\Document\PanierProduct;
use RestBundle\Document\PanierBWMProduct;
use RestBundle\Document\PanierProductGros;
use RestBundle\Document\AuthToken;
use RestBundle\Document\LivraisonAdress;
use RestBundle\Document\RelaisEmbedded;
use RestBundle\Document\ProductModel;
use RestBundle\Document\ProductModelGros;


class UserController extends Controller
{

	
	
	/**
     * @GET("/user/auth")
	 * registration
     */
    public function postSocialAction(Request $request)
    {
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
			<sender>nono</sender>
			<text>Message from your friend!</text>
			</message>
			<recipients>
			<gsm>237699494380</gsm>
			</recipients>
			</SMS>";
			
			// previously formatted XML data becomes value of "XML" POST variable
			$fields = "XML=" . urlencode($xmlString);
			
			// in this example, POST request was made using PHP's CURL
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $postUrl);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
			
			// response of the POST request
			$smsResponse = curl_exec($ch);
			curl_close($ch);
			// write out the response
			//echo $response;
			$response = [
				'statut' => '200',
				'response'=> $smsResponse
			];

			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($response);	
	}
	
	/**
     * @POST("/user/timer")
	 * registration
     */
    public function posttimerAction(Request $request)
    {
	    $dm= $this->get('doctrine_mongodb')->getManager();
		$user_id = $request->get('login');

		// retrieving of the user
	    $repository = $this->get('doctrine_mongodb')->getManager()->getRepository('RestBundle:User');
	   
        sleep(185);
 	
		$user = $repository->findOneByLogin($request->get('login'));
		if($user->getIsActivated()==true){
		 $state="active";
		}else{
			$state="non active";
			$dm->remove($user);
			$dm->flush();
		}
		
		$response = [
			'statut' => '200',
			'response'=> $user->getIsActivated()
		];
		
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($response);
	}
	

	/**
     * @POST("/user/passRecover/code")
	 * renew password
     */
    public function renewPassWordAction(Request $request)
    {	
	
		$dm= $this->get('doctrine_mongodb')->getManager();

		function RandomString(){
			$characters = '0123456789';
			$randstring = "";
			for ($i = 0; $i < 5; $i++) {
				$randstring .= $characters[rand(0, strlen($characters))];
			}
			return $randstring;
		}

		function fakeip()  
      	{  
        	return long2ip( mt_rand(0, 65537) * mt_rand(0, 65535) );   
      	} 

		$tempPassword="";

		// retrieving of the user
		$repository = $this->get('doctrine_mongodb')->getManager()->getRepository('RestBundle:User');
		$user = $repository->findOneByLogin($request->get('login'));
		if(empty($user)){
			$formatted = [
               'statut' => '404'
            ];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
	    }else{

			$code= RandomString();
			$user->setActivationNumber($code);
			$dm->flush();
			// send of the activation inside the email or a sms
			if(preg_match("#^([A-Za-z0-9])+\@([A-Za-z0-9])+\.([A-Za-z]{2,4})$#", $request->get('login'))){
				$message = \Swift_Message::newInstance()
			   ->setSubject('code de reinitialisation')
			   ->setFrom('mail@ounkoun.com')
			   ->setTo($request->get('login'))
			   ->setBody($this->renderView(
					// app/Resources/views/Emails/registration.html.twig
					'RestBundle:Emails:reinitPassword.html.twig',array('code' => $code)
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
				<text>Le code de réinitialisation votre mot de passe ounkoun est ".$code."</text>
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

			$response = [
			'statut' => '200'
			];

			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($response);
		} 
	}
	
	/**
     * @POST("/user/passRecover/confirm")
	 * new password confirmation
     */
    public function ConfirmNewPassWordAction(Request $request)
    {
	
		$dm= $this->get('doctrine_mongodb')->getManager();
		// retrieving of the user
		$repository = $this->get('doctrine_mongodb')->getManager()->getRepository('RestBundle:User');
		$user = $repository->findOneByLogin($request->get('login'));
        $newPassword= $request->get('password');
        $code= $request->get('code');

		
	
		if(empty($user) ){
			$formatted = [
               'statut' => '404'
            ];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
	    }
	    else{
			
			$code = $user->getActivationNumber();
			if($code==$request->get('code')){		
				$encoder = $this->get('security.password_encoder');
				$encoded = $encoder->encodePassword($user, $newPassword);
				$user->setPassword( $encoded);
				$dm->flush();		
			}
			else{
				$response = [
				'statut' => '404'
				];
				header('Access-Control-Allow-Origin: *');
				return new JsonResponse($response);
			}
			
			$response = [
			'statut' => '200'
			];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($response);
		} 
	}
	
	
	
    /**
     * @POST("/user/registration")
	 * registration
     */
    public function postUser1Action(Request $request)
    {  
	   $pieceNom = " ";
	   $piecePrenom = " ";
	   $activationNumber= " ";
	   
	   function fakeip()  
      {  
        return long2ip( mt_rand(0, 65537) * mt_rand(0, 65535) );   
      } 
	   
	   $user= new user();
	   $user->setLogin($request->get('login'));
	   $user->setPlainPassword($request->get('plainPassword'));
	   $user->setName($request->get('name'));
	   $user->setFirstName($request->get('firstName'));
	   $user->setSex($request->get('sex'));
	   $user->setBirthDate($request->get('dateDeNaiss'));
	   $user->setIsActivated(false);
       $pieceNom = explode(" ", $request->get('name'));
	   $piecePrenom = explode(" ", $request->get('firstName'));
	   $user->setPseudo($piecePrenom[0]." ".$pieceNom[0] );
	   
	    function RandomString(){
			$characters = "0123456789";
			$randstring = "";
			for ($i = 0; $i < 5; $i++) {
				$randstring .= $characters[rand(0,strlen($characters))%5];
			}
			return $randstring;
		}
	   
	    //check if an account with the email or tel is already exist
		   $repository = $this->get('doctrine_mongodb')->getManager()->getRepository('RestBundle:User');
		   $user1 = $repository->findOneByLogin($request->get('login'));
		   if(!empty($user1)){
				$formatted = [
				   'statut' => '404'
				];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		
	       }
	   
	   
	   // else create an account 
	   //building of hash password
	   $encoded= " ";
       $encoder = $this->get('security.password_encoder');
	   $encoded = $encoder->encodePassword($user, $user->getPlainPassword());
	   $user->setPassword( $encoded);
	   
	   // buiding of the token
	   $token = "";
	   $token = new AuthToken();
	   $token->setValue(base64_encode(random_bytes(50)));
	   $token->setCreatedAt(new \DateTime('now'));
	   $user->setToken($token);
	   
	   
	   // building of an activated number
	   $activationNumber = " ";
	   $activationNumber = RandomString();
	   $user->setActivationNumber($activationNumber);
	   
	   $formattedToken=[
		'value' => $token->getValue(),
		'createdAt' => $token->getCreatedAt()
	   ];
	   
	   $dm = $this->get('doctrine_mongodb')->getManager();
	   $dm->persist($token);
	   $dm->persist($user);
       $dm->flush();
		 
	    // building of the response 
	   $repository = $this->get('doctrine_mongodb')->getManager()->getRepository('RestBundle:User');
	   $user1= $repository->findOneByLogin($user->getLogin());
		
		
	    $payload = [ 
			'id'=> $user1->getId(),
			'name' => $user1->getName(),
			'firstName'=> $user1->getFirstName(),
			'login' => $user1->getLogin(),
			'pseudo'=> $user1->getPseudo(),
			'token'=>  $formattedToken
		];
		
		
			// send of the activation inside the email or a sms
			if(preg_match("#^([A-Za-z0-9])+\@([A-Za-z0-9])+\.([A-Za-z]{2,4})$#", $request->get('login'))){
				$message = \Swift_Message::newInstance()
			   ->setSubject('code d\'activation')
			   ->setFrom('mail@ounkoun.com')
			   ->setTo($request->get('login'))
			   ->setBody($this->renderView(
					// app/Resources/views/Emails/registration.html.twig
					'RestBundle:Emails:registration.html.twig',array('code' => $activationNumber)
				),'text/html');
				
				$this->get('mailer') ->send($message);
			}
			else{
				
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
				<text>Le code d'actication de votre compte ounkoun est ".$activationNumber."</text>
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
		
		$response = [
				'statut' => '200',
				'response'=> $payload
		];

		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($response);
    }
	
	 /**
     * @POST("/user/registration/social")
	 * registration
     */
    public function postUserSocialAction(Request $request)
    {  
	   $user= new user();
	   $user->setLogin($request->get('login'));
	   $user->setName($request->get('name'));
	   $user->setPseudo($request->get('pseudo'));
	   $user->setIsActivated(true);
       
	   
	   // check if an account with the account already exist
	   $repository = $this->get('doctrine_mongodb')->getManager()->getRepository('RestBundle:User');
	   $repository2 = $this->get('doctrine_mongodb')->getManager()->getRepository('RestBundle:ProductModel');
	   $user1 = $repository->findOneByLogin($request->get('login'));
	   if(!empty($user1)){
	   
			// retreving of livraison adresse
			$adresseDeLivraison = $user1->getLivraisonAdress();
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
				$formattedAdresse="null";
			}
		
			//retreiving of the favorite
			$favorites=$user1->getFavorites();
			$formattedFavorites =[];
			if(empty($favorites)){
				$formattedFavorites =[];
			}else
			{
				foreach ($favorites as $favorite) {
					$product = $repository2->findOneById($favorite->getIdProduct());
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
					$formattedProductCar=[];
					foreach($productCaracteristics as $productCaracteristic ){
						$formattedProductCar[] =[
						'id' => $productCaracteristic->getId(),
						'name' => $productCaracteristic->getName(),
						'unity' => $productCaracteristic->getUnity(),
						'value' => $productCaracteristic->getValue()
						];
					}
					$formattedModelCar=[];
					foreach($modelCaracteristics as $modelCaracteristic){
						$formattedModelCar[] =[
						'id' => $modelCaracteristic->getId(),
						'name' => $modelCaracteristic->getName(),
						'unity' => $modelCaracteristic->getUnity(),
						'value' => $modelCaracteristic->getValue()
						];
					}
				    $formattedDetail=[];
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
						'idCategory' => $product->getNameCategory(),
						'idScategory' => $product->getNameScategory(),
						'idSScategory' => $product->getNameSScategory(),
						'idImage'=> $product->getIdImage(),
						'idBigImage1'=> $product->getIdBigImage1(),
						'idBigImage2'=> $product->getIdBigImage2(),
						'idBigImage3'=> $product->getIdBigImage3(),
						'idBigImage4'=> $product->getIdBigImage4(),
						'detail' => $formattedDetail,
						'marque'=> $formattedMarque,
						'productCar' => $formattedProductCar,
						'modelCar' =>	$formattedModelCar,
						'retailSale' => $formattedRetailSale,
						'wholeSale'=> $formattedWholeSale
					]; 
			   
					$formattedFavorites[]=[
						'dateAjout'=> $favorite->getAddDate(),
						'product'=> $formatted
					];
			
				}
			}
			
			//recuperation des produit du panier
			//recuperation des produit du panier
			$productPaniers=$user1->getPanierProducts();
			$productPaniersGros= $user1->getPanierProductGros();
			$formattedProductPaniers =[];
			if(empty($productPaniers)){
				$formattedProductPaniers =[];
			}else
			{
				foreach ($productPaniers as $productPanier) {
			
					$product = $repository2->findOneById($productPanier->getIdProduct());
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
					 'idCategory' => $product->getNameCategory(),
					 'idScategory' => $product->getNameScategory(),
					 'idSScategory' => $product->getNameSScategory(),
					 'idImage'=> $product->getIdImage(),
					 'idBigImage1'=> $product->getIdBigImage1(),
					 'idBigImage2'=> $product->getIdBigImage2(),
					 'idBigImage3'=> $product->getIdBigImage3(),
					 'idBigImage4'=> $product->getIdBigImage4(),
					 'detail' => $formattedDetail,
					 'marque'=> $formattedMarque,
					 'productCar' => $formattedProductCar,
					 'modelCar' =>	$formattedModelCar,
					 'retailSale' => $formattedRetailSale,
					 'wholeSale'=> $formattedWholeSale
						]; 
				   
				   $formattedProductPaniers[]=[
					'type'=> $productPanier->getTypeVente(),
					'number'=>  $productPanier->getNumber(),
					'product'=> $formatted				
				   ];
				
				}
			}	
			// produit en gros
			if(!empty($productPaniersGros)){
				foreach ($productPaniersGros as $productPanier) {
		
					$product = $repository2->findOneById($productPanier->getPanierProducts()[0]->getIdProduct());
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
					 'idCategory' => $product->getNameCategory(),
					 'idScategory' => $product->getNameScategory(),
					 'idSScategory' => $product->getNameSScategory(),
					 'idImage'=> $product->getIdImage(),
					 'idBigImage1'=> $product->getIdBigImage1(),
					 'idBigImage2'=> $product->getIdBigImage2(),
					 'idBigImage3'=> $product->getIdBigImage3(),
					 'idBigImage4'=> $product->getIdBigImage4(),
					 'detail' => $formattedDetail,
					 'marque'=> $formattedMarque,
					 'productCar' => $formattedProductCar,
					 'modelCar' =>	$formattedModelCar,
					 'retailSale' => $formattedRetailSale,
					 'wholeSale'=> $formattedWholeSale
					]; 
				   
					   $formattedProductPaniers[]=[
						'type'=> $productPanier->getTypeVente(),
						'number'=>  $productPanier->getNumber(),
						'product'=> $formatted				
					   ];
			
				}
			}
		 
			$token = $user1->getToken();
			$formattedToken=[
				'value' =>  $token->getValue(),
				'createdAt' => $token->getCreatedAt()
			]; 
		 
			$payload = [ 
				
				'name' => $user->getName(),
				'firstName'=> "null",
				'sex'=>"null",
				'login' => $user1->getLogin(),
				'pseudo'=> $user1->getPseudo(),
				'dateDeNaiss'=>"null",
				'token'=> $formattedToken,
				'livraisonAddress'=>$formattedAdresse,
				'favorites' => $formattedFavorites,
				'panier'=>	$formattedProductPaniers
				
			]; 
		
			$response = [
				'statut' => '404',
				'response'=> $payload
			];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($response);
        }
		else // enregistrement 
		{
			$formattedAdresse="null";
			$formattedFavorites =[];
			$formattedAdresse="null";
			$formattedProductPaniers =[];
			
			// buiding of the token
			$token = new AuthToken();
			$token->setValue($request->get('token'));
			$token->setCreatedAt(new \DateTime('now'));
			$user->setToken($token);
	   
			$formattedToken=[
				'value'=> $token->getValue(),
				'createdAt'=> $token->getCreatedAt()
			];
			
		 
			$payload = [ 	
				'name' => $user->getName(),
				'firstName'=> "null",
				'sex'=>"null",
				'login' => $user->getLogin(),
				'pseudo'=> $user->getPseudo(),
				'dateDeNaiss'=>"null",
				'token'=> $formattedToken,
				'livraisonAddress'=>$formattedAdresse,
				'favorites' => $formattedFavorites,
				'panier'=>	$formattedProductPaniers
				
			]; 
	   
			$dm = $this->get('doctrine_mongodb')->getManager();
			$dm->persist($token);
			$dm->persist($user);
			$dm->flush();
		
			$response = [
				'statut' => '200'
			];

			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($response);
		}
	}	
	

	
	/**
     * @POST("/user/validation")
	 * validation of the registration
     */
    public function validateUserAction(Request $request)
    {
		// retrieving of the user
	   $repository = $this->get('doctrine_mongodb')->getManager()->getRepository('RestBundle:User');
	   $user = $repository->findOneByLogin($request->get('login'));
	   if(empty($user)){
			$formatted = [
               'statut' => '404'
            ];
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
	   }
	   
	   if($request->get('code') == $user->getActivationNumber()){
	   
	     $user->setIsActivated(true);
		  $token = $user->getToken();
		 $formattedToken=[
		   'value' =>  $token->getValue(),
		   'createdAt' => $token->getCreatedAt()
		 ]; 
		 
		$payload = [ 
			'id'=> $user->getId(),
			'name' => $user->getName(),
			'firstName'=> $user->getFirstName(),
			'sex'=> $user->getSex(),
			'login' => $user->getLogin(),
			'pseudo'=> $user->getPseudo(),
			'dateDeNaiss'=> $user->getBirthDate(),
			'token'=>  $formattedToken,
		]; 
		
	   }
	   else{
			$formatted = [
               'statut' => '404'
            ];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted); 
	   }
	   
	    $dm = $this->get('doctrine_mongodb')->getManager();
        $dm->flush();
	    $response = [
			'statut' => '200',
			'response'=> $payload
	    ];
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($response);
	}
	
	
	/**
     * @GET("/user/log/{login}/{password}")
	 * logging of a user
     */
    public function logUserAction($login,$password ,Request $request)
    {  
	   
	    $repository = $this->get('doctrine_mongodb')->getManager()->getRepository('RestBundle:User');
		$repository2 = $this->get('doctrine_mongodb')->getManager()->getRepository('RestBundle:ProductModel');
		//check if the user exist
		$user= $repository->findOneByLogin($login);
		if(empty($user)){
			$formatted = [
				'statut' => '404'
			];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}
		
		// check if the password is valid
		$encoder = $this->get('security.password_encoder');
        $isPasswordValid = $encoder->isPasswordValid($user, $password);
		if ($isPasswordValid) { // Le mot de passe est  correct
			
			$token = $user->getToken();
			$formattedToken=[
				'value' =>  $token->getValue(),
				'createdAt' => $token->getCreatedAt()
			]; 
		
			// retreving of livraison adresse
			$adresseDeLivraison = $user->getLivraisonAdress();
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
		
			//retreiving of the favorite
			$favorites=$user->getFavorites();
			$formattedFavorites =[];
			if(empty($favorites)){
				$formattedFavorites =[];
			}else
			{
				foreach ($favorites as $favorite) {
					$product = $repository2->findOneById($favorite->getIdProduct());
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
					 'idCategory' => $product->getNameCategory(),
					 'idScategory' => $product->getNameScategory(),
					 'idSScategory' => $product->getNameSScategory(),
					 'idImage'=> $product->getIdImage(),
					 'idBigImage1'=> $product->getIdBigImage1(),
					 'idBigImage2'=> $product->getIdBigImage2(),
					 'idBigImage3'=> $product->getIdBigImage3(),
					 'idBigImage4'=> $product->getIdBigImage4(),
					 'detail' => $formattedDetail,
					 'marque'=> $formattedMarque,
					 'productCar' => $formattedProductCar,
					 'modelCar' =>	$formattedModelCar,
					 'retailSale' => $formattedRetailSale,
					 'wholeSale'=> $formattedWholeSale
					
					]; 
			   
				    $formattedFavorites[]=[
					 'dateAjout'=> $favorite->getAddDate(),
					 'product'=> $formatted
				    ];
			
				}
		
		    }
			
			//recuperation des produit du panier
			$productPaniers=$user->getPanierProducts();
			$formattedProductPaniers =[];
			if(empty($productPaniers)){
				$formattedProductPaniers =[];
			}else
			{
				foreach ($productPaniers as $productPanier) {
			
					$product = $repository2->findOneById($productPanier->getIdProduct());
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
					 'idCategory' => $product->getNameCategory(),
					 'idScategory' => $product->getNameScategory(),
					 'idSScategory' => $product->getNameSScategory(),
					 'idImage'=> $product->getIdImage(),
					 'idBigImage1'=> $product->getIdBigImage1(),
					 'idBigImage2'=> $product->getIdBigImage2(),
					 'idBigImage3'=> $product->getIdBigImage3(),
					 'idBigImage4'=> $product->getIdBigImage4(),
					 'detail' => $formattedDetail,
					 'marque'=> $formattedMarque,
					 'productCar' => $formattedProductCar,
					 'modelCar' =>	$formattedModelCar,
					 'retailSale' => $formattedRetailSale,
					 'wholeSale'=> $formattedWholeSale
						]; 
				   
				   $formattedProductPaniers[]=[
					'type'=> $productPanier->getTypeVente(),
					'number'=>  $productPanier->getNumber(),
					'product'=> $formatted				
				   ];
				
				}
			}
			
			//recuperation des produit du panier
			$productBWMPaniers=$user->getPanierBWMProducts();
			$formattedProductBWMPaniers =[];
			if(empty($productBWMPaniers)){
				$formattedProductBWMPaniers =[];
			}else
			{
				foreach ($productBWMPaniers as $productBWMPanier) {
			
					$product = $repository2->findOneById($productBWMPanier->getIdProduct());
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
					 'idCategory' => $product->getNameCategory(),
					 'idScategory' => $product->getNameScategory(),
					 'idSScategory' => $product->getNameSScategory(),
					 'idImage'=> $product->getIdImage(),
					 'idBigImage1'=> $product->getIdBigImage1(),
					 'idBigImage2'=> $product->getIdBigImage2(),
					 'idBigImage3'=> $product->getIdBigImage3(),
					 'idBigImage4'=> $product->getIdBigImage4(),
					 'detail' => $formattedDetail,
					 'marque'=> $formattedMarque,
					 'productCar' => $formattedProductCar,
					 'modelCar' =>	$formattedModelCar,
					 'retailSale' => $formattedRetailSale,
					 'wholeSale'=> $formattedWholeSale
						]; 
				   
				   $formattedProductBWMPaniers[]=[
					'number'=>  $productBWMPanier->getNumber(),
					'product'=> $formatted				
				   ];
				
				}
			}

			
			
		 
			$payload = [ 
				'id'=> $user->getId(),
				'name' => $user->getName(),
				'firstName'=> $user->getFirstName(),
				'sex'=>$user->getSex(),
				'login' => $user->getLogin(),
				'pseudo'=> $user->getPseudo(),
				'dateDeNaiss'=>$user->getBirthDate(),
				'token'=>  $formattedToken,
				'livraisonAddress'=>$formattedAdresse,
				'favorites' => $formattedFavorites,
				'panier'=>	$formattedProductPaniers,
				'panierBWM'=> $formattedProductBWMPaniers
			]; 
		
			$response = [
				'statut' => '200',
				'response'=> $payload
			];
			
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($response);
        }
		
		// else password not valid for the login
	    $formatted = [
         'statut' => '404'
        ];
		header('Access-Control-Allow-Origin: *');
		
		return new JsonResponse($formatted);
	}
	
	/**
     * @GET("/user/initPassword/{login}")
	 * reinitialisation of password
     */
    public function initPasswordAction($login,Request $request)
    {  
	   
	    $repository = $this->get('doctrine_mongodb')->getManager()->getRepository('RestBundle:User');
		//check if the user exist
		$user= $repository->findOneByLogin($login);
		if(empty($user)){
			$formatted = [
				'statut' => '404'
			];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}
		
		//creer a new password
		$newPasss= base64_encode(random_bytes(2));
		$user->setPlainPassword($newPasss);
		$encoder = $this->get('security.password_encoder');
	    $encoded = $encoder->encodePassword($user, $user->getPlainPassword());
	    $user->setPassword( $encoded);
	   
	   // buiding of the token
	   $token = new AuthToken();
	   $token->setValue(base64_encode(random_bytes(50)));
	   $token->setCreatedAt(new \DateTime('now'));
	   $user->setToken($token);
	   
	   
	   $formattedToken=[
	   'value'=> $token->getValue(),
	   'createdAt'=> $token->getCreatedAt()
	   ];
	   
	   $dm = $this->get('doctrine_mongodb')->getManager();
       $dm->flush();
		 
		
		$payload = [ 
			'mot de passe'=> $newPasss
			
		];
		
		
		// send of the activation inside the email or a sms
		$message = \Swift_Message::newInstance()
       ->setSubject('reinitialisation du mot de passe')
       ->setFrom('mail@ounkoun.com')
       ->setTo($login)
       ->setBody($this->renderView(
            // app/Resources/views/Emails/registration.html.twig
            'RestBundle:Emails:reinitPassword.html.twig',array('password' => $newPasss)
        ),'text/html');
		
	    $this->get('mailer') ->send($message);
		$response = [
			'statut' => '200',
			'response'=> $payload
		];

		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($response);
		
		
	}
	
	/**
     * @POST("/user/update")
     */
	 public function updateUserAction(Request $request){
	 
		$dm = $this->get('doctrine_mongodb')->getManager();
		$repository = $dm->getRepository('RestBundle:User');
		
		$user= $repository->findOneByLogin($request->get('login'));
		if(empty($user)){
			$formatted = [
               'status' => '404'
            ];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}
		
		$name=$request->get('name');
		if($name!=null){
		 $user->setName($name);
		}
		
		$firstName=$request->get('firstName');
		if($firstName!=null){
		 $user->setFirstName($firstName);
		}
		
		$pieceNom = explode(" ", $request->get('name'));
	    $piecePrenom = explode(" ", $request->get('firstName'));
	    $user->setPseudo($piecePrenom[0]." ".$pieceNom[0]);
		
		$sex=$request->get('sex');
		if($sex!=null){
		 $user->setSex($sex);
		}
		
		
		$birthDate=$request->get('dateDeNaiss');
		if($birthDate!=null){
		 $user->setBirthDate($birthDate);
		}
		
		$dm->flush();
		
		// retreive and send as reponse the new user profil
		$user1= $repository->findOneByLogin($request->get('login'));
		$payload = [ 
				'id'=>$user1->getId(),
				'name' => $user1->getName(),
				'sex'=>$user1->getSex(),
				'birthDate'=>$user1->getBirthDate(),
				'phone'=>$user1->getPhone(),
				'firstName'=> $user1->getFirstName(),
				'login' => $user1->getLogin(),
				'pseudo'=> $user1->getPseudo(),
		]; 
		
		$response = [
				'statut' => '200',
				'response'=> $payload
		];
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($response);
		
	}
	
	/**
     * @POST("/user/modifyPassword")
	 * reinitialisation of password
     */
    public function modifyPasswordAction(Request $request)
    {  
	    $dm = $this->get('doctrine_mongodb')->getManager();
		$repository = $dm->getRepository('RestBundle:User');
		
	    $repository = $this->get('doctrine_mongodb')->getManager()->getRepository('RestBundle:User');
		//check if the user exist
		$user= $repository->findOneByLogin($request->get('login'));
		if(empty($user)){
			$formatted = [
				'statut' => '404'
			];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}
		
		// check if the password is valid
		 $encoder = $this->get('security.password_encoder');
         $isPasswordValid = $encoder->isPasswordValid($user, $request->get('oldPassword'));
		if ($isPasswordValid) { // Le mot de passe est  correct
			//modifier a new password
			$user->setPlainPassword($request->get('newPassword'));
			$encoder = $this->get('security.password_encoder');
			$encoded = $encoder->encodePassword($user, $user->getPlainPassword());
			$user->setPassword( $encoded);
			$dm = $this->get('doctrine_mongodb')->getManager();
            $dm->flush();
		 
			$formatted = [
				'statut' => '200'
			];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}else
		{
			$formatted = [
				'statut' => '4040'
			];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}
		
	}
	
	/**
     * @DELETE("/user/{id}")
     */
	 public function deleteUserAction(Request $request){
		
		
	}
	
	/**
     * @POST("/user/favorite")
	 * 
     */
    public function postFavoriteAction(Request $request)
    {  
	   $dm = $this->get('doctrine_mongodb')->getManager();
	   $repository = $this->get('doctrine_mongodb')->getManager()->getRepository('RestBundle:User');
		//check if the user exist
	   $user= $repository->findOneByLogin($request->get('login'));
		if(empty($user)){
			$formatted = [
				'statut' => '404'
			];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}
		
		$favorite= new Favorite();
		$favorite->setIdProduct($request->get('idProduct'));
		$favorite->setAddDate(new \DateTime('now'));
		$user->addFavorite($favorite);
		$dm->flush();
		 
		$formatted = [
			'statut' => '200'
		];
		
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
		
    }
	
	/**
     * @POST("/user/panier/detail")
	 * 
     */
    public function postPanierDetailAction(Request $request)
    {  
	   $dm = $this->get('doctrine_mongodb')->getManager();
	   $repository = $this->get('doctrine_mongodb')->getManager()->getRepository('RestBundle:User');
		//check if the user exist
	   $user= $repository->findOneByLogin($request->get('login'));
		if(empty($user)){
			$formatted = [
				'statut' => '404'
			];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}
		
		// if the product in already there: delete
		$products = $user->getPanierProducts();
		$quantity=0;
		foreach ($products as $product) {
	       if ($product->getIdProduct() == $request->get('idProduct') AND $product->getTypeVente()== $request->get('type') ){
		       $myproduct= $product;
			   $quantity=$product->getNumber();
		   }
		}
		if(!empty( $myproduct)){
			$user->removePanierProduct($myproduct);
			$dm->flush();
		}
		
		// we can now add the new product
		
			$product= new PanierProduct();
			$product->setIdProduct($request->get('idProduct'));
			$product->setNumber($request->get('number')+$quantity);
			$product->setTypeVente($request->get('type'));
			$user->addPanierProduct($product);
			$dm->flush();
		
		
		$formatted = [
			'statut' => '200'
		];
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);	
    }
	
	/**
     * @POST("/user/panier/BWM")
	 * 
     */
    public function postPanierBWMAction(Request $request)
    {  
	   $dm = $this->get('doctrine_mongodb')->getManager();
	   $repository = $this->get('doctrine_mongodb')->getManager()->getRepository('RestBundle:User');
		//check if the user exist
	   $user= $repository->findOneByLogin($request->get('login'));
		if(empty($user)){
			$formatted = [
				'statut' => '404'
			];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}
		
		// if the product in already there: delete
		$products = $user->getPanierBWMProducts();
		$quantity=0;
		foreach ($products as $product) {
	       if ($product->getIdProduct() == $request->get('idProduct')){
		       $myproduct= $product;
			   $quantity=$product->getNumber();
		   }
		}
		if(!empty( $myproduct)){
			$user->removePanierBWMProduct($myproduct);
			$dm->flush();
		}
		
		// we can now add the new product
		
			$product= new PanierBWMProduct();
			$product->setIdProduct($request->get('idProduct'));
			$product->setNumber($request->get('number')+$quantity);
			$user->addPanierBWMProduct($product);
			$dm->flush();
		
		
		$formatted = [
			'statut' => '200'
		];
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);	
    }
	
	/**
     * @POST("/user/panier/gros")
	 * 
     */
    public function postPanierGrosAction(Request $request)
    {  
	   $dm = $this->get('doctrine_mongodb')->getManager();
	   $repository = $this->get('doctrine_mongodb')->getManager()->getRepository('RestBundle:User');
		//check if the user exist
	   $user= $repository->findOneByLogin($request->get('login'));
	    if(empty($user)){
			$formatted = [
				'statut' => '404'
			];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}
		
		// if the product in already there: delete
		$products = $user->getPanierProductGros();
		$quantity=0;
		
		 foreach ($products as $product) {
	       if ($product->getName() == $request->get('name')){
		       $myproduct= $product;
			   $quantity= $product->getNumber();
		   }
		}
		
		if(!empty( $myproduct)){
			$user->removePanierProductGros($myproduct);
			$dm->flush();
		} 
		
		// we can now add the new product
		
		$product= new PanierProductGros();
		$PanierProduct = json_decode($request->get('product'),true);
		$tabPanierProducts=[];
			
		//foreach ($PanierProducts as $prod) {
			$item= new PanierProduct();
			$item->setIdProduct($PanierProduct['productId']);
			$item->setNumber($request->get('lotQuantity'));
			$product->addProduct($item);
		//}
			
		$product->setName($request->get('name'));
		if($request->get('iscustomized')=="true"){
				$product->setIsCustomized(true);
		}
			
		else{
			$product->setIsCustomized(false);
		}
			
			$product->setTailleLot($request->get('lotQuantity'));
			$product->setNumber($request->get('number') +  $quantity);
			$product->setTypeVente($request->get('type'));
			$user->addPanierProductGros($product);
			$dm->flush();
		
		
		$formatted = [
			'statut' => '200'
		];
		
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
		
    }
	
	/**
     * @POST("/user/panier/gros/personalize")
	 * 
     */
    public function postPanierGrosPersoAction(Request $request)
    {  
	   $dm = $this->get('doctrine_mongodb')->getManager();
	   $repository = $this->get('doctrine_mongodb')->getManager()->getRepository('RestBundle:User');
	   
	   //check if the user exist
	   $user= $repository->findOneByLogin($request->get('login'));
	    if(empty($user)){
			$formatted = [
				'statut' => '404'
			];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}
		
		// delete the product in database 
		$products = $user->getPanierProductGros();
		$quantity=0;
		
		foreach ($products as $product) {
	       if ($product->getName() == $request->get('oldName')){
		       $myproduct= $product;
			   $quantity= $product->getNumber();
		   }
		}
		
		if(!empty( $myproduct)){
			$user->removePanierProductGros($myproduct);
			$dm->flush();
		} 
		
		// we can now add the new product
		
		$product= new PanierProductGros();
		$PanierProducts = json_decode($request->get('products'),true);
		$tabPanierProducts=[];
			
		foreach ($PanierProducts as $prod) {
			$item= new PanierProduct();
			$item->setIdProduct($prod['id']);
			$item->setNumber($prod['number']['val']);
			$product->addProduct($item);
		}
			
		$product->setName($request->get('newName'));
		if($request->get('iscustomized')=="true"){
				$product->setIsCustomized(true);
		}
			
		else{
			$product->setIsCustomized(false);
		}
			
			$product->setTailleLot($request->get('lotQuantity'));
			$product->setNumber($quantity);
			$product->setTypeVente($request->get('type'));
			$user->addPanierProductGros($product);
			$dm->flush();
		
		
		$formatted = [
			'statut' => '200'
		];
		
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
		
    }
	
	
	/**
     * @POST("/user/favorite/delete")
	 * delete favorite
     */
    public function deleteFavoriteAction(Request $request)
    {  
	   
	  $dm= $this->get('doctrine_mongodb')->getManager();
	  $repository = $dm->getRepository('RestBundle:User');
	 
	  //check if the user exist
	   $user= $repository->findOneByLogin($request->get('login'));
		if(empty($user)){
			$formatted = [
				'statut' => '404'
			];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}
		 $favorites = $user->getFavorites();
		 
		foreach ($favorites as $favorite) {
	       if ($favorite->getIdProduct() == $request->get('idProduct')){
		       $myfavorite= $favorite;
			   
		   }
		}
	
	    if(empty($myfavorite)){
			$formatted = [
               'statut' => '404'
         ];
			
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
	   }
	   
		// else we can delete
		$user->removeFavorite($myfavorite);
        $dm->flush();
		$formatted = [
           'statut' => '200'
		];
		
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
		
    }
	
	/**
     * @POST("/user/panier/delete/gros")
	 * 
     */
    public function deletePanierGrosAction(Request $request)
    {  
	   
	  $dm= $this->get('doctrine_mongodb')->getManager();
	  $repository = $dm->getRepository('RestBundle:User');
	 
	  //check if the user exist
	   $user= $repository->findOneByLogin($request->get('login'));
		if(empty($user)){
			$formatted = [
				'statut' => '404'
			];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}
		
		$products = $user->getPanierProductGros();
		foreach ($products as $product) {
	       if ($product->getName() == $request->get('nameProduct')){
		       $myproduct= $product;  
		   }
		}
	
	    if(empty($myproduct)){
			$formatted = [
               'statut' => '404'
         ];
			
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
	   }
	   
		// else we can delete
		$user->removePanierProductGros($myproduct);
        $dm->flush();
		$formatted = [
           'statut' => '200'
		];
		
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
		
    }
	
	/**
     * @POST("/user/panier/gros/modifNumber")
	 * 
     */
    public function modiNumberPanierGrosAction(Request $request)
    {  
	   
	  $dm= $this->get('doctrine_mongodb')->getManager();
	  $repository = $dm->getRepository('RestBundle:User');
	 
	  //check if the user exist
	   $user= $repository->findOneByLogin($request->get('login'));
		if(empty($user)){
			$formatted = [
				'statut' => '404'
			];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}
		
		$products = $user->getPanierProductGros();
		foreach ($products as $product) {
	       if ($product->getName() == $request->get('nameProduct')){
		       $myproduct= $product;  
		   }
		}
	
	    if(empty($myproduct)){
			$formatted = [
               'statut' => '4041'
         ];
			
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
	   }
	   
		// else we can modify the number
		$myproduct->setNumber($request->get('number'));
        $dm->flush();
		$formatted = [
           'statut' => '200'
		];
		
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
		
    }
	
	/**
     * @POST("/user/panier/delete/detail")
	 * delete favorite
     */
    public function deletePanierDetailAction(Request $request)
    {  
	   
	  $dm= $this->get('doctrine_mongodb')->getManager();
	  $repository = $dm->getRepository('RestBundle:User');
	 
	  //check if the user exist
	   $user= $repository->findOneByLogin($request->get('login'));
		if(empty($user)){
			$formatted = [
				'statut' => '404'
			];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}
		$products = $user->getPanierProducts();
		 
		foreach ($products as $product) {
	       if ($product->getidProduct() == $request->get('idProduct')){
		       $myproduct= $product;
			   
		   }
		}
	
	    if(empty($myproduct)){
			$formatted = [
               'statut' => '404'
         ];
			
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
	   }
	   
		// else we can delete
		$user->removePanierProduct($myproduct);
        $dm->flush();
		$formatted = [
           'statut' => '200'
		];
		
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
		
    }
	
	/**
     * @POST("/user/panier/delete/BWM")
	 * delete favorite
     */
    public function deletePanierBWMAction(Request $request)
    {  
	   
	  $dm= $this->get('doctrine_mongodb')->getManager();
	  $repository = $dm->getRepository('RestBundle:User');
	 
	  //check if the user exist
	   $user= $repository->findOneByLogin($request->get('login'));
		if(empty($user)){
			$formatted = [
				'statut' => '404'
			];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}
		$products = $user->getPanierBWMProducts();
		 
		foreach ($products as $product) {
	       if ($product->getidProduct() == $request->get('idProduct')){
		       $myproduct= $product;
			   
		   }
		}
	
	    if(empty($myproduct)){
			$formatted = [
               'statut' => '404'
         ];
			
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
	   }
	   
		// else we can delete
		$user->removePanierBWMProduct($myproduct);
        $dm->flush();
		$formatted = [
           'statut' => '200'
		];
		
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
		
    }
	
	/**
     * @POST("/user/panier/detail/modifNumber")
	 * modify number
     */
    public function ModifPanierDetailNumberAction(Request $request)
    {  
	   
	  $dm= $this->get('doctrine_mongodb')->getManager();
	  $repository = $dm->getRepository('RestBundle:User');
	 
	  //check if the user exist
	   $user= $repository->findOneByLogin($request->get('login'));
		if(empty($user)){
			$formatted = [
				'statut' => '4041'
			];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}
		$products = $user->getPanierProducts();
		 
		foreach ($products as $product) {
	       if ($product->getidProduct() == $request->get('idProduct')){
		       $myproduct= $product;
			   
		   }
		}
	
	    if(empty($myproduct)){
			$formatted = [
               'statut' => '4042'
         ];
			
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
	   }
	   
		// else we can modify number
		 $myproduct->setNumber($request->get('number'));
        $dm->flush();
		$formatted = [
           'statut' => '200'
		];
		
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
		
    }
	
	/**
     * @POST("/user/panier/BWM/modifNumber")
	 * modify number
     */
    public function ModifPanierBWMNumberAction(Request $request)
    {  
	   
	  $dm= $this->get('doctrine_mongodb')->getManager();
	  $repository = $dm->getRepository('RestBundle:User');
	 
	  //check if the user exist
	   $user= $repository->findOneByLogin($request->get('login'));
		if(empty($user)){
			$formatted = [
				'statut' => '4041'
			];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}
		$products = $user->getPanierBWMProducts();
		 
		foreach ($products as $product) {
	       if ($product->getidProduct() == $request->get('idProduct')){
		       $myproduct= $product;
			   
		   }
		}
	
	    if(empty($myproduct)){
			$formatted = [
               'statut' => '404'
            ];
			
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
	    }
	   
		// else we can modify number
		$myproduct->setNumber($request->get('number'));
        $dm->flush();
		$formatted = [
           'statut' => '200'
		];
		
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
		
    }
	
	
	/**
     * @GET("/user/favorite/{login}")
	 * url-parm: id(String) 
	 * return of favorite product
     */
	 public function getFavoritesAction($login,Request $request)
     { 
		$dm= $this->get('doctrine_mongodb')->getManager();
	    $repository1 = $dm->getRepository('RestBundle:User');
		$repository2 = $dm->getRepository('RestBundle:ProductModel');
	 
	  //check if the user exist
	   $user= $repository1->findOneByLogin($request->get('login'));
		if(empty($user)){
			$formatted = [
				'statut' => '404'
			];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}
		
		//retreiving of the favorite
		$favorites=$user->getFavorites();
		$formattedFavorites =[];
		if(empty($favorites)){
			$formattedFavorites =[];
		}
		else
		{
			foreach ($favorites as $favorite) {
		
				//$favorite->getIdProduct;
				$product = $repository2->findOneById($favorite->getIdProduct());
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
				 'retailSale' => $formattedRetailSale,
				 'wholeSale'=> $formattedWholeSale
					]; 
			   
			   $formattedFavorites[]=[
				'dateAjout'=> $favorite->getAddDate(),
				'product'=> $formatted
			   ];
			
			}
		
		}
		
		
        $data=[
		 'favorites'=> $formattedFavorites
		]; 
		
		 $response = [
        'statut' => '200',
		'data' => $data
		];
		
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($response);
	}
	
	/**
     * @GET("/user/panier/detail/{login}")
	 * url-parm: id(String) 
	 * return of favorite product
     */
	 public function getPanierAction($login,Request $request)
     { 
		$dm= $this->get('doctrine_mongodb')->getManager();
	    $repository1 = $dm->getRepository('RestBundle:User');
		$repository2 = $dm->getRepository('RestBundle:ProductModel');
	 
	  //check if the user exist
	   $user= $repository1->findOneByLogin($request->get('login'));
		if(empty($user)){
			$formatted = [
				'statut' => '404'
			];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}
		
	  //else retreiving of the products du panier
		$productPaniers=$user->getPanierProducts();
		$formattedProductPaniers =[];
		if(empty($productPaniers)){
			$formattedProductPaniers =[];
		}else
		{
			foreach ($productPaniers as $productPanier) {
		
				$product = $repository2->findOneById($productPanier->getIdProduct());
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
				 'weight' => $product->getWeight(),
				 'idImage'=> $product->getIdImage(),
				 'idBigImage1'=> $product->getIdBigImage1(),
				 'idBigImage2'=> $product->getIdBigImage2(),
				 'idBigImage3'=> $product->getIdBigImage3(),
				 'idBigImage4'=> $product->getIdBigImage4(),
				 'detail' => $formattedDetail,
				 'marque'=> $formattedMarque,
				 'productCar' => $formattedProductCar,
				 'modelCar' =>	$formattedModelCar,
				 'retailSale' => $formattedRetailSale,
				 'wholeSale'=> $formattedWholeSale
				]; 
			   
			   $formattedProductPaniers[]=[
				'type'=> $productPanier->getTypeVente(),
				'number'=>  $productPanier->getNumber(),
				'product'=> $formatted				
			   ];
			
			}
		
		}
		 
        $data=[
		 'products'=>  $formattedProductPaniers
		];
		
		 $response = [
        'statut' => '200',
		'data' => $data
		];
		
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($response);
	}
	
	/**
     * @GET("/user/panier/BWM/{login}")
	 * url-parm: id(String) 
	 * return of favorite product
     */
	 public function getPanierBWMAction($login,Request $request)
     { 
		$dm= $this->get('doctrine_mongodb')->getManager();
	    $repository1 = $dm->getRepository('RestBundle:User');
		$repository2 = $dm->getRepository('RestBundle:ProductModel');
	 
	  //check if the user exist
	   $user= $repository1->findOneByLogin($request->get('login'));
		if(empty($user)){
			$formatted = [
				'statut' => '404'
			];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}
		
	  //else retreiving of the products du panier
		$productPaniers=$user->getPanierBWMProducts();
		$formattedProductPaniers =[];
		if(empty($productPaniers)){
			$formattedProductPaniers =[];
		}else
		{
			foreach ($productPaniers as $productPanier) {
		
				$product = $repository2->findOneById($productPanier->getIdProduct());
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
				
				$BWMSale = $product->getBuyWithMeSale();
				$formattedBWMSale=[];
				if($BWMSale!= null){
					//$date= $retailSale->getEndPromotionDate();
					$formattedBWMSale=[
						'price'=> $BWMSale->getPrice(),
						'lotQuantity'=> $BWMSale->getLotQuantity(),
						'duree'=> $BWMSale->getDuree()
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
				 'retailSale' => $formattedRetailSale,
				 'BWMSale'=> $formattedBWMSale
				]; 
			   
			   $formattedProductPaniers[]=[
				'type'=> "BWM",
				'number'=>  $productPanier->getNumber(),
				'product'=> $formatted				
			   ];
			
			}
		
		}
		 
        $data=[
		 'products'=>  $formattedProductPaniers
		];
		
		 $response = [
        'statut' => '200',
		'data' => $data
		];
		
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($response);
	}
	
	/**
     * @GET("/user/panier/gros/{login}")
	 * url-parm: login
	 * 
     */
	 public function getPanierGrosAction($login,Request $request)
     { 
		$dm= $this->get('doctrine_mongodb')->getManager();
	    $repository1 = $dm->getRepository('RestBundle:User');
		$repository2 = $dm->getRepository('RestBundle:ProductModel');
	 
	  //check if the user exist
	   $user= $repository1->findOneByLogin($request->get('login'));
		if(empty($user)){
			$formatted = [
				'statut' => '404'
			];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}
		
	  //else retreiving of the products du panier
		$productPaniers=$user->getPanierProductGros();
		$formattedProductPaniers =[];
		if(empty($productPaniers)){
			$formattedProductPaniers =[];
		}
		else
		{
			foreach ($productPaniers as $productPanier) {
				$products = $productPanier->getPanierProducts();
				$formatted= [];
				foreach ($products as $prod){
				
					$product = $repository2->findOneById($prod->getIdProduct());
					$productCaracteristics = $product->getProductCaracteristics();
					$modelCaracteristics = $product->getModelCaracteristics();
					$details = $product->getDetails();
					$formattedProductCar =[];
					$formattedModelCar =[];
					$formattedDetail=[];
			
				    $marque= $product->getMarque();
				    $formattedMarque=[
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
				 
				 
					$formatted [] =[
					 'id'=>	$product->getId(),
					 'name' => $product->getName(),
					 'number'=> $prod->getNumber(),
					 'wholeSale'=> $formattedWholeSale ,
					 'idImage'=> $product->getIdImage(),
					 'Product'=> $product->getIdProduit(),
					 'quantity'=> $product->getQuantity()
					 /* 'description' => $product->getDescription(), */
					 /* 'idCategory' => $product->getNameCategory(),
					 'idScategory' => $product->getNameScategory(),
					 'idSScategory' => $product->getNameSScategory(), */ 
					 /* 'idBigImage1'=> $product->getIdBigImage1(),
					 'idBigImage2'=> $product->getIdBigImage2(),
					 'idBigImage3'=> $product->getIdBigImage3(),
					 'idBigImage4'=> $product->getIdBigImage4(), */
					 /* 'detail' => $formattedDetail,
					 'marque'=> $formattedMarque,
					 'productCar' => $formattedProductCar,
					 'modelCar' =>	$formattedModelCar,
					 'retailSale' => $formattedRetailSale,
					*/
					]; 
			    }
				
			   $formattedProductPaniers[]=[
				'id'=> $productPanier->getId(),
				'name'=> $productPanier->getName(),
				'type'=> $productPanier->getTypeVente(),
				'number'=>  $productPanier->getNumber(),
				'tailleLot'=> $productPanier->getTailleLot(),
				'isCustomized'=> $productPanier->getIsCustomized(),
				'product'=> $formatted				
			   ];
			
			}
		
		}
		 
        $data=[
		 'products'=>  $formattedProductPaniers
		];
		
		 $response = [
        'statut' => '200',
		'data' => $data
		];
		
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($response);
	}
	
	/**
     * @POST("/user/adresseLivraison")
	 * 
     */
    public function postLivraisonAdressAction(Request $request)
    {  
	   $dm = $this->get('doctrine_mongodb')->getManager();
	   $repository = $this->get('doctrine_mongodb')->getManager()->getRepository('RestBundle:User');
		//check if the user exist
	   $user= $repository->findOneByLogin($request->get('login'));
		if(empty($user)){
			$formatted = [
				'statut' => '404'
			];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}
		
		$adresse= new LivraisonAdress();
		$adresse->setNameReceptionist($request->get('name'));
		$adresse->setSecondNameReceptionist($request->get('firstName'));
		$adresse->setTelephone1Receptionist($request->get('tel1'));
		$adresse->setTelephone2Receptionist($request->get('tel2'));
		$adresse->setRegion($request->get('region'));
		$adresse->setTown($request->get('town'));
		$adresse->setAdresse($request->get('address'));
		$user->setLivraisonAdress($adresse);
		$dm->flush();
		
		$formattedAdresse=[
			'name' =>$request->get('name'),
			'firstName' => $request->get('firstName'),
			'tel1'=>$request->get('tel1'),
			'tel2'=>$request->get('tel2'),
			'region'=>$request->get('region'),
			'town'=>$request->get('town'),
			'adresse'=>$request->get('address')
		];
		
		 
		$formatted = [
			'statut' => '200',
			'livraisonadresse'=>$formattedAdresse
		];
		
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
		
    }
	
	/**
     * @GET("/user/command/{login}")
	 * url-parm: id(String) 
	 * return of favorite product
     */
	 public function getCommandAction($login,Request $request)
     { 
		$dm= $this->get('doctrine_mongodb')->getManager();
	    $repository1 = $dm->getRepository('RestBundle:User');
		$repository2 = $dm->getRepository('RestBundle:ProductModel');
	 
	   //check if the user exist
	   $user= $repository1->findOneByLogin($request->get('login'));
		if(empty($user)){
			$formatted = [
				'statut' => '404'
			];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}
		
		$qb= $this->get('doctrine_mongodb')
						->getManager()
						->createQueryBuilder('RestBundle:Commande')
						->field('login')->equals($login)
						->sort('commandDate','desc');
						
		
		$commands= $qb->getQuery()->execute();
		$i=0;
		$formattedCommand=[];
		$formattedAdresse=[];
		$formatteLivraison=[];
		$relaisAdress=[];
		$formatted=[];
		$number=0;


		foreach($commands as $command){
			$number++;
		}


		foreach($commands as $command){
		
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
					$retailSale = $product->getRetailSale();
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
			
			$livraison=  $command->getLivraison();
			$formattedLivraison= [
				'type'=> $livraison->getType(),
				'delais'=> $livraison->getDelais(),
				'price'=> $livraison->getPrice()
			];

			if($livraison->getType()==2){
				$relais =  $command->getRelaisAdress();
				$relaisAdress= [
					'nom'=> $relais->getNom(),
					'quartier'=> $relais->getQuartier(),
					'emplacement'=> $relais->getEmplacement()
				];
			}
			
			$formattedCommand[]= [
			   'reference' => $command->getReference(),
			   'dateCreation' => $command->getCommandDate(),
			   'isPaided' => $command->getIsPaid(),
			   'isShipped' => $command->getIsShipped(),
			   'livraisonAdress' => $formattedAdresse,
			   'relaisAdress' => $relaisAdress,
			   'livraison' => $formattedLivraison,
			   'products' => $formattedProducts
			];
		 
			$i++;
		}
		 
		
		
		 $response = [
        'statut' => '200',
        'number' => $number++,
		'data' =>  $formattedCommand
		];
		
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($response);
	}
	
	/**
     * @GET("/user/commandBWM/{login}")
	 * url-parm: id(String) 
	 * return of favorite product
     */
	public function getCommandBWMAction($login,Request $request)
    { 
		$dm= $this->get('doctrine_mongodb')->getManager();
	    $repository1 = $dm->getRepository('RestBundle:User');
		$repository2 = $dm->getRepository('RestBundle:ProductModel');
	 
	  //check if the user exist
	   $user= $repository1->findOneByLogin($request->get('login'));
		if(empty($user)){
			$formatted = [
				'statut' => '404'
			];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}
		
		$qb= $this->get('doctrine_mongodb')
						->getManager()
						->createQueryBuilder('RestBundle:CommandeBWM')
						->field('login')->equals($login)->sort('commandDate','desc');
						
		
		$commands= $qb->getQuery()->execute();
		$i=0;
		$formattedCommand=[];
		$formattedAdresse=[];
		$formatted=[];
		foreach($commands as $command){
		
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
					
					$BWMSale = $product->getBuyWithMeSale();
					$formattedBWMSale=[];
					if($BWMSale!= null){
						
						$formattedRetailSale=[
							'price'=> $BWMSale->getPrice(),
							'lotQuantity'=> $BWMSale->getLotQuantity(),
							'duree'=> $BWMSale->getDuree()
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
					 'retailSale' => $formattedRetailSale,
					 'BWMSale'=> $formattedBWMSale
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
			   'reference' => $command->getReference(),
			   'dateCreation' => $command->getCommandDate(),
			   'isPaided' =>$command->getIsPaid(),
			   'isShipped'=>$command->getIsShipped(),
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
	
		
}
