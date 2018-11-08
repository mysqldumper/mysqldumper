<?php

namespace MSD\Controllers;

use MSD\Template;

class Home extends \MSD\Controllers\Base
{
    public function getIndex()
    {
        $template = new Template();
        return $this->template->display('pages/index.twig');
    }
}