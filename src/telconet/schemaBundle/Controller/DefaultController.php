<?php

namespace telconet\schemaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('schemaBundle:Default:index.html.twig', array('name' => $name));
    }
}
