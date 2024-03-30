<?php

namespace App\Events\Enum\Table;

use OpenSwoole\Table;

enum Columns: string
{
    case LISTENER_KEY = 'listener_key';
    case EVENT = 'event';

    public static function getSize(self $column): int
    {
        return match ($column) {
            Columns::LISTENER_KEY => 40,
            Columns::EVENT => 250,
        };
    }

    public static function getType(self $column): int
    {
        return match ($column) {
            Columns::LISTENER_KEY, Columns::EVENT => Table::TYPE_STRING,
        };
    }
}
