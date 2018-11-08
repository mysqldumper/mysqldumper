<?php

namespace MSD\src;

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

    public function __construct()
    {
        // Set the version number
        $bootstrap     = new \MSD\src\Bootstrap();
        $this->version = $bootstrap->getVersion();

        // Load the templates from filesystem
        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../../templates');

        // Prep the environment
        $this->twig = new \Twig_Environment($loader, array(
            // 'cache' => '/path/to/compilation_cache',
            'debug' => true
        ));
    }

    public function display($template, $viewData = []) : void
    {
        echo $this->render($template, $viewData);
        exit;
    }

    public function render($template, $viewData = []) : string
    {
        $viewData = array_merge($viewData, $this->getInitialViewData());

        return $this->twig->render($template, $viewData);
    }

    private function getInitialViewData() : array
    {
        $manifest = json_decode(file_get_contents(__DIR__ . '/../../mix-manifest.json'), true);

        return [
            'cssURL'     => $manifest['/css/app.css'],
            'jsURL'      => $manifest['/js/app.js'],
            'msdVersion' => $this->version,
        ];
    }

    public function getInstance() : \MSD\src\Template
    {
        return $this;
    }
}
