<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomepageController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function homeAction()
    {
        //Render the template for home page
        return $this->render("AppBundle:home:get.html.twig");
    }

    public function indexAction()
    {
        return $this->redirectToRoute('home');
    }
}
