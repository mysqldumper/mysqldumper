<?php

namespace MSD\Controllers;

class BaseController
{
    public $template;

    public function __construct()
    {
        $template       = new \MSD\src\Template();
        $this->template = $template->getInstance();
    }
}
