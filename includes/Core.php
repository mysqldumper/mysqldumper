<?php

namespace MSD;

class Core
{
    /**
     * MSD version.
     */
    private static $version = '1.0.0';

    /**
     * The router.
     */
    private static $router;

    private static function registerErrorHandler()
    {
        $whoops = new \Whoops\Run();
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
        $whoops->register();
    }

    private static function registerRouter()
    {
        // Create Router instance
        $router = new Router();

        self::$router = $router->getInstance();

        self::$router->setNamespace('\MSD\Controllers');
        self::$router->get('/', 'Home@getIndex');

        self::$router->run();
    }

    public static function getVersion()
    {
        return self::$version;
    }

    private function detectInstallation()
    {
        if (file_exists(__DIR__ . '/config.php')) return true;

        return false;
    }

    public function redirect($to)
    {
        header("Location: $to");
        exit;
    }

    public static function run()
    {
        // Setup the error handler
        self::registerErrorHandler();

        // Register router
        self::registerRouter();

        // Run installation detection
        // if (! $this->detectInstallation()) $this->redirect('install');

        return self::class;
    }
}