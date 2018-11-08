<?php

namespace MSD\Controllers;

class Base
{
    public $template;

    public function __construct()
    {
        $template       = new \MSD\Template();
        $this->template = $template->getInstance();
    }
}