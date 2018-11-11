<?php

namespace MSD\src;

class Request extends \Symfony\Component\HttpFoundation\Request
{
    public function all()
    {
        return array_merge($this->request->all(), $this->query->all());
    }
}