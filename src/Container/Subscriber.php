<?php

namespace App\Container;

use App\Service\Session;

class Subscriber
{
    use SubscriberManager;

    /**
     * @throws \Throwable
     * @throws \Swoole\Exception
     */
    public function new(): \Mix\Redis\Subscribe\Subscriber
    {
        $host = $_ENV['REDIS_SUB_HOST'];
        $port = $_ENV['REDIS_SUB_PORT'];
        $password = $_ENV['REDIS_SUB_PASSWORD'];
        $database = $_ENV['REDIS_SUB_DATABASE'];
        $sub = new \Mix\Redis\Subscribe\Subscriber($host, $port, $password, $database);
        $this->add($sub);
        return $sub;
    }

    public static function handle(\Mix\Redis\Subscribe\Subscriber $sub, Session $session)
    {
        go(function () use ($sub, $session) {
            while (true) {
                $data = $sub->channel()->pop();

                if (empty($data)) { // 手动close与redis异常断开都会导致返回false
                    if (!$sub->closed) {
                        // redis异常断开处理
                        var_dump('Redis connection is disconnected abnormally');
                    }
                    break;
                }
                $session->send(json_encode($data, JSON_UNESCAPED_UNICODE));
            }
        });
    }
}