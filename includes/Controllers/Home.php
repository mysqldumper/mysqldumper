<?php

namespace MSD\Controllers;

use MSD\Controllers\Base;
use MSD\Template;

class Home extends Base
{
    public function getIndex()
    {
        Template::display('pages/install.twig');
    }
}