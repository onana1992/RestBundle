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
use RestBundle\Document\Paiement;



class PaiementController extends Controller
{
    /**
     * @GET("/paiement")
     * create a command
     */
    public function postPaiementAction(Request $request)
    {   
          
        
        $dm= $this->get('doctrine_mongodb')->getManager();
        $repository = $dm->getRepository('RestBundle:Paiement');
        $repository1= $dm->getRepository('RestBundle:Commande');
        $montant=0;


        $commandeID = $_GET['rI'];
        $montantPaye = $_GET['rMt'];
        $idReqDoh = $_GET['idReqDoh'];
        $devise = $_GET['rDvs'];
        $codeMarchand = $_GET['rH'] ;
        $devise = $_GET['rDvs'] ;

        $paiement = new Paiement();
        $paiement->setDate(new \DateTime('now'));
        $paiement->setNumeroCmd($commandeID);
        $paiement->setMontant($montantPaye);
        $paiement->setRefTransaction($idReqDoh);
        $paiement->setDevise($devise);
        
        

        //verification du numero de la commande
        $commande= $repository1->findOneByReference($commandeID);
        if(empty($commande)){
            $formatted = [
                'statut' => '404'
            ];
            header('Access-Control-Allow-Origin: *');
            return new JsonResponse($formatted);
        }

        //Calcul du montant de la commandes
         foreach ($commande->getCommandProduct() as $product ) {
            $montant += $product->getPrice();
        }

        //Verification du montant de la transaction
        if ( (int) $montant != (int) $montantPaye) {

            $formatted = [
                'statut' => '404'
            ];

            header('Access-Control-Allow-Origin: *');
            return new JsonResponse($formatted);
        }

        //Verification du code marchand
        if ($codeMarchand != "ED155M321925" ) {

            $formatted = [
                'statut' => '404'
            ];
            
            header('Access-Control-Allow-Origin: *');
            return new JsonResponse($formatted);
        }

        $dm->persist($paiement);
        $dm->flush();

        $response = [
         'statut' => '200'
        ];

        header('Access-Control-Allow-Origin: *');
        return new JsonResponse($response); 
    }
    
}