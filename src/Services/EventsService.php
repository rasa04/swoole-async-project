<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\Enum\Table\Columns;
use App\Events\Enum\Table\Settings;
use App\Events\EventInterface;
use App\Listeners\ListenerInterface;
use OpenSwoole\Table;

final class EventsService
{
    /**
     * List of event listeners with all callbacks.
     *
     * @var array {'listener' => callable[]}
     */
    private array $listeners = [];
    private Table $table;

    private function __construct() {
        $table = new Table(Settings::TABLE_SIZE->value);

        $table->column(
            Columns::LISTENER_KEY->value,
            Columns::getType(Columns::LISTENER_KEY),
            Columns::getSize(Columns::LISTENER_KEY)
        );
        $table->column(
            Columns::EVENT->value,
            Columns::getType(Columns::EVENT),
            Columns::getSize(Columns::EVENT)
        );

        $table->create();

        $this->table = $table;
    }

    public static function getInstance(): self
    {
        static $instance;

        if ($instance === null) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * @param array $listeners
     *
     * @return EventsService
     */
    public function addListeners(array $listeners): self
    {
        /** @var ListenerInterface $listener */
        foreach ($listeners as $listener) {
            if (!isset(self::getInstance()->listeners[$listener::class])) {
                self::getInstance()->listeners[$listener::class] = [];
            }

            self::getInstance()->listeners[$listener::class][] = $listener;
        }

        return $this;
    }

    public static function getListeners(): array
    {
        return self::getInstance()->listeners;
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    public function dispatch(EventInterface $event): void
    {
        global $app;
        /** @var EventsService $eventsService */
        $eventsService = $app[EventsService::class];
        $eventsService
            ->getTable()
            ->set(
            $event->getKey(),
            [
                Columns::LISTENER_KEY->value => $event->getListener(),
                Columns::EVENT->value => serialize($event),
            ]
        );
    }
}
