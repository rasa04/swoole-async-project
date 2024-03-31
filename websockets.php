<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use App\Servers\Websockets;
use Swoole\Constant;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

Websockets::run(
    onMessageCallback: function(Server $server, Frame $frame) {
        echo "received message: {$frame->data}\n";
        foreach ($server->connections as $fileDescriptors) {
            if (!$server->isEstablished($fileDescriptors)) {
                 continue;
            }

            $server->push($fileDescriptors, $frame->data);
        }
    },
    callbacks: [
        Constant::EVENT_START => function (Server $server) {
            echo "OpenSwoole WebSocket Server is started at http://$server->host:$server->port\n";
        },
        Constant::EVENT_OPEN => function (Server $server, OpenSwoole\Http\Request $request) {
            echo "connection open: {$request->fd}\n";
        },
        Constant::EVENT_REQUEST => function (Request $request, Response $response) {
            $response->header('Content-Type', 'text/html');
            $response->end(file_get_contents('resources/websockets.php'));
        },
    ]
);
