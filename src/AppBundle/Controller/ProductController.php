<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ProductController extends Controller
{
    /**
     * @Route("/product/{productId}/{locale}", requirements={"productId" = "\d+"}, name="product_get")
     * @todo Add Product Entity and optionally convert this with paramconverter
     */
    public function getAction($productId,$locale='en')
    {
        $this->get('translator')->setLocale($locale);
        //add db fetch logic or paramconverter
        return $this->render("AppBundle:product:get.html.twig", ['id' => $productId]);
    }
}
