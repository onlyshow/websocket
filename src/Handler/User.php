<?php

namespace App\Handler;

use App\Container\RDS;
use App\Container\SUB;

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

        $sub = SUB::instance()->subscriber();
        $sub->subscribe($channel);

//        go(function () use($sub) {
//            while (true) {
//                $data = $sub->channel()->pop();
//
//                if (empty($data)) { // 手动close与redis异常断开都会导致返回false
//                    if (!$sub->closed) {
//                        // redis异常断开处理
//                        var_dump('Redis connection is disconnected abnormally');
//                    }
//                    break;
//                }
//                $this->session->send(json_encode($data, JSON_UNESCAPED_UNICODE));
//            }
//        });
    }

    public function send($userId = 0, $msg = '')
    {
        $channel = $this->formatUserId($userId);

        $rds = RDS::instance();
        $rds->publish($channel, $msg);
    }
}