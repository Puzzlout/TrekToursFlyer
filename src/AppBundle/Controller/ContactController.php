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
            $restClient = $this->container->get('circle.restclient');
            $captchaPayload = [
                "secret" => $this->getParameter('recaptcha_secret'),
                "response" => $request->request->get('g-recaptcha-response')
            ];
            $captchaJsonResponse = $restClient->post('https://www.google.com/recaptcha/api/siteverify',
                http_build_query($captchaPayload),
                [
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_HTTPHEADER => [ 'Content-Type: application/x-www-form-urlencoded' ]
                ]
            );
            $captchaResponse = json_decode($captchaJsonResponse->getContent());
            if(json_last_error() === 0 && $captchaResponse->success === true )
            {
                $apiUrl = $this->getParameter('api_url');
                $apiFormat = $this->getParameter('api_format');
                $formData = $form->getData();
                error_log($formData['message']);
                $formData['status'] = 'TBP';
                $response = $restClient->post($apiUrl.'/customerinforequests'.$apiFormat, json_encode($formData));
                $customerInfoRequest = json_decode($response->getContent());
                if($response->getStatusCode() == 201 && $customerInfoRequest->id) {
                    //send emails
                    $noReplyAddress = $this->container->getParameter('mail_noreply_address');
                    $contactAddress = $this->container->getParameter('mail_contact_address');
                    $adminLanguage = $this->container->getParameter('mail_admin_language');
                    $adminMessageSent = $clientMessageSent = 0;
                    $adminMessage = \Swift_Message::newInstance()
                        ->setSubject($this->get('translator')->trans(
                            'admin_subject',
                            [],
                            'email',
                            $adminLanguage
                        ))
                        ->setFrom($noReplyAddress)
                        ->setTo($contactAddress)
                        ->setBody(
                            $this->renderView(
                                'AppBundle:emails:cri_admin.html.twig',
                                [
                                    'first_name' => $formData['first_name'],
                                    'last_name' => $formData['last_name'],
                                    'message' => $formData['message'],
                                    'phone_number' => $formData['phone_number'],
                                    'email' => $formData['email'],
                                    'admin_email_language' => $adminLanguage
                                ]
                            ),
                            'text/html'
                        )->addPart(
                            $this->renderView(
                                'AppBundle:emails:cri_admin.txt.twig',
                                [
                                    'first_name' => $formData['first_name'],
                                    'last_name' => $formData['last_name'],
                                    'message' => $formData['message'],
                                    'phone_number' => $formData['phone_number'],
                                    'email' => $formData['email'],
                                    'admin_email_language' => $adminLanguage
                                ]
                            ),
                            'text/plain'
                        )
                    ;
                    if($noReplyAddress && $contactAddress) {
                        try {
                            $adminMessageSent = $this->get('mailer')->send($adminMessage);
                        } catch (\Exception $e) {
                            $adminMessageSent = 0;
                        }
                    }

                    if($formData['send_copy_to_client'] == 1) {
                        $clientMessage = \Swift_Message::newInstance()
                            ->setSubject($this->get('translator')->trans(
                                'client_subject',
                                [],
                                'email'
                            ))
                            ->setFrom($noReplyAddress)
                            ->setTo($formData['email'])
                            ->setBody(
                                $this->renderView(
                                    'AppBundle:emails:cri_client.html.twig',
                                    [
                                        'first_name' => $formData['first_name'],
                                        'last_name' => $formData['last_name'],
                                        'message' => $formData['message']
                                    ]
                                ),
                                'text/html'
                            )->addPart(
                                $this->renderView(
                                    'AppBundle:emails:cri_client.txt.twig',
                                    [
                                        'first_name' => $formData['first_name'],
                                        'last_name' => $formData['last_name'],
                                        'message' => $formData['message']
                                    ]
                                ),
                                'text/plain'
                            )
                        ;
                        if($noReplyAddress) {
                            try {
                                $clientMessageSent = $this->get('mailer')->send($clientMessage);
                            } catch (\Exception $e) {
                                $clientMessageSent = 0;
                            }
                        }
                    }

                    if($adminMessageSent != 0 || ($formData['send_copy_to_client'] == 1 && $clientMessageSent != 0)) {
                        $adminEmailSent = ($adminMessageSent != 0) ? 1 : 0;
                        $clientEmailSent = ($clientMessageSent != 0) ? 1 : 0;
                        $response = $restClient->patch(
                            $apiUrl.'/customerinforequests/'.$customerInfoRequest->id.'/sentemails'.$apiFormat,
                            json_encode([
                                'admin_email_sent' => $adminEmailSent,
                                'client_email_sent' => $clientEmailSent
                            ]));
                    }

                    unset($form);
                    $form = $this->createForm(CustomerInfoRequestType::class, null, array(
                        'translation_domain' => 'contact',
                        'action' => $this->generateUrl('contact'),
                        'method' => 'POST'
                    ));
                    $this->addFlash('cri_success', $this->get('translator')->trans('div_success', [], 'contact'));
                } else {
                    $form->addError(new FormError('API error'));
                }
            } else {
                $form->addError(new FormError('reCaptcha error'));
            }

        }
        return $this->render("AppBundle:contact:show.html.twig", array(
            'form' => $form->createView(),
            'recaptcha_key' => $this->getParameter('recaptcha_key')
        ));
    }
}