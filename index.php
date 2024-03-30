<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use App\Events\RequestEvent;
use App\Listeners\RequestEventListener;
use App\Servers\Http;
use App\Services\EventsService;
use OpenSwoole\Core\Psr\Response;
use OpenSwoole\Core\Psr\ServerRequest;
use OpenSwoole\Http\Server;
use Psr\Http\Message\ResponseInterface;

$eventsService = EventsService::getInstance()
    ->addListeners([
        new RequestEventListener
    ]);

global $app;
$app[EventsService::class] = $eventsService;

Http::run(
    onRequestCallback: function (ServerRequest $request, Server $server) use ($eventsService): ResponseInterface {
        $data = ['new_test' => 'actually new'];

        $eventsService->dispatch(new RequestEvent($data));

        return (new Response(
            body: json_encode($data),
            headers: ['Content-Type' => 'application/json']
        ));
    }
);
