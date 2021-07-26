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
use RestBundle\Document\Enchere;
use RestBundle\Document\User;
use RestBundle\Document\HistoriqueEnchere;
use RestBundle\Document\Detail;
use RestBundle\Document\EnchereEmbedded;
use \DateTime;

class EnchereController extends Controller
{

	public function __construct(){
    	date_default_timezone_set("Africa/Douala");
    }

	/**
     * @POST("/enchere")
	 * body-parm: numEnchere(String), etat(string), statut(String),initDate(String)
	 * create a new product
     */
    public function postEnchereAction(Request $request)
    { 
	  
	  $dm= $this->get('doctrine_mongodb')->getManager();
	  $repository = $dm->getRepository('RestBundle:Enchere');
	  

	  // checking if the product is already exist
	   $enchere1 = $repository->findOneByNumEnchere($request->get('numEnchere'));
	   if(!empty($enchere1)){
			$formatted = [
               'statut' => '404'
            ];
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
	   }
	      
	   //else we can created this enchere
	   $enchere = new Enchere();
	   $enchere->setNumEnchere($request->get('numEnchere'));
	   $enchere->setName($request->get('name'));
	   $enchere->setEtat($request->get('etat'));
	   $enchere->setStatut("En cours");
	   $datetime = new \DateTime('now', new \DateTimeZone('Africa/Douala'));
	   $enchere->setInitDate($datetime);
	   $startDate = date('Y-m-d H:i:s');
	   $time = $request->get('time');
       $nextDate = date("Y-m-d H:i:s", strtotime("$startDate  +$time day"));
	   $enchere->setCloseDate($nextDate); 
	   $enchere->setInitPrice($request->get('initPrice')); 
	   $enchere->setIdImage($request->get('idImage')); 
	   $enchere->setIdBigImage1($request->get('idImage1')); 
	   $enchere->setIdBigImage2($request->get('idImage2'));
	   $enchere->setIdBigImage3($request->get('idImage3'));
	   $enchere->setIdBigImage4($request->get('idImage4'));
	   $enchere->setDescription($request->get('description'));
	   $enchere->setCategory($request->get('category'));
	   $enchere->setTime($request->get('time'));
	   $enchere->setQuantity($request->get('quantity'));

	   //json_decode($details,true)
	   $details = $request->get('detail');
	   foreach (json_decode($details,true) as $detail ) {
	     	$d= new Detail();
		 	$d->setName($detail['name']);
		 	$d->setValue($detail['value']);  
	     	$enchere->addDetail($d);
	   }
	
	   	$dm->persist($enchere);
       	$dm->flush();
	   
	   	$formatted = [
          'statut' => '200'
       	];

	   	header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
	}
	
	/**
     * @POST("/enchere/modify")
	 * body-parm: numEnchere(String), etat(string), statut(String),initDate(String),closeDate(String),initPrice(string), idImage, IdBigImage1, IdBigImage2, IdBigImage3,idBigImage4)
	 * create a new product
     */
    public function modifyEnchereAction(Request $request)
    { 
	  

	  $dm= $this->get('doctrine_mongodb')->getManager();
	  $repository = $dm->getRepository('RestBundle:Enchere');
	  $repository2 = $dm->getRepository('RestBundle:Image');

	  
	  // retrieving the product
	   $enchere = $repository->findOneByNumEnchere($request->get('numEnchere'));
	   if(empty( $enchere )){
			$formatted = [
               'statut' => '404'
            ];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
	   }
	      
	   

	   if(!empty($request->get('name'))){
		$enchere->setName($request->get('name'));	
	   }


	   if(!empty($request->get('statut'))){
		 $enchere->setStatut($request->get('statut'));	
	   }


	   if(!empty($request->get('etat'))){
		 $enchere->setEtat($request->get('etat'));	
	   }

	   if(!empty($request->get('initDate'))){
		 $enchere->setInitDate($request->get('initDate'));	
	   }

	   if(!empty($request->get('closeDate'))){
		 $enchere->setCloseDate($request->get('closeDate'));	
	   }

	   if(!empty($request->get('initPrice'))){
		 $enchere->setInitPrice($request->get('initPrice')); 	
	   }

	   

	   	 if($enchere->getIdImage()!= $request->get('idImage')){
			$image = $repository2->findOneById($enchere->getIdImage());
	  		$dm->remove($image);
	  		$dm->flush();
		 }
		 $enchere->setIdImage($request->get('idImage')); 	
	  

	   if($request->get('idImage1')!= 'null'){

	   	 if($enchere->getIdBigImage1()!= $request->get('idImage1')){
			$image = $repository2->findOneById($enchere->getIdBigImage1());
	  		$dm->remove($image);
	  		$dm->flush();
		 }
		 $enchere->setIdBigImage1($request->get('idImage1'));  	
	   }

	   if($request->get('idImage2')!= 'null'){

	   	 if($enchere->getIdBigImage2()!= $request->get('idImage2')){
			$image = $repository2->findOneById($enchere->getIdBigImage2());
	  		$dm->remove($image);
	  		$dm->flush();
		 }
		 $enchere->setIdBigImage2($request->get('idImage2'));  	
	   }

	   if($request->get('idImage3')!= 'null'){
	   	 if($enchere->getIdBigImage3()!= $enchere->getIdBigImage3()){
			$image = $repository2->findOneById($enchere->getIdBigImage3());
	  		$dm->remove($image);
	  		$dm->flush();
		 }
		 $enchere->setIdBigImage3($request->get('idImage3')); 	
	   }

	   if($request->get('idImage4')!= 'null'){
	   	 if($enchere->getIdBigImage4()!= $enchere->getIdBigImage4()){
			$image = $repository2->findOneById($enchere->getIdBigImage4());
	  		$dm->remove($image);
	  		$dm->flush();
		 }
		 $enchere->setIdBigImage4($request->get('idImage4'));  	
	   }
	  
	  
	   if(!empty($request->get('detail'))){

	   		$details  = $enchere->getDetails();
			foreach ($details as $detail) {
		      $enchere->removeDetail($detail);
			  $dm->flush();
			}
	    	
		    $newDetails = $request->get('detail');

			foreach (json_decode($newDetails,true) as $detail ) {
			    $d= new Detail();
				$d->setName($detail['name']);
				$d->setValue($detail['value']);  
			    $enchere->addDetail($d);	
			}

	   }

	    
       $dm->flush();
	   $formatted = [
               'statut' => '200'
       ];
	   
	   header('Access-Control-Allow-Origin: *');
	   return new JsonResponse($formatted);
	}


	/**
     * @POST("/enchere/historique")
	 * create a new product
     */
    public function addHistoriqueAction(Request $request)
    { 
	    function fakeip()  
         {  
        return long2ip( mt_rand(0, 65537) * mt_rand(0, 65535) );   
        }

        $shareUrl="https://www.ounkoun.com/produit-enchere.php?numEnchere=".$request->get('numEnchere');
	    $dm= $this->get('doctrine_mongodb')->getManager();
	    $repository = $dm->getRepository('RestBundle:Enchere');
	    $repository1 = $dm->getRepository('RestBundle:User');
	    $historique  = json_decode($request->get('historique'),true);
	  
	    $enchere = $repository->findOneByNumEnchere($request->get('numEnchere'));
	    if(empty( $enchere )){
			$formatted = [
               'statut' => '404'
            ];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
	    }
	     
	  
	    if(!empty($request->get('historique'))){

	   		$datetime = new \DateTime('now', new \DateTimeZone('Africa/Douala'));
	   		$user = $repository1->findOneByLogin($historique['idUser']);
		  	
			$embeddedEnchere = new EnchereEmbedded();
			$embeddedEnchere->setNumEnchere($request->get('numEnchere'));
			$embeddedEnchere->setAddDate($datetime);
			$user->addEnchere($embeddedEnchere);
			$dm->flush();
		  	

	   		$historique  = json_decode($request->get('historique'),true);
			$newHistorique = new HistoriqueEnchere();
			$newHistorique->setPrice($historique['prix']);
			$newHistorique->setDate( $datetime); 
			$newHistorique->setIdUser($historique['idUser']);
			$enchere->addHistoriques($newHistorique);	
			$dm->flush();

			if(preg_match("#^([A-Za-z0-9])+\@([A-Za-z0-9])+\.([A-Za-z]{2,4})$#", $historique['idUser'])){
				$message = \Swift_Message::newInstance()
			   ->setSubject(' Enchère enregistré')
			   ->setFrom('mail@ounkoun.com')
			   ->setTo($historique['idUser'])
			   ->setBody($this->renderView(
					// app/Resources/views/Emails/registration.html.twig
					'RestBundle:Emails:confirmEnchere.html.twig',array('numEncher' => $request->get('numEnchere'),'shareUrl'=>$shareUrl)
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
				<text> Votre enchère sur la vente ".$request->get('numEnchere')." a bien été enregistreé. ".$shareUrl."
				</text>
				</message>
				<recipients>
				<gsm>237".$historique['idUser']."</gsm>
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
	    }

        
	    $formatted = [
            'statut' => '200'
        ];
	   
	    header('Access-Control-Allow-Origin: *');
	    return new JsonResponse($formatted);
	}


	
	/**
     * @POST("/enchere/delete")
	 * body-parm: idProduit(String)
	 * modify a new product model
     */
    public function deleteEnchereAction(Request $request)
    { 
		
		$dm= $this->get('doctrine_mongodb')->getManager();
		$repository1 = $dm->getRepository('RestBundle:Enchere');
		$repository2 = $dm->getRepository('RestBundle:Image');
		
		//retrieving of the model
		$enchere =  $repository1->findOneByNumEnchere($request->get('numEnchere'));
		if(empty($enchere)){
			$formatted = [
               'statut' => '404'
            ];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}

		// supression des image
		if($enchere->getIdImage()!= 'null'){
			$image = $repository2->findOneById($enchere->getIdImage());
	  		$dm->remove($image);
	  		$dm->flush();
		}

		if($enchere->getIdBigImage1()!= 'null'){
			$image = $repository2->findOneById($enchere->getIdBigImage1());
	  		$dm->remove($image);
	  		$dm->flush();
		}

		if($enchere->getIdBigImage2() != 'null'){
			$image = $repository2->findOneById($enchere->getIdBigImage2());
	  		$dm->remove($image);
	  		$dm->flush();
		}

		if($enchere->getIdBigImage3()!= 'null'){
			$image = $repository2->findOneById($enchere->getIdBigImage3());
	  		$dm->remove($image);
	  		$dm->flush();
		}

		if($enchere->getIdBigImage4()!= 'null'){
			$image = $repository2->findOneById($enchere->getIdBigImage4());
	  		$dm->remove($image);
	  		$dm->flush();
		}
		
		
		$dm->remove($enchere);
		$dm->flush();
		$formatted = [
			'statut' => '200'
		];
		header('Access-Control-Allow-Origin: *');
	   	return new JsonResponse($formatted);
	}

	
	/**
     * @GET("/enchere/{nom_category}/{page}")
	 * return all the categories
     */
    public function getAllEnchereAction($nom_category,$page,Request $request)
    {	

    	$dm= $this->get('doctrine_mongodb')->getManager();
		$repository = $dm->getRepository('RestBundle:Enchere');
		$allEncheres = $repository->findAll();

		$qb1= $this->get('doctrine_mongodb')
						->getManager()
						->createQueryBuilder('RestBundle:Enchere');

		if( $nom_category != "all" ){
			$qb1 = $qb1->field('category')->equals($nom_category);
		}

		$query1 = $qb1->getQuery();
		$encheres1=$query1->execute();
		$taille= sizeof($encheres1);


    	$qb= $this->get('doctrine_mongodb')
						->getManager()
						->createQueryBuilder('RestBundle:Enchere')
						->skip(20*($page-1))
					    ->limit(20);

		if( $nom_category != "all" ){
			$qb = $qb->field('category')->equals($nom_category);
		}

		$query = $qb->getQuery();
		$encheres=$query->execute();
		

		
		$formatted = [];
		$response=[];
		$formattedDetail=[];
		$formattedHsitorique =[];
	 
    	foreach ($encheres as $enchere) {
		     
			$details = $enchere->getDetails();;
			$formattedDetail=[];
			foreach($details as $detail ){
				$formattedDetail[] = [
				'name' => $detail->getName(),
				'value' => $detail->getValue()
				];
			}

			$historiques = $enchere->getHistoriques();;
			$formattedHistorique=[];
			foreach($historiques as $historique ){
				$formattedHistorique[] = [
					'idUser' => $historique->getIdUser(),
					'price' => $historique->getPrice(),
					'date' => $historique-> getDate()
				];
			}
		
			$formatted[] = [
				'id' => $enchere->getId(),
				'numEnchere' => $enchere->getNumEnchere(),
				'name'=> $enchere->getName(),
				'etat' => $enchere->getEtat(),
				'statut' => $enchere->getStatut(),
				'initDate' => $enchere->getInitDate(),
				'closeDate' => $enchere->getCloseDate(),
				'initPrice' => $enchere->getInitPrice(),
				'description' => $enchere->getDescription(),
				'quantity' => $enchere->getQuantity(),
				'time' => $enchere->getTime(),
				'idImage' => $enchere->getIdImage(),
				'idImage1' => $enchere->getIdBigImage1(),
				'idImage2' => $enchere->getIdBigImage2(),
				'idImage3' => $enchere->getIdBigImage3(),
				'idImage4' => $enchere->getIdBigImage4(),
				'detail' => $formattedDetail,
				'historique' => $formattedHistorique
			];

		}

		$response=[
			'statut' => '200',
			'taille' => $taille,
			'data'=> $formatted
		];

	  	header('Access-Control-Allow-Origin: *');
	  	return new JsonResponse($response);
	}


	/**
     * @GET("/enchere/all")
	 * return all the categories
     */
    public function getAllAllEnchereAction(Request $request)
    {	

    	$dm= $this->get('doctrine_mongodb')->getManager();
		$repository = $dm->getRepository('RestBundle:Enchere');
		$allEncheres = $repository->findAll();
		$taille = sizeof($allEncheres);

    	$qb= $this->get('doctrine_mongodb')
						->getManager()
						->createQueryBuilder('RestBundle:Enchere');
		$query = $qb->getQuery();
		$encheres=$query->execute();
		
		$formatted = [];
		$response=[];
		$formattedDetail=[];
		$formattedHsitorique =[];
	 
    	foreach ($encheres as $enchere) {
		     
			$details = $enchere->getDetails();;
			$formattedDetail=[];
			foreach($details as $detail ){
				$formattedDetail[] = [
				'name' => $detail->getName(),
				'value' => $detail->getValue()
				];
			}

			$historiques = $enchere->getHistoriques();;
			$formattedHistorique=[];
			foreach($historiques as $historique ){
				$formattedHistorique[] = [
					'idUser' => $historique->getIdUser(),
					'price' => $historique->getPrice(),
					'date' => $historique-> getDate()
				];
			}
		
			$formatted[] = [
				'id' => $enchere->getId(),
				'numEnchere' => $enchere->getNumEnchere(),
				'name'=> $enchere->getName(),
				'etat' => $enchere->getEtat(),
				'statut' => $enchere->getStatut(),
				'initDate' => $enchere->getInitDate(),
				'closeDate' => $enchere->getCloseDate(),
				'initPrice' => $enchere->getInitPrice(),
				'description' => $enchere->getDescription(),
				'quantity' => $enchere->getQuantity(),
				'time' => $enchere->getTime(),
				'idImage' => $enchere->getIdImage(),
				'idImage1' => $enchere->getIdBigImage1(),
				'idImage2' => $enchere->getIdBigImage2(),
				'idImage3' => $enchere->getIdBigImage3(),
				'idImage4' => $enchere->getIdBigImage4(),
				'detail' => $formattedDetail,
				'historique' => $formattedHistorique
			];

		}

		$response=[
			'statut' => '200',
			'taille' => $taille,
			'data'=> $formatted
		];

	  	header('Access-Control-Allow-Origin: *');
	  	return new JsonResponse($response);
	}

		
	/**
     * @GET("/enchere_one/{numEnchere}")
	 * url-parm: id(String) 
	 * return of a product
     */
	 public function getOneEnchereAction($numEnchere,Request $request)
     { 
		
		$dm= $this->get('doctrine_mongodb')->getManager();
		$repository = $dm->getRepository('RestBundle:Enchere');

	    $enchere =  $repository->findOneByNumEnchere($request->get('numEnchere'));
		if(empty($enchere)){
			$formatted = [
               'statut' => '404'
            ];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}
		
		
		$details = $enchere->getDetails();;
		$formattedDetail=[];
		foreach($details as $detail ){
			$formattedDetail[] = [
				'name' => $detail->getName(),
				'value' => $detail->getValue()
			];
		}

		$historiques = $enchere->getHistoriques();;
		$formattedHistorique=[];
		foreach($historiques as $historique ){
			$formattedHistorique[] = [
				'idUser' => $historique->getIdUser(),
				'price' => $historique->getPrice(),
				'date' => $historique->getDate()
			];
		}
		
		$formatted = [
			'id' => $enchere->getId(),
			'numEnchere' => $enchere->getNumEnchere(),
			'name'=> $enchere->getName(),
			'etat' => $enchere->getEtat(),
			'statut' => $enchere->getStatut(),
			'initDate' => $enchere->getInitDate(),
			'closeDate' => $enchere->getCloseDate(),
			'initPrice' => $enchere->getInitPrice(),
			'description' => $enchere->getDescription(),
			'idImage' => $enchere->getIdImage(),
			'idImage1' => $enchere->getIdBigImage1(),
			'idImage2' => $enchere->getIdBigImage2(),
			'idImage3' => $enchere->getIdBigImage3(),
			'idImage4' => $enchere->getIdBigImage4(),
			'detail' => $formattedDetail,
			'historique' => $formattedHistorique
		];

		
	    $response = [
			'statut' => '200',
			'response' => $formatted
	    ];

	    header('Access-Control-Allow-Origin: *');
		return new JsonResponse($response);
	}

	/**
     * @GET("/enchere/all_categories")
	 * url-parm: id(String) 
	 * return of a product
     */
	 public function getEnchereCategoryAction(Request $request)
     { 
		// select of all the marque
		$dm= $this->get('doctrine_mongodb')->getManager();
		$EnchereCollection = $dm->getDocumentCollection('RestBundle:Enchere')->getMongoCollection();
	    $criteria = array("statut" => "En cours");
		$allcategoriesName= $EnchereCollection->distinct("category");

		 $data=[
			'categories' => $allcategoriesName,
		];
		
		 $response = [
        	'status' => '200',
			'data' => $data
		];
	   header('Access-Control-Allow-Origin: *');
	   return new JsonResponse($response);
	}


	/**
     * @GET("/enchere_user/{login}")
	 * url-parm: id(String) 
	 * return of a product
    */
	public function getUserEnchereAction($login,Request $request)
    { 

		$dm= $this->get('doctrine_mongodb')->getManager();
		$repository = $dm->getRepository('RestBundle:User');
		$repository1 = $dm->getRepository('RestBundle:Enchere');

	    $user =  $repository->findOneByLogin($request->get('login'));
		if(empty($user)){
			$formatted = [
               'statut' => '404'
            ];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}

		$formattedEncheres=[];

		$encheres = $user->getEncheres();


		foreach($encheres as $enchere ){
	    	$currentEnchere = $repository1->findOneByNumEnchere($enchere->getNumEnchere());
			if(!empty($currentEnchere)){
				$formattedEncheres[] = [
					'numEnchere' => $enchere->getNumEnchere(),
					'article'  => $currentEnchere->getName(), 
					'date' => $enchere->getAddDate()
				];
			}
		}

		
		$formatted = [
			'enchere' => $formattedEncheres
		];

		
	    $response = [
			'statut' => '200',
			'response' => $formatted
	    ];

	   header('Access-Control-Allow-Origin: *');
	   return new JsonResponse($response);
	}


	/**
     * @POST("/enchere_statut/vendu")
	 * 
	 * 
     */
    public function modifyStatut1Action(Request $request)
    { 
	  

	  $dm= $this->get('doctrine_mongodb')->getManager();
	  $repository = $dm->getRepository('RestBundle:Enchere');
	  
	  
	   // retrieving the product
	   $enchere = $repository->findOneByNumEnchere($request->get('numEnchere'));
	   if(empty( $enchere )){
			$formatted = [
               'statut' => '404'
            ];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
	   }
	      
	   $enchere->setStatut('Vendu');
       $dm->flush();
	   $formatted = [
               'statut' => '200'
       ];
	   
	   header('Access-Control-Allow-Origin: *');
	   return new JsonResponse($formatted);
	}



	/**
     * @GET("/enchere_statut/clos")
	 * 
     */
    public function modifyStatut3Action(Request $request)
    { 
	  
	   $dm= $this->get('doctrine_mongodb')->getManager();
	   $repository = $dm->getRepository('RestBundle:Enchere');

	   $dm= $this->get('doctrine_mongodb')->getManager();
	   $repository = $dm->getRepository('RestBundle:Enchere');
	   $encheres = $repository->findAll();

       foreach ($encheres as  $enchere) {

      		$now = new \DateTime('now', new \DateTimeZone('Africa/Douala'));
      		if($enchere->getCloseDate() < $now){
       			$enchere->setStatut('Clos');
       			$dm->flush();
	   		}

      	}

      	$formatted = [
	        'statut' => '200',
	        'now' => sizeof($encheres)
	    ];
	   
	    header('Access-Control-Allow-Origin: *');
	    return new JsonResponse($formatted);
	}


	/**
     * @GET("/enchere_statut/reset")
	 * 
     */
    public function modifyStatut4Action(Request $request)
    { 
	  
	   $dm= $this->get('doctrine_mongodb')->getManager();
	   $repository = $dm->getRepository('RestBundle:Enchere');

	   $encheres = $repository->findAll();
       foreach ($encheres as  $enchere) {
      		if($enchere->getStatut() == 'Clos'){
       			$enchere->setStatut("En cours");
	   			$datetime = new \DateTime('now', new \DateTimeZone('Africa/Douala'));
	   			$enchere->setInitDate($datetime);
	  			$startDate = date('Y-m-d H:i:s');
	   			$time = $enchere->getTime();
      			$nextDate = date("Y-m-d H:i:s", strtotime("$startDate  +$time day"));
	   			$enchere->setCloseDate($nextDate); 
       			$dm->flush();
	   		}
      	}

      	$formatted = [
	        'statut' => '200'
	    ];
	   
	    header('Access-Control-Allow-Origin: *');
	    return new JsonResponse($formatted);
	}

/******************************************************************************************/	 
/*********************** get enchère  by text ***********************************************/	
    /**
     * @GET("/enchere_search/{text}/{page}")
	 * 
     */
	 public function getEnchereBytextAction($text,$page,Request $request)
     {  
		
		$formatted=[];
		$formattedWholeSale=[];
		$formattedRetailSale=[];
		$formattedMarque=[];
		$formattedSearchMarque=[] ;
		$formattedCat =[];
		
		$qb= $this->get('doctrine_mongodb')
						->getManager()
						->createQueryBuilder('RestBundle:Enchere') 
						->field('name')->equals(new \MongoRegex('/'.$text.'/i'))	
						->skip(5*($page-1))
						->limit(5);

		$qb1= $this->get('doctrine_mongodb')
						->getManager()
						->createQueryBuilder('RestBundle:Enchere') 
						->field('name')->equals(new \MongoRegex('/'.$text.'/i'));

		$query = $qb1->getQuery();
		$taille= sizeof($query->execute());

		$query = $qb->getQuery();
		$encheres=$query->execute();



		$encheres=$query->execute();

		$formatted = [];
		$response=[];
		$formattedDetail=[];
		$formattedHsitorique =[];
	 
    	foreach ($encheres as $enchere) {
		
			$details = $enchere->getDetails();;
			$formattedDetail=[];
			foreach($details as $detail ){
				$formattedDetail[] = [
				'name' => $detail->getName(),
				'value' => $detail->getValue()
				];
			}

			$historiques = $enchere->getHistoriques();;
			$formattedHistorique=[];
			foreach($historiques as $historique ){
				$formattedHistorique[] = [
					'idUser' => $historique->getIdUser(),
					'price' => $historique->getPrice(),
					'date' => $historique-> getDate()
				];
			}
		
			$formatted[] = [
				'id' => $enchere->getId(),
				'numEnchere' => $enchere->getNumEnchere(),
				'name'=> $enchere->getName(),
				'etat' => $enchere->getEtat(),
				'statut' => $enchere->getStatut(),
				'initDate' => $enchere->getInitDate(),
				'closeDate' => $enchere->getCloseDate(),
				'initPrice' => $enchere->getInitPrice(),
				'description' => $enchere->getDescription(),
				'quantity' => $enchere->getQuantity(),
				'time' => $enchere->getTime(),
				'idImage' => $enchere->getIdImage(),
				'idImage1' => $enchere->getIdBigImage1(),
				'idImage2' => $enchere->getIdBigImage2(),
				'idImage3' => $enchere->getIdBigImage3(),
				'idImage4' => $enchere->getIdBigImage4(),
				'detail' => $formattedDetail,
				'historique' => $formattedHistorique
			];
		}

		$response=[
			'statut' => '200',
			'data'=> $formatted,
			'taille' => $taille
		];

	  	header('Access-Control-Allow-Origin: *');
	  	return new JsonResponse($response);
		
	}

}