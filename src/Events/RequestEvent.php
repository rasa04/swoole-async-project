<?php

declare(strict_types=1);

namespace App\Events;

use App\Listeners\ListenerInterface;
use App\Listeners\RequestEventListener;
use Override;

final class RequestEvent implements EventInterface
{
    private string $key;

    private array $payload;
    private string $listener = RequestEventListener::class;

    public function __construct(array $payload)
    {
        $this->key = (string)time();
        $this->payload = $payload;
    }

    public function getListener(): string
    {
        return $this->listener;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    #[Override] public function getPayload(): array
    {
        return $this->payload;
    }

    #[Override] public function getJsonSerialized(): string
    {
        return json_encode($this->getPayload());
    }
}
