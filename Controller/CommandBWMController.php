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
use RestBundle\Document\CommandBWMProduct;
use RestBundle\Document\CommandeBWM;
use RestBundle\Document\Livraison;
use RestBundle\Document\LivraisonAdress;
use RestBundle\Document\ProductModel;
use RestBundle\Document\BWM;


class CommandBWMController extends Controller
{
    /**
     * @POST("/commandBWM")
	 * create a command
     */
    public function postCommandBWMAction(Request $request)
    {   
		
		$dm= $this->get('doctrine_mongodb')->getManager();
		$repository = $dm->getRepository('RestBundle:ProductModel');
		$repository1 = $dm->getRepository('RestBundle:BuyWithMe');
		
		$reference = uniqid();
		$command= new CommandeBWM();
		$command->setReference($reference);
		$command->setLogin($request->get('login'));
		$command->setCommandDate($request->get('commandDate'));
		$command->setEndBWMDate($request->get('dateCloture'));
		$command->setIsPaid(true);
		$command->setIsShipped(false);
		
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
	
		$products= json_decode($request->get('products'),true);
		
		foreach ($products as $prod) {
			$item= new CommandBWMProduct();
			$item->setName($prod['name']);
			$item->setPrice($prod['price']);
			$item->setQuantity($prod['quantity']);
			
			
			
			$commandQuantity=$prod['quantity'];
			// get the actual BWM if exist else create on
			$currentBWM = $dm->getRepository('RestBundle:BWM')->findOneBy(array('isCurrent' => true,'nameProduct' => $prod['name']));
			if(empty($currentBWM )){
			   $currentBWM = new BWM();
			   $currentBWM->setNameProduct($prod['name']);
			   $currentBWM->setSize($prod['size']);
			   $currentBWM->setActualQuantity(0);
			   $currentBWM->setCreationDate(new \DateTime('now'));
			   $currentBWM->setIsCurrent(true);
			   $dm->persist( $currentBWM);
		       $dm->flush();
			   
			   $actualQuantity=0;
			   $remain=$prod['size'];
			   
			}
			else{
			   $actualQuantity = $currentBWM->getActualQuantity();
			   $remain= $prod['size']- $currentBWM->getActualQuantity();
			}
			
			while($commandQuantity != 0){
				if($commandQuantity >= $remain){
				   $commandQuantity-=$remain;
				   $currentBWM->setActualQuantity($prod['size']);
				   $currentBWM->setIsCurrent(false);
				   $dm->flush();
				   
				   $currentBWM = new BWM();
				   $currentBWM->setNameProduct($prod['name']);
				   $currentBWM->setSize($prod['size']);
				   $currentBWM->setActualQuantity(0);
			       $currentBWM->setCreationDate(new \DateTime('now'));
			       $currentBWM->setIsCurrent(true);
				   
			       $dm->persist($currentBWM);
		           $dm->flush();
				  
				   $actualQuantity=0;
			       $remain=$prod['size'];
				}
				else{
					$remain-=$commandQuantity;
					$actualQuantity= $actualQuantity+$commandQuantity;
					$currentBWM->setActualQuantity($actualQuantity);
					$commandQuantity=0;
				    $currentBWM->setIsCurrent(true);
					$dm->flush();
				}
			}
			
			$currentBWMId= $currentBWM-> getId();
			$item->setIdCurrentBWM($currentBWMId);
			$command->addCommandProduct($item);
	
		}
		
		
		$command->setLivraisonAdress($adresseLivraison);
		$command->setLivraison($livraison);
		$dm = $this->get('doctrine_mongodb')->getManager();
		$dm->persist($command);
		$dm->flush();
		
		// modification des qunatite des produit
		foreach ($products as $product) {
			$myProduct= $repository->findOneByName($product['name']);
			$nb= $myProduct->getQuantity();
			$myProduct-> setQuantity($nb - $product['quantity']);
			//$dm->flush();
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
}