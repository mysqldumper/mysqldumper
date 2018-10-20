<?php

namespace MSD\Controllers;

class Base
{
    private static $template;

    public function __construct()
    {
        // Load the templates from filesystem
        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../../templates');

        // Prep the environment
        self::$template = new \Twig_Environment($loader, array(
            // 'cache' => '/path/to/compilation_cache',
            'debug' => true
        ));
    }
}