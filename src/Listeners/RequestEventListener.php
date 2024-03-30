<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\EventInterface;

final readonly class RequestEventListener implements ListenerInterface
{
    public function __construct() {
    }

    public function __invoke(EventInterface $event): void
    {
        sleep(3);
        dump($event->getPayload(), time());
    }
}
