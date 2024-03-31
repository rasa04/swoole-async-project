<?php

declare(strict_types=1);

namespace App\Servers\Interfaces;

interface ServerInterface
{
    public static function run(callable $onRequestCallback, ?array $callbacks = null): void;
}
