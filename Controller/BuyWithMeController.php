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
use RestBundle\Document\BuyWithMe;


class BuyWithMeController extends Controller
{
    /**
     * @POST("/buywithMe")
	 * registration
     */
    public function postUserAction(Request $request)
    {   
		$reference = uniqid();
		$buyWithMe= new BuyWithMe();
		$buyWithMe->setReference($reference);
		$buyWithMe->setNameProduct($request->get('nameProduct'));
		$buyWithMe->setRemaindingQuantity($request->get('quantity'));
		$buyWithMe->setCreationDate($request->get('creationDate'));
		$buyWithMe->setEndDate($request->get('endDate'));
		$buyWithMe->setIsCurrent(true);
	  
		$dm = $this->get('doctrine_mongodb')->getManager();
		$dm->persist($buyWithMe);
		$dm->flush();

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