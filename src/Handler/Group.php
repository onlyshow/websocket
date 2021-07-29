<?php

namespace App\Handler;

use App\Container\RDS;
use App\Container\SUB;
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

    /**
     * @throws \Throwable
     * @throws \Swoole\Exception
     */
    public function leave($groupId = 0)
    {
        $sub = SUB::instance()->subscriber();
        $sub->unsubscribe($groupId);
    }

    public function send($groupId = 0, $msg = '')
    {
        $channel = $this->formatGroupId($groupId);

        $rds = RDS::instance();
        $rds->publish($channel, $msg);
    }
}