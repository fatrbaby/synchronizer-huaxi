<?php

namespace Bridge\Model;

use Slim\Container;

class Model
{
    protected $db = null;
    protected $container = null;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    
    public function getDb()
    {
        if (!($this->db instanceof \PDO)) {
            $this->db = $this->container->get('pdo.remote');
        }
        
        return $this->db;
    }
}
