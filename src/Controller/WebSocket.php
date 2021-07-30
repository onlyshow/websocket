<?php

namespace App\Controller;

use App\Container\SUB;
use App\Container\Subscriber;
use App\Container\Upgrader;
use App\Handler\User;
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
        $sub  = SUB::instance()->new();

        $session = new Session($conn);
        $session->setUserId($user_id);
        $session->setSubscriber($sub);
        $session->start();

        Subscriber::handle($sub, $session);
        (new User($session))->private();
    }

}
