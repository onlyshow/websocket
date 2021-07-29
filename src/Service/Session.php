<?php

namespace App\Service;

use App\Container\Logger;
use App\Handler\Contract\BaseHandler;
use App\Handler\User;
use Mix\WebSocket\Connection;
use Swoole\Coroutine\Channel;

class Session
{
    use UserAuth;

    /**
     * @var Connection
     */
    protected $conn;

    /**
     * @var Channel
     */
    protected $writeChan;

    /**
     * Session constructor.
     * @param Connection $conn
     */
    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
        $this->writeChan = new Channel(10);
    }

    /**
     * @param string $data
     */
    public function send(string $data, $opcode = WEBSOCKET_OPCODE_TEXT): void
    {
        $this->writeChan->push(['data' => $data, 'opcode' => $opcode]);
    }

    public function start(): void
    {
        // 接收消息
        go(function () {
            while (true) {
                try {
                    $frame = $this->conn->readMessage();
                } catch (\Throwable $ex) {
                    // 忽略一些异常日志
                    if (!in_array($ex->getMessage(), ['Active closure of the user', 'Connection reset by peer'])) {
                        Logger::instance()->error(sprintf('ReadMessage: %s', $ex->getMessage()));
                    }
                    $this->stop();
                    return;
                }

                if ($frame->opcode == WEBSOCKET_OPCODE_PING) {
                    $this->send('', WEBSOCKET_OPCODE_PONG);
                } elseif ($frame->data == 'PING') {
                    $this->send('PONG');
                } else {
                    $data = json_decode($frame->data, true);
                    if (json_last_error()) {
                        $this->send(json_encode(['error' => 'not json format'], JSON_UNESCAPED_UNICODE));
                        continue;
                    }

                    [$classname, $method] = explode(':', $data['op'] ?? '');
                    $class = '\\App\\Handler\\' . ucfirst($classname);
                    if (!class_exists($class)) {
                        $this->send(json_encode(['error' => 'not found class'], JSON_UNESCAPED_UNICODE));
                        continue;
                    }

                    $class = new $class($this);
                    if (!method_exists($class, $method)) {
                        $this->send(json_encode(['error' => 'not exists method'], JSON_UNESCAPED_UNICODE));
                        continue;
                    }

                    $class->{$method}(...$data['args']);
                }
            }
        });

        // 发送消息
        go(function () {
            while (true) {
                $result = $this->writeChan->pop();
                if (!$result) {
                    return;
                }

                $frame = new \Swoole\WebSocket\Frame();
                $frame->data = $result['data'];
                $frame->opcode = $result['opcode']; // WEBSOCKET_OPCODE_TEXT or WEBSOCKET_OPCODE_BINARY
                try {
                    $this->conn->writeMessage($frame);
                } catch (\Throwable $ex) {
                    Logger::instance()->error(sprintf('WriteMessage: %s', $ex->getMessage()));
                    $this->stop();
                    return;
                }
            }
        });
    }

    public function stop()
    {
        $this->writeChan->close();
    }

}
