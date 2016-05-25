<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
{
	/*
	 * @todo Change this when we implement product entity and change our product/get.html.twig view
	 */
	public function testGetProduct()
	{
		$client = static::createClient();
		$id = mt_rand(1,3000);
		$crawler = $client->request('GET', '/product/'.$id);
		$this->assertGreaterThan(0,$crawler->filter('html:contains("Product display '.$id.'")')->count(),"GET /product/$id; Failed asserting that 'Product display $id' is shown on page");

		$client->request('GET', '/product/asd');
		$this->assertTrue($client->getResponse()->isNotFound(),"GET /product/asd; Failed asserting that response code is 404");
	}
}
