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
use RestBundle\Document\Image;



class ImageController extends Controller
{

	 /**
      * @POST("/image")
	  * key:image
      */
     public function uploadImageAction(Request $request)
    {
		$dm= $this->get('doctrine_mongodb')->getManager();
		$repository = $dm->getRepository('RestBundle:Image');
		$file = $request->files->get('image');
	    //var_dump($file);
		$directory='C:/wamp/www/maayi';
		$name = base64_encode(random_bytes(15).''.$file->getClientOriginalName());
		$extension = explode("/", $file->getMimeType());
		$image = new Image();
		$name = str_replace("/","0",base64_encode(random_bytes(15).$file->getClientOriginalName()).'.'.$extension[1]);
		$path = $directory.'/'.$name;
		$file1 = $file->move($directory, $name);
		$path = $directory.'/'.$name;
		$image->setFile($path);
		$image->setFilename($name);
		$image->setIsUsed(false);
		$image->setMimeType($file->getClientMimeType());
		$dm->persist($image);
		$dm->flush();
		$image1 = $repository->findOneByFilename($name);
		$response = [
		'id'=> $image1->getId()
		];
		$formatted=[
		  'statut'=>'200',
		  'response'=> $response
		];
		 header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);;
	}
	
	/**
     * @GET("/image/{id}")
     */
    public function getImageAction($id ,Request $request)
    {  
	   
	    $dm= $this->get('doctrine_mongodb')->getManager();
	    $repository = $dm->getRepository('RestBundle:Image');
		//check if the user exist
		$image = $repository->findOneById($id);
		if(empty($image)){
			$formatted = [
				'statut' => '404'
			];
			return new JsonResponse($formatted);
		}
		$response = new Response();
        $response->headers->set('Content-Type', $image->getMimeType());
		$response->setContent( $image->getFile()->getBytes());
	    return $response;
	}
	
	/**
     * @POST("/image/delete/{id}")
     */
    public function deleteImageAction($id ,Request $request)
    {  
	   
	    $dm= $this->get('doctrine_mongodb')->getManager();
	    $repository = $dm->getRepository('RestBundle:Image');
		//check if the user exist
		$image = $repository->findOneById($id);
		if(empty($image)){
			$formatted = [
				'statut' => '404'
			];
			
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}
		
		// else you can delete the images
		$dm->remove($image);
		$dm->flush();
		
			$formatted = [
               'statut' => '200'
            ];
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
	}
}