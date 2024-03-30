<?php

declare(strict_types=1);

namespace App\Events;

interface EventInterface
{
    public function getPayload(): array;
    public function getJsonSerialized(): string;
    public function getListener(): string;
    public function getKey(): string;
}
