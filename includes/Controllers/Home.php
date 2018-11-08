<?php

namespace MSD\Controllers;

class Home extends \MSD\Controllers\Base
{
    public function getIndex()
    {
        return $this->template->display('pages/index.twig');
    }
}
