<?php

namespace MSD\Controllers;

use MSD\Controllers\BaseController;

class InstallController extends BaseController
{
    public function getIndex()
    {
        return $this->template->display('pages/install/index.twig');
    }
}
