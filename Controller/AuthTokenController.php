# src/AppBundle/Controller/AuthTokenController.php
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


class AuthTokenController extends Controller
{
    /**
	 * @Get("/auth-tokens")
    */
    public function getAuthTokensAction(Request $request)
    {
       // $article= new Article();
        // $encoder = $this->get('security.password_encoder');
        //$isPasswordValid = $encoder->isPasswordValid($user, $credentials->getPassword());

        
       // $authToken = new AuthToken();
       // $authToken->setValue(base64_encode(random_bytes(50)));
       // $authToken->setCreatedAt(new \DateTime('now'));
        //$authToken->setUser($article);

        //$em->persist($authToken);
        //$em->flush();

       $formatted = [
           'token' => 'nanan';
        ];
		return new JsonResponse($formatted);
    }

    
}