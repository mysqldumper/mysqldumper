<?php

namespace MSD;

use MSD\src\Session;

class Core
{
    /**
     * Bootstrap MSD and get us ready to run.
     * @return \MSD\Core The core.
     */
    public static function run()
    {
        // Start a session
        $session = new Session;
        $session->start();

        // Call the Bootstrapper
        $bootstrap = new \MSD\src\Bootstrap();

        // Setup the error handler
        $bootstrap->registerErrorHandler();

        // Register router
        $bootstrap->registerRouter();

        // Check if MSD is installed
        $bootstrap->checkInstallation();

        // Run the application
        $bootstrap->run();

        return self::class;
    }
}
