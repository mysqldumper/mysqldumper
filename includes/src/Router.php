<?php

namespace MSD\src;

use \Bramus\Router\Router as BramusRouter;

class Router extends BramusRouter
{
    /**
     * Retrieve the current router instance.
     * @return \MSD\src\Router The router instance.
     */
    public function getInstance() : Router
    {
        return $this;
    }

    /**
     * Setup the router. Used only in bootstrapping, after that it should
     * always be readily available.
     * @return \MSD\src\Router The router instance.
     */
    public function init() : \MSD\src\Router
    {
        $this->setNamespace('\MSD\Controllers');
        $this->set404('ErrorController@notFound');

        $this->initRoutes();

        return $this;
    }

    /**
     * Setup and process all the routes required, as well as their associated
     * controller.
     * @return [type] [description]
     */
    public function initRoutes() : void
    {
        // Install
        $this->get('/install', 'InstallController@getIndex');
        $this->post('/install', 'InstallController@postIndex');

        // Index
        $this->get('/', 'HomeController@getIndex');

        // Dashboard
        $this->get('/dashboard', 'DashboardController@getIndex');
    }

    /**
     * Detect if we are on the install system. Used for making sure we don't
     * try and redirect users who are already in the installer.
     * @return bool True if we're on the installer.
     */
    public function onInstallPage() : bool
    {
        if ($this->getCurrentUri() === '/install') {
            return true;
        }

        return false;
    }

    /**
     * Send a 301 redirect back to the browser.
     * @param  string $location The URI to redirect to
     * @return void
     */
    public function redirect(string $location) : void
    {
        header("Location: " . $this->getBasePath() . $location);
        exit;
    }

    /**
     * Fire a redirect to the installer.
     * @return void
     */
    public function redirectToInstaller() : void
    {
        // redirect to the installation uri
        $this->redirect('install');
    }
}
