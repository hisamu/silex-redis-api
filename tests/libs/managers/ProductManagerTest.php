<?php 

use Acme\Manager\Manager;
use Silex\WebTestCase;

class ProductManagerTest extends WebTestCase
{
    public function createApplication()
    {
        $app = require __DIR__.'/../../../src/app.php';
        $app['session.test'] = true;
 
        return $app;
    }

    public function testGetOnePrice()
    {
        $manager = $this->getManagerOne();

        $product = $manager->product->one('test-product');

        $this->assertNotNull($product);
        $this->assertEquals($product['name'], 'test-product');
        $this->assertNotNull($product['price']);
    }

    public function testGetManyPrice()
    {
        $manager = $this->getManagerMany();

        $products = $manager->product->many(array('test-product', 'another-product'));

        $this->assertCount(2, $products);
        $this->assertNotNull($products[0]['price']);
        $this->assertEquals($products[1]['name'], 'another-product');
    }

    protected function getManagerOne()
    {
        $dbMock = \Mockery::mock('\Doctrine\DBAL\Connection');
        $dbMock
            ->shouldReceive('fetchAssoc')
            ->with('SELECT * FROM products WHERE name = ?', array('test-product'))
            ->once()
            ->ordered('reset')
            ->andReturn(array('name' => 'test-product', 'price' => 90));

        return new Manager($dbMock);
    }

    protected function getManagerMany()
    {
        $dbMock = \Mockery::mock('\Doctrine\DBAL\Connection');
        $dbMock
            ->shouldReceive('fetchAll')
            ->with('SELECT * FROM products WHERE name IN (?,?)', array('test-product', 'another-product'))
            ->once()
            ->ordered('reset')
            ->andReturn(array(
                array('name' => 'test-product', 'price' => 90), 
                array('name' => 'another-product', 'price' => 120)));

        return new Manager($dbMock);
    }
}