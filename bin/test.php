<?php
use Swoole\WebSocket\Frame;
use Swoole\Coroutine\Http\Client;
use function Swoole\Coroutine\run;

run(function () {
    $cli = new Client('127.0.0.1', 9502);
    $cli->upgrade('/websocket?token=1');
    $pingFrame = new Frame;
    $pingFrame->opcode = WEBSOCKET_OPCODE_PING;
    // 发送 PING
    $cli->push($pingFrame);

    // 接收 PONG
    $pongFrame = $cli->recv();
    var_dump($pongFrame, $pongFrame->opcode === WEBSOCKET_OPCODE_PONG);
});
