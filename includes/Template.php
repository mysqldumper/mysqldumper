<?php

namespace MSD;

class Template
{
    /**
     * MSD version.
     */
    private $version;

    /**
     * The Twig environment.
     */
    private $twig;

    public function __construct($msdVersion)
    {
        // Load the templates from filesystem
        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../templates');

        // Prep the environment
        $this->twig = new \Twig_Environment($loader, array(
            // 'cache' => '/path/to/compilation_cache',
            'debug' => true
        ));

        // Set the version number
        $this->version = $msdVersion;
    }

    public function display($template, $viewData = [])
    {
        echo $this->render($template, $viewData);
        exit;
    }

    public function render($template, $viewData = [])
    {
        $viewData = array_merge($viewData, $this->getInitialViewData());
        return $this->twig->render($template, $viewData);
    }

    private function getInitialViewData()
    {
        $manifest = json_decode(file_get_contents(__DIR__ . '/../mix-manifest.json'), true);

        return [
            'cssURL'     => $manifest['/css/app.css'],
            'jsURL'      => $manifest['/js/app.js'],
            'msdVersion' => $this->version,
        ];
    }
}