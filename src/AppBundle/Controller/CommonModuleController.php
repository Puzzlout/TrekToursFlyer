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
        $masterRequest = $this->getMasterRequest();
        $currentRoute = $masterRequest->get('_route');
        $currentRouteParams = $masterRequest->attributes->get('_route_params');
        return $this->render("AppBundle:module:language-menu.html.twig", ['route'=>$currentRoute,
            'routeParams'=>$currentRouteParams]);
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

    /**
     * Get top level Request object
     */
    private function getMasterRequest()
    {
        $stack = $this->get('request_stack');
        $masterRequest = $stack->getMasterRequest();
        return $masterRequest;
    }

}
