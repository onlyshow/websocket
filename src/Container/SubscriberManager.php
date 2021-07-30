<?php

namespace App\Container;

use Mix\Redis\Subscribe\Subscriber;

trait SubscriberManager
{
    /**
     * @var Subscriber[]
     */
    protected $connections = [];

    /**
     * 新增连接
     * @param Subscriber $connection
     */
    public function add(Subscriber $connection)
    {
        $id = spl_object_id($connection);
        $this->connections[$id] = $connection;
    }

    /**
     * 移除连接
     * 这里不可关闭连接，因为这个方法是在关闭连接中调用的
     * @param Subscriber $connection
     */
    public function remove(Subscriber $connection)
    {
        $id = spl_object_id($connection);
        if (!isset($this->connections[$id])) {
            return;
        }
        unset($this->connections[$id]);
    }

    /**
     * @param Subscriber $connection
     * @throws \Swoole\Exception
     */
    public function close(Subscriber $connection)
    {
        $connection->close();
        $this->remove($connection);
    }

    /**
     * 关闭全部连接
     * @throws \Swoole\Exception
     */
    public function closeAll()
    {
        foreach ($this->connections as $connection) {
            $connection->close();
            $this->remove($connection);
        }
    }

    /**
     * 计数
     * @return int
     */
    public function count(): int
    {
        return count($this->connections);
    }

    /**
     * 获取全部连接
     * @return Subscriber[]
     */
    public function getConnections(): array
    {
        return $this->connections;
    }
}