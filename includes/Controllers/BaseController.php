<?php

namespace MSD\Controllers;

use MSD\src\Request;
use MSD\src\Router;
use MSD\src\Session;
use MSD\src\Template;
use MSD\src\Database;

class BaseController
{
    /**
     * Template engine.
     * @var Template
     */
    public $template;

    /**
     * Request engine.
     * @var Request
     */
    public $request;

    /**
     * Session engine.
     * @var Session
     */
    public $session;

    /**
     * Routing engine.
     * @var Router
     */
    public $router;

    /**
     * Database engine.
     * @var Database
     */
    public $db;

    public function __construct()
    {
        $this->template = new Template();
        $this->request  = Request::createFromGlobals();
        $this->session  = new Session();
        $this->router   = new Router();
        $this->db       = new Database();

        // Get all dbs
    }
}
