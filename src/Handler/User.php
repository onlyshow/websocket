<?php

namespace App\Handler;

use App\Container\RDS;

class User extends Contract\BaseHandler
{
    private function formatUserId($userId): string
    {
        return "user:{$userId}";
    }

    /**
     * @throws \Throwable
     * @throws \Swoole\Exception
     */
    public function private()
    {
        $userId = $this->session->getUserId();
        $channel = $this->formatUserId($userId);

        $sub = $this->session->getSubscriber();
        $sub->subscribe($channel);
    }

    public function send($userId = 0, $msg = '')
    {
        $channel = $this->formatUserId($userId);

        $rds = RDS::instance();
        $rds->publish($channel, $msg);
    }
}