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
        $query = '?limit=5';
        $limit = $request->query->get('limit');
        if(isset($limit) and trim($limit) != '') {
            $query = '?limit='.$limit;
        } else {
            $limit = 5;
        }
        $page = $request->query->get('page');
        if(isset($page) and trim($page) != '') {
            $query .= '&offset='.($page-1)*$limit;
        } else {
            $page = 1;
            $query .= '&offset=0';
        }
        $fromDate = $request->query->get('from');
        if(isset($fromDate) and trim($fromDate) != '') {
            $query = '?from='.$fromDate;
        }
        $toDate = $request->query->get('to');
        if(isset($toDate) and trim($toDate) != '') {
            $query = '?to='.$toDate;
        }


        $response = $restClient->get($apiUrl.'/customerinforequests'.$apiFormat.$query, [
            CURLOPT_HTTPHEADER => ['Authorization: Bearer '.$this->getUser()->getToken()]
        ]);
        if($response->getStatusCode() != 200) {
            throw new \Exception();
        }
        $totalCount = $response->headers->get('X-Total-Count');
        $totalPages = (int)ceil($totalCount/$limit);
        $previousParams = $nextParams = $request->query->all();
        if($page <= 1) {
            $page = 1;
            $previousParams['page'] = 1;
            $nextParams['page'] = $page+1;
        } else if($page >= $totalPages) {
            $page = $totalPages;
            $nextParams['page'] = $totalPages;
            $previousParams['page'] = $page - 1;
        } else {
            $nextParams['page'] = $page + 1;
            $previousParams['page'] = $page - 1;
        }
        $customerInfoRequests = json_decode($response->getContent());
        return $this->render('AdminBundle:customerinforequests:index.html.twig', [
            'customerInfoRequests' => $customerInfoRequests,
            'page' => $page,
            'totalPages' => $totalPages,
            'previousParams' => $previousParams,
            'nextParams' => $nextParams
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
