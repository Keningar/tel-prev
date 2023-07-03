<?php

namespace telconet\catalogoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction($name)
    {
        return $this->render('catalogoBundle:Default:index.html.twig', array('name' => $name));
    }
}
