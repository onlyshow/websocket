<?php

namespace App\Handler;

use App\Container\RDS;
use App\Handler\Contract\BaseHandler;

class Group extends BaseHandler
{
    private function formatGroupId($groupId): string
    {
        return "group:{$groupId}";
    }

    /**
     * @throws \Throwable
     * @throws \Swoole\Exception
     */
    public function join($groupId = 0)
    {
        $channel = $this->formatGroupId($groupId);

        $sub = $this->session->getSubscriber();
        $sub->subscribe($channel);
    }

    /**
     * @throws \Throwable
     * @throws \Swoole\Exception
     */
    public function leave($groupId = 0)
    {
        $sub = $this->session->getSubscriber();
        $sub->unsubscribe($groupId);
    }

    public function send($groupId = 0, $msg = '')
    {
        $channel = $this->formatGroupId($groupId);

        $rds = RDS::instance();
        $rds->publish($channel, $msg);
    }
}