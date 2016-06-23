<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\Type\CustomerInfoRequestType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class ContactController extends Controller
{
    /**
     * @Route("/contact", name="contact")
     */
    public function showContact(Request $request) {
        $form = $this->createForm(CustomerInfoRequestType::class, null, array(
            'translation_domain' => 'contact',
            'action' => $this->generateUrl('contact'),
            'method' => 'POST'
        ));
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $apiUrl = $this->getParameter('api_url');
            $apiFormat = $this->getParameter('api_format');
            $restClient = $this->container->get('circle.restclient');
            $formData = $form->getData();
            $formData['status'] = 'TBP';
            try {
                $response = $restClient->post($apiUrl.'/customerinforequests'.$apiFormat, json_encode($formData));
                if($response->getStatusCode() != 201) {
                    throw new \Exception();
                }

                unset($form);
                $form = $this->createForm(CustomerInfoRequestType::class, null, array(
                    'action' => $this->generateUrl('contact'),
                    'method' => 'POST'
                ));
                $this->addFlash('cri_success', $this->get('translator')->trans('div_success'));
            } catch (\Exception $e) {
                $form->addError(new FormError('API error'));
            }
        }
        return $this->render("AppBundle:contact:show.html.twig", array(
            'form' => $form->createView()
        ));
    }
}