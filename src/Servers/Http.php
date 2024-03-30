<?php

declare(strict_types=1);

namespace App\Servers;

use App\Enum\Events\Table\Columns;
use App\Enum\Events\Table\Settings;
use App\Services\Events;
use OpenSwoole\Timer;
use OpenSwoole\Core\Psr\{Response, ServerRequest};
use OpenSwoole\Http\Request as SwooleRawRequest;
use OpenSwoole\Http\Response as SwooleRawResponse;
use OpenSwoole\Table;
use Swoole\Constant;
use Swoole\Http\Server;
use Throwable;

final readonly class Http implements ServerInterface
{
    public const string DEFAULT_ADDRESS = '127.0.0.1';
    public const int DEFAULT_PORT = 9501;

    public static function run(callable $onStartCallback, callable $onRequestCallback): void
    {
        try {
            $server = new Server(self::DEFAULT_ADDRESS, self::DEFAULT_PORT);

            $server->on(
                Constant::EVENT_START,
                callback: $onStartCallback
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

            self::setEvents();
            $server->start();
        } catch (Throwable $t) {
            dd($t->getMessage(), $t->getTrace(), $t);
        }
    }

    private static function setEvents(): void
    {
        $table = new Table(Settings::TABLE_SIZE->value);
        $table->column(
            Columns::getValue(Columns::EVENT_KEY),
            Columns::getType(Columns::EVENT_KEY),
            Columns::getSize(Columns::EVENT_KEY)
        );
        $table->column(
            Columns::getValue(Columns::EVENT_DATA),
            Columns::getType(Columns::EVENT_DATA),
            Columns::getSize(Columns::EVENT_DATA)
        );
        $table->create();

        global $app;
        $app['events-table'] = $table;

        Timer::tick(1000, function () use ($table) {
            $daemonEvents = Events::getEvents();

            foreach ($table as $key => $event) {
                if (!isset($daemonEvents[$event['event_key']])) {
                    continue;
                }

                foreach ($daemonEvents[$event['event_key']] as $handler) {
                    $handler($event['event_data']);
                }

                $table->del($key);
            }
        });
    }
}
