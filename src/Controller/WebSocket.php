<?php

namespace App\Controller;

use App\Container\SUB;
use App\Container\Upgrader;
use App\Service\Session;
use Mix\Vega\Context;

class WebSocket
{
    /**
     * @param Context $ctx
     */
    public function index(Context $ctx): void
    {
        $user_id = $ctx->query('token');

        $conn = Upgrader::instance()->upgrade($ctx->request, $ctx->response);
        $session = new Session($conn);
        $session->setUserId($user_id);
        $session->start();

        SUB::handle($session);
//        (new User($session))->private();
    }

}
