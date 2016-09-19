<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class CommonModuleController extends Controller
{

    public function headerMenuAction()
    {
        $masterRequest = $this->getMasterRequest();
        $currentUri = $masterRequest->getRequestUri();
        $currentRoute = $masterRequest->get('_route');
        return $this->render(
            "AppBundle:module:header-menu.html.twig",
            ['currentUri' => $currentUri, 'route' => $currentRoute]);
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

    public function languageMenuAction()
    {
        $masterRequest = $this->getMasterRequest();
        $currentRoute = $masterRequest->get('_route');
        $currentRouteParams = $masterRequest->attributes->get('_route_params');
        return $this->render(
            "AppBundle:module:language-menu.html.twig",
            ['route' => $currentRoute, 'routeParams' => $currentRouteParams]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function footerAction()
    {
        $currentYear = date('Y');
        return $this->render(
            "AppBundle:module:footer.html.twig",
            ["currentYear" => $currentYear]);
    }

    public function partnerAction()
    {
        $translator = $this->get('translator');
        $showLink1 = filter_var($translator->trans('footer_partner_a_link1_href'), FILTER_VALIDATE_URL);
        $showLink2 = filter_var($translator->trans('footer_partner_a_link2_href'), FILTER_VALIDATE_URL);
        return $this->render(
            "AppBundle:module:partner.html.twig",
            [
                "showLink1" => $showLink1,
                "showLink2" => $showLink2,
            ]
        );
    }

    public function socialAction()
    {
        $translator = $this->get('translator');
        $showLinkFacebook = filter_var($translator->trans('footer_social_a_facebook_href'), FILTER_VALIDATE_URL);
        $showLinkTwitter = filter_var($translator->trans('footer_social_a_twitter_href'), FILTER_VALIDATE_URL);
        $showLinkGoogle = filter_var($translator->trans('footer_social_a_google_href'), FILTER_VALIDATE_URL);
        $showLinkPintrest = filter_var($translator->trans('footer_social_a_pintrest_href'), FILTER_VALIDATE_URL);
        return $this->render(
            "AppBundle:module:social.html.twig",
            [
                "showLinkFacebook" => $showLinkFacebook,
                "showLinkTwitter" => $showLinkTwitter,
                "showLinkGoogle" => $showLinkGoogle,
                "showLinkPintrest" => $showLinkPintrest

            ]
        );
    }

    public function cookieAction()
    {
        return $this->render("AppBundle:module:cookie.html.twig");
    }

    public function analyticsAction(Request $request)
    {
        $trackingCode = null;
        $cookiesEnabled = false;
        if($request->cookies->has('usr_cc'))
        {
            $cookiesEnabled = $request->cookies->get('usr_cc');
            $trackingCode = $this->container->getParameter('google_analytics');
            if(is_null($trackingCode))
            {
                $cookiesEnabled = false;
            }
        }
        return $this->render(
            "AppBundle:module:analytics.html.twig",
            ['tracking' => $trackingCode, 'enabled' => $cookiesEnabled]
        );
    }

}
