<?php 

use Acme\Manager\Manager;

class ManagerTest extends \PHPUnit_Framework_TestCase 
{
    public function testNewManagerInstance()
    {
        $manager = new Manager(null);
        $this->assertInstanceOf('Acme\\Manager\\ProductManager', $manager->product);
    }
}