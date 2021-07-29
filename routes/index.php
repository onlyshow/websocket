<?php

use App\Controller\WebSocket;

$func = function (Mix\Vega\Context $ctx) {
    $token = $ctx->query('token');
    if (!$token) {
        $ctx->abortWithStatus(401);
    }

    $ctx->next();
};

return function (Mix\Vega\Engine $vega) use ($func) {
    $vega->handle('/websocket', $func, [new WebSocket(), 'index'])->methods('GET');

    $vega->withHTMLRoot(__DIR__ . '/../views');
    $vega->handle('/', function (Mix\Vega\Context $ctx) {
        $ctx->HTML(200, 'index', []);
    })->methods('GET');

    $vega->staticFile('/favicon.ico', __DIR__ . '/../public/favicon.ico');
};
