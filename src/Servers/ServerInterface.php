<?php

declare(strict_types=1);

namespace App\Servers;

interface ServerInterface
{
    public static function run(callable $onStartCallback, callable $onRequestCallback): void;
}
