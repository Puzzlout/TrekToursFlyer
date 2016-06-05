<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class CommonModuleController extends Controller
{

    public function headerMenuAction()
    {
        return $this->render("AppBundle:module:header-menu.html.twig");
    }

    public function languageMenuAction()
    {
        return $this->render("AppBundle:module:language-menu.html.twig");
    }

    public function footerAction()
    {
        return $this->render("AppBundle:module:footer.html.twig");
    }

    public function partnerAction()
    {
        return $this->render("AppBundle:module:partner.html.twig");
    }

    public function socialAction()
    {
        return $this->render("AppBundle:module:social.html.twig");
    }

    public function cookieAction()
    {
        return $this->render("AppBundle:module:cookie.html.twig");
    }

}
