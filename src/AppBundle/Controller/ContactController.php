<?php


namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class ContactController extends Controller
{
    /**
     * @Route("/contact", name="contact")
     */
    public function showContact() {
        return $this->render("AppBundle:contact:show.html.twig");
    }
}