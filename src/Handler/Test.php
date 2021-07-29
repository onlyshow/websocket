<?php

namespace App\Handler;

use App\Container\SUB;

class Test extends Contract\BaseHandler
{
    public function all()
    {
        var_dump(SUB::instance($this->session->getUserId())->getConnections());
    }
}