<?php

namespace RestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/oo")
     */
    public function indexAction()
    {
        return $this->render('RestBundle:Default:index.html.twig');
    }
}
