<?php

namespace App\Container;

use App\Service\Session;

class SUB
{
    /**
     * @var Subscriber
     */
    static private $instance;

    /**
     * @param $userId
     * @return Subscriber
     */
    public static function instance($userId): Subscriber
    {
        if (!isset(self::$instance)) {
            self::$instance = new Subscriber($userId);
        }
        return self::$instance;
    }

    /**
     * @param Session $session
     * @throws \Swoole\Exception
     * @throws \Throwable
     */
    public static function handle(Session $session)
    {
        $subscriber = self::instance($session->getUserId());

        go(function () use ($subscriber, $session) {
            while (true) {
                $data = $subscriber->subscriber()->channel()->pop();

                if (empty($data)) { // 手动close与redis异常断开都会导致返回false
                    if (!$subscriber->subscriber()->closed) {
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