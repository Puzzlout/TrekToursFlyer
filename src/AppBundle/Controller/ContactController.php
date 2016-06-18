<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\Type\CustomerInfoRequestType;
use Symfony\Component\HttpFoundation\Request;

class ContactController extends Controller
{
    /**
     * @Route("/contact", name="contact")
     */
    public function showContact(Request $request) {
        $form = $this->createForm(CustomerInfoRequestType::class, null, array(
            'action' => $this->generateUrl('contact'),
            'method' => 'POST'
        ));
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            //TODO: Implement API call
        }
        return $this->render("AppBundle:contact:show.html.twig", array(
            'form' => $form->createView()
        ));
    }
}