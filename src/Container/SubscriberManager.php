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
     * @var string
     */
    protected string $userId;

    public function __construct(string $userId)
    {
        $this->userId = $userId;
    }

    /**
     * 新增连接
     * @param Subscriber $connection
     */
    public function add(Subscriber $connection)
    {
        $id = $this->userId;
        $this->connections[$id] = $connection;
    }

    /**
     * @return Subscriber|null
     */
    public function get(): ?Subscriber
    {
        $id = $this->userId;
        return $this->connections[$id] ?? null;
    }

    /**
     * 移除连接
     * 这里不可关闭连接，因为这个方法是在关闭连接中调用的
     * @param Subscriber $connection
     */
    public function remove()
    {
        $id = $this->userId;
        if (!isset($this->connections[$id])) {
            return;
        }
        unset($this->connections[$id]);
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
     * 获取全部连接
     * @return Subscriber[]
     */
    public function getConnections(): array
    {
        return $this->connections;
    }
}