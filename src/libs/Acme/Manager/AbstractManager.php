<?php 

namespace Acme\Manager;

abstract class AbstractManager
{
    protected $manager;

    public function __construct($manager)
    {
        $this->manager = $manager;
    }
}