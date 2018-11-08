<?php

namespace MSD\src;

use \Whoops\Run;
use \Whoops\Handler\PrettyPageHandler;

class Bootstrap
{
    /**
     * The router.
     */
    private $router;

    /**
     * MSD version.
     */
    private static $version = '1.0.0';

    /**
     * Get the version string.
     * @return string The version number.
     */
    public function getVersion() : string
    {
        return self::$version;
    }

    public function registerErrorHandler(): void
    {
        $whoops = new Run();
        $whoops->pushHandler(new PrettyPageHandler());
        $whoops->register();
    }

    public function registerRouter() : void
    {
        // Create Router instance
        $router = new Router();

        $this->router = $router->getInstance();

        $this->router->setNamespace('\MSD\Controllers');
        $this->router->get('/', 'Home@getIndex');
    }

    public function checkInstallation() : bool
    {
        if (file_exists(__DIR__ . '/config.php')) {
            return true;
        }

        return false;
    }

    public function run() : void
    {
        $this->router->run();
    }
}
