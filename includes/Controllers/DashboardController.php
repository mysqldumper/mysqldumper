<?php

namespace MSD\Controllers;

use MSD\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function getIndex()
    {
        return $this->template->display('pages/dashboard/index.twig');
    }
}
