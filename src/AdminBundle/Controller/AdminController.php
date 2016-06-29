<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
    /**
     * @Route("/", name="admin_index")
     */
    public function indexAction()
    {
        return $this->render('AdminBundle:index:index.html.twig');
    }

    /**
     * @Route("/login", name="admin_login")
     */
    public function loginAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('AdminBundle:security:login.html.twig',
            ["last_username" => $lastUsername, "error" => $error]);
    }

    /**
     * @Route("/customerinforequests", name="admin_cri")
     */
    public function customerInfoRequestsAction(Request $request)
    {
        $apiUrl = $this->getParameter('api_url');
        $apiFormat = $this->getParameter('api_format');
        $restClient = $this->container->get('circle.restclient');
        $query = '?limit=50';
        $limit = $request->query->get('limit');
        if(isset($limit) and trim($limit) != '') {
            $query = '?limit='.$limit;
        }
        $from = $request->query->get('from');
        if(isset($from) and trim($from) != '') {
            $query = '?from='.$from;
        }
        $to = $request->query->get('to');
        if(isset($to) and trim($to) != '') {
            $query = '?to='.$to;
        }

        $response = $restClient->get($apiUrl.'/customerinforequests'.$apiFormat.$query, [
            CURLOPT_HTTPHEADER => ['Authorization: Bearer '.$this->getUser()->getToken()]
        ]);
        if($response->getStatusCode() != 200) {
            throw new \Exception();
        }
        $customerInfoRequests = json_decode($response->getContent());
        return $this->render('AdminBundle:customerinforequests:index.html.twig', [
            'customerInfoRequests' => $customerInfoRequests
        ]);
    }

    /**
     * @Route("/customerinforequests/{id}", name="admin_cri_update_status")
     */
    public function updateStatusCustomerInfoRequests($id, Request $request)
    {
        $status = $request->request->get('status');
        if(!is_null($status)) {
            $apiUrl = $this->getParameter('api_url');
            $apiFormat = $this->getParameter('api_format');
            $restClient = $this->container->get('circle.restclient');
            $response = $restClient->patch($apiUrl.'/customerinforequests/'.$id.'/status'.$apiFormat,
                json_encode([ 'status' => $status ]),
                [
                    CURLOPT_HTTPHEADER => [
                        'Authorization: Bearer '.$this->getUser()->getToken(),
                        'Content-Type:application/json'
                    ]
            ]);
            if($response->getStatusCode() != 200) {
                throw new \Exception('Error processing request');
            }
            $this->addFlash('admin_update_status', 'Status updated');
        }
        return $this->redirect($request->headers->get('referer'));
    }
}
