<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\Type\CustomerInfoRequestType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class TermsController extends Controller
{
    /**
     * @Route("/terms", name="terms")
     */
    public function showTermsAction()
    {
        return $this->render("AppBundle:terms:show.html.twig");
    }
}