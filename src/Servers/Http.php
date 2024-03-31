<?php

declare(strict_types=1);

namespace App\Servers;

use App\Events\Enum\Table\Columns;
use App\Servers\Interfaces\ServerInterface;
use App\Services\EventsService;
use OpenSwoole\Core\Psr\{Response, ServerRequest};
use OpenSwoole\Http\Request as SwooleRawRequest;
use OpenSwoole\Http\Response as SwooleRawResponse;
use OpenSwoole\Timer;
use Swoole\Constant;
use Swoole\Http\Server;
use Throwable;

final readonly class Http implements ServerInterface
{
    public const string DEFAULT_ADDRESS = '127.0.0.1';
    public const int DEFAULT_PORT = 9501;

    public static function run(callable $onRequestCallback, ?array $callbacks = null): void
    {
        try {
            $server = new Server(self::DEFAULT_ADDRESS, self::DEFAULT_PORT);

            $server->on(
                Constant::EVENT_START,
                callback: $callbacks[Constant::EVENT_START] ?? function (Server $server) {
                    echo sprintf(
                        'OpenSwoole http server is started at https://%s:%d%s',
                        $server->host ?: self::DEFAULT_ADDRESS,
                        $server->port ?: self::DEFAULT_PORT,
                        PHP_EOL
                    );
                }
            );

            $server->on(
                Constant::EVENT_REQUEST,
                callback: fn (
                    SwooleRawRequest $request,
                    SwooleRawResponse $response
                ) => Response::emit(
                    response: $response,
                    psrResponse: $onRequestCallback(ServerRequest::from($request), $server)
                )
            );

            self::setListeners();

            $server->start();

        } catch (Throwable $t) {
            dd($t->getMessage(), $t->getTrace(), $t);
        }
    }

    private static function setListeners(): void
    {
        global $app;
        /** @var EventsService $eventsService */
        $eventsService = $app[EventsService::class];

        // Time should be greater than handler executing time
        Timer::tick(2005, function () use ($eventsService) {
            $listeners = $eventsService->getListeners();
            $table = $eventsService->getTable();

            foreach ($table as $rowKey => $event) {
                if (!isset($listeners[$event[Columns::LISTENER_KEY->value]])) {
                    continue;
                }

                foreach ($listeners[$event[Columns::LISTENER_KEY->value]] as $handler) {
                    $handler(unserialize($event[Columns::EVENT->value]));
                }

                $table->del($rowKey);
            }
        });
    }
}
