<?php

namespace MSD\Controllers;

use MSD\Controllers\BaseController;

class HomeController extends BaseController
{
    public function getIndex()
    {
        return $this->router->redirect('dashboard');
    }
}
