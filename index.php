<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use App\Servers\Http;
use App\Services\Events;
use OpenSwoole\Core\Psr\Response;
use OpenSwoole\Core\Psr\ServerRequest;
use OpenSwoole\Http\Server;
use Psr\Http\Message\ResponseInterface;

Http::run(
    onStartCallback: function (Server $server) {
        Events::addEvent(\App\Enum\Events\Events::REQUEST_EVENT->value, function (string $payload) {
            sleep(5);
            dump('test event happen', $payload);
        });
        echo "OpenSwoole http server is started at http://127.0.0.1:9501\n";
    },

    onRequestCallback: function (ServerRequest $request, Server $server): ResponseInterface {
        $data = json_encode(['new_test' => 'actually new']);

        Events::dispatch(\App\Enum\Events\Events::REQUEST_EVENT->value, $data);

        return (new Response(
            body: $data,
            headers: ['Content-Type' => 'application/json']
        ));
    }
);
