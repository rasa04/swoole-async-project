<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use OpenSwoole\Core\Psr\ServerRequest;
use OpenSwoole\Core\Psr\Response;
use Psr\Http\Message\ResponseInterface;
use Swoole\Http\Server;
use Servers\Http;

Http::run(
    onStartCallback: function (Server $server) {
        echo "OpenSwoole http server is started at http://127.0.0.1:9501\n";
    },

    onRequestCallback: function (ServerRequest $request): ResponseInterface {
        dump($request);

        return (new Response(json_encode(['new_test' => 'actually new'])));
    }
);
