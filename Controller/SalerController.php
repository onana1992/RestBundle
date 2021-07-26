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
use RestBundle\Document\saler;



class SalerController extends Controller
{
	
    /**
     * @POST("/saler")
	 * registration
     */
    public function postSalerAction(Request $request)
    {  
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
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$randstring = '';
			for ($i = 0; $i < 5; $i++) {
				$randstring .= $characters[rand(0, strlen($characters))];
			}
			return $randstring;
		}
	   
	   // check if an account with the email or tel is already exist
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
       $encoder = $this->get('security.password_encoder');
	   $encoded = $encoder->encodePassword($user, $user->getPlainPassword());
	   $user->setPassword( $encoded);
	   
	   // buiding of the token
	   $token = new AuthToken();
	   $token->setValue(base64_encode(random_bytes(50)));
	   $token->setCreatedAt(new \DateTime('now'));
	   $user->setToken($token);
	   
	   
	   // building of an activated number
	   $activationNumber =RandomString();
	   $user->setActivationNumber($activationNumber);
	   
	   $formattedToken=[
	   'value'=> $token->getValue(),
	   'createdAt'=> $token->getCreatedAt()
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
		   ->setSubject('Some Subject')
		   ->setFrom('onanajunior92@gmail.com')
		   ->setTo($request->get('login'))
		   ->setBody($this->renderView(
				// app/Resources/views/Emails/registration.html.twig
				'RestBundle:Emails:registration.html.twig',array('code' => $activationNumber)
			),'text/html');
			
			$this->get('mailer') ->send($message);
			
		}
		
		$response = [
				'statut' => '200',
				'response'=> $payload
		];

		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($response);
    }
}