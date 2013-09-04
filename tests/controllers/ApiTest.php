<?php 

use Silex\WebTestCase;

class ApiTest extends WebTestCase
{
    public function createApplication()
    {
        $app = require __DIR__.'/../../src/app.php';
        $app['session.test'] = true;
 
        return $app;
    }

    public function testSinglePrice()
    {
        $client = $this->createClient();
        $client->request('GET', '/api/price/test-product');

        $this->assertTrue($client->getResponse()->isOk());
        $product = json_decode($client->getResponse()->getContent());
        $this->assertFalse(array_key_exists('error', $product));

        $this->assertEquals($product->name, 'test-product');
        $this->assertEquals($product->price, 90);
    }

    public function testMultipleProductsPrice()
    {
        $client = $this->createClient();
        $client->request('GET', '/api/price/test-product/another-test');

        $this->assertTrue($client->getResponse()->isOk());
        $products = (array) json_decode($client->getResponse()->getContent());
        $this->assertCount(2, $products);

        $keys = array_keys($products);
        $this->assertEquals($keys[0], 'test-product');
    }

    public function testProductNotFound()
    {
        $client = $this->createClient();
        $client->request('GET', '/api/price/foo-bar');

        $this->assertFalse($client->getResponse()->isOk());
        $this->assertTrue(array_key_exists('error', json_decode($client->getResponse()->getContent())));
    }

    public function testCacheHit()
    {
        $path = '/api/price/test-product';
        $this->app['mcache']->expire($path);

        $client = $this->createClient();
        $client->request('GET', $path); // populates the cache
        $client->request('GET', $path);

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertNotNull($this->app['mcache']->get($path));
    }
}