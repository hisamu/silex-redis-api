<?php 

namespace Acme\Manager;

class Manager
{
    protected $instances = array();

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function __get($manager)
    {
        if (!array_key_exists($manager, $this->instances)) {
            $className = 'Acme\\Manager\\' . ucfirst($manager) . 'Manager';
            $this->instances[$manager] = new $className($this);
        }

        return $this->instances[$manager];
    }
}