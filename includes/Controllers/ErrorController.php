<?php

namespace MSD\Controllers;

use MSD\Controllers\BaseController;

class ErrorController extends BaseController
{
    public function notFound()
    {
        return $this->template->display('errors/404.twig');
    }
}
