<?php

declare(strict_types=1);

namespace App\Services;

final class Events
{
    /**
     * List of events with all callbacks.
     *
     * @var array {'event-key' => callable[]}
     */
    protected array $events = [];

    protected function __construct() {
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
     * @param string $key
     * @param callable $callback
     */
    public static function addEvent(string $key, callable $callback): void
    {
        if (!isset(self::getInstance()->events[$key])) {
            self::getInstance()->events[$key] = [];
        }

        self::getInstance()->events[$key][] = $callback;
    }

    public static function getEvents(): array
    {
        return self::getInstance()->events;
    }

    public static function dispatch(string $key, string $data): void
    {
        global $app;
        $eventsTable = $app['events-table'];
        $eventsTable->set(
            (string)count($eventsTable),
            [
                'event_key' => $key,
                'event_data' => $data,
            ]
        );
    }
}
