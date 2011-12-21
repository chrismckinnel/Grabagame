<?php

namespace Grabagame\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction($name)
    {
        return $this->render('GrabagameBundle:Default:index.html.twig', array('name' => $name));
    }
}
