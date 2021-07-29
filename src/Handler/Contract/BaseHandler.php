<?php

namespace App\Handler\Contract;

use App\Service\Session;

abstract class BaseHandler
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * Hello constructor.
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }
}