<?php

declare(strict_types=1);

namespace Servers;

use Swoole\Constant;
use Swoole\Http\Server;
use OpenSwoole\Http\Request as SwooleRawRequest;
use OpenSwoole\Http\Response as SwooleRawResponse;
use OpenSwoole\Core\Psr\{ServerRequest, Response};
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
                    psrResponse: $onRequestCallback(ServerRequest::from($request))
                )
            );

            $server->start();
        } catch (Throwable $t) {
            dd($t->getMessage(), $t->getTrace(), $t);
        }
    }
}
