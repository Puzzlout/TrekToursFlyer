<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
{
    /**
     * @todo Change this when we implement product entity and change our product/get.html.twig view
     */
    public function testGetProduct()
    {
        $client = static::createClient();
        $productId = mt_rand(1, 3000);
        $crawler = $client->request('GET', '/product/' . $productId);
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Product display ' . $productId . '")')->count(),
            "GET /product/$productId; Failed asserting that 'Product display $productId' is shown on page");

        $client->request('GET', '/product/asd');
        $this->assertTrue(
            $client->getResponse()->isNotFound(),
            "GET /product/asd; Failed asserting that response code is 404");
    }
}
