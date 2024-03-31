<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\EventInterface;
use OpenSwoole\Coroutine;

final readonly class RequestEventListener implements ListenerInterface
{
    public function __construct() {
    }

    public function __invoke(EventInterface $event): void
    {
        Coroutine::sleep(2);
        dump($event->getPayload(), time());
    }
}
