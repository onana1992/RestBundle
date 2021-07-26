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
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\View\View; // Utilisation de la vue de FOSRestBundle
use RestBundle\Entity\Place;

class PlaceController extends Controller
{
    /**
     * @Get("/places")
     */
    public function getPlacesAction(Request $request)
    {
       
	   $formatted = [
           'id' => ['id1'=>'1','id2'=>'2'],
           'name' => 'nano',
           'address' => 'junior',
        ];

        return new JsonResponse($formatted);
    }
	
	/**
     * @Get("/places/{id}")
     */
	public function getPlaceAction(Request $request)
    {
       
	   $formatted = [
           'id' => ['id1'=>'1','id2'=>'2'],
           'name' => 'nano',
           'address' => 'junior',
        ];
		
		 // Récupération du view handler
        $viewHandler = $this->get('fos_rest.view_handler');

        // Création d'une vue FOSRestBundle
        $view = View::create($formatted);
        $view->setFormat('json');

        // Gestion de la réponse
        return $viewHandler->handle($view);

        //return new JsonResponse($formatted);
    }
	
	/**
     * @POST("/places")
     */
    public function postPlacesAction(Request $request)
    {
       
	   $formatted = [
           'name' => $request->get('name'),
           'address' => $request->get('address')
        ];

        return new JsonResponse($formatted);
    }
	
	/**
     * @DELETE("/places/{id}")
     */
    public function deletePlacesAction(Request $request)
    {
       
	   $formatted = [
           'name' => $request->get('id'),
           'address' => ''
        ];

        return new JsonResponse($formatted);
    }
	
	/**
     * @PUT("/places/{id}")
     */
    public function updatePlacesAction(Request $request)
    {
       
	   $formatted = [
           'name' => $request->get('non'),
           'address' => ''
        ];

        return new JsonResponse($formatted);
    }
	
	
}