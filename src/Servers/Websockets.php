<?php

declare(strict_types=1);

namespace App\Servers;

use Swoole\Constant;
use Swoole\Websocket\Server;
use Throwable;

final readonly class Websockets
{
    public const string DEFAULT_ADDRESS = '0.0.0.0';
    public const int DEFAULT_PORT = 8585;
    public const array SUPPORTED_EVENTS = [
        Constant::EVENT_START,
        Constant::EVENT_OPEN,
        Constant::EVENT_REQUEST
    ];

    public static function run(callable $onMessageCallback, ?array $callbacks = null): void
    {
        try {
            $server = new Server(self::DEFAULT_ADDRESS, self::DEFAULT_PORT);

            foreach ($callbacks as $eventName => $callback) {
                if (!in_array($eventName, self::SUPPORTED_EVENTS, true)) {
                    continue;
                }

                $server->on($eventName, $callback);
            }

            $server->on(Constant::EVENT_MESSAGE, $onMessageCallback);

            $server->on(Constant::EVENT_CLOSE, function($server, $fd) {
                echo sprintf('connection 
                : %d%s', $fd, PHP_EOL);
            });

            $server->start();

        } catch (Throwable $t) {
            dd($t->getMessage(), $t->getTrace(), $t);
        }
    }
}
