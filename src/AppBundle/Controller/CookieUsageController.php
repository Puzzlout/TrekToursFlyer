<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\Type\CustomerInfoRequestType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class CookieUsageController extends Controller
{
    /**
     * @Route("/cookie-usage", name="cookie_usage")
     */
    public function showCookieUsageAction()
    {
        return $this->render("AppBundle:cookie:show.html.twig");
    }
}