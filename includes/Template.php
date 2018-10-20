<?php

namespace MSD;

class Template
{
    /**
     * MSD version.
     */
    private static $version;

    /**
     * The Twig environment.
     */
    private static $twig;

    public static function display($template, $viewData = [])
    {
        echo self::render($template, $viewData);
        exit;
    }

    public static function render($template, $viewData = [])
    {
        // Load the templates from filesystem
        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../templates');

        // Prep the environment
        $twig = new \Twig_Environment($loader, array(
            // 'cache' => '/path/to/compilation_cache',
            'debug' => true
        ));

        // Set the version number
        self::$version = \MSD\Core::getVersion();

        $viewData = array_merge($viewData, self::getInitialViewData());

        return $twig->render($template, $viewData);
    }

    private static function getInitialViewData()
    {
        $manifest = json_decode(file_get_contents(__DIR__ . '/../mix-manifest.json'), true);

        return [
            'cssURL'     => $manifest['/css/app.css'],
            'jsURL'      => $manifest['/js/app.js'],
            'msdVersion' => self::$version,
        ];
    }
}