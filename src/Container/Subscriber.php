<?php

namespace App\Container;

class Subscriber
{
    use SubscriberManager;

    /**
     * @throws \Throwable
     * @throws \Swoole\Exception
     */
    public function subscriber(): \Mix\Redis\Subscribe\Subscriber
    {
        if (!$sub = $this->get()) {
            $host = $_ENV['REDIS_SUB_HOST'];
            $port = $_ENV['REDIS_SUB_PORT'];
            $password = $_ENV['REDIS_SUB_PASSWORD'];
            $database = $_ENV['REDIS_SUB_DATABASE'];
            $sub = new \Mix\Redis\Subscribe\Subscriber($host, $port, $password, $database);
            $this->add($sub);
        }
        return $sub;
    }
}