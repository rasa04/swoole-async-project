<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\EventInterface;

interface ListenerInterface
{
    public function __invoke(EventInterface $event): void;
}
