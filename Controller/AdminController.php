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
use RestBundle\Document\Administrateur;


class AdminController extends Controller
{

    /**
     * @POST("/admin")
     * registration
     */
    public function postAdminAction(Request $request)
    {  

       $repository = $this->get('doctrine_mongodb')->getManager()->getRepository('RestBundle:Administrateur');
       //check if the user exist
        $user1= $repository->findOneByLogin($request->get('password'));
        if(!empty($user1)){
            $formatted = [
                'statut' => '404'
            ];
            header('Access-Control-Allow-Origin: *');
            return new JsonResponse($formatted);
        }

        $user= new Administrateur();
        $user->setLogin($request->get('login'));
        $user->setPassword( $request->get('password'));       
       
        $dm = $this->get('doctrine_mongodb')->getManager();
        $dm->persist($user);
        $dm->flush();
         
        
        $response = [
                'statut' => '200',
        ];

        header('Access-Control-Allow-Origin: *');
        return new JsonResponse($response);
    }
    

    /**
     * @GET("/admin/log/{login}/{password}")
     * logging of a user
     */
    public function logAdminAction($login,$password ,Request $request)
    {  
       
        $repository = $this->get('doctrine_mongodb')->getManager()->getRepository('RestBundle:Administrateur');

        //check if the user exist
        $admin= $repository->findOneByLogin($login);
        if(empty($admin)){
            $formatted = [
                'statut' => '404'
            ];!
            header('Access-Control-Allow-Origin: *');
            return new JsonResponse($formatted);
        }

        $admin = $repository->findOneBy(array('login'=>$login));
        
        // check if the password is valid
        if ($admin->getPassword() == $password) {
            
                $response = [
                    'statut' => '200',
                ];
                
                header('Access-Control-Allow-Origin: *');
                return new JsonResponse($response);
        } 
        
        else {
                // else password not valid for the login
                $formatted = [
                    'statut' => '404',
                ];

                header('Access-Control-Allow-Origin: *');
                return new JsonResponse($formatted);
        }
    }
            
}
