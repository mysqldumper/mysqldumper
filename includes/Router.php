<?php

namespace MSD;

class Router
{
    private $router;

    public function __construct()
    {
        // Create Router instance
        $router = new \Bramus\Router\Router();

        $this->router = $router;
    }

    public function getInstance()
    {
        return $this->router;
    }
}