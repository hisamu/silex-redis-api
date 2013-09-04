<?php 

namespace Acme\Manager;

class ProductManager extends AbstractManager
{
    public function one($name)
    {
        return $this->manager->conn->fetchAssoc("SELECT * FROM products WHERE name = ?", array($name));
    }

    public function many($names)
    {
        $inQuery = implode(',', array_fill(0, count($names), '?'));

        return $this->manager->conn->fetchAll("SELECT * FROM products WHERE name IN ({$inQuery})", $names);
    }    
}