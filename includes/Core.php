<?php

namespace MSD;

class Core
{
    public static function run()
    {
        // Call the Bootstrapper
        $bootstrap = new \MSD\Bootstrap();

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