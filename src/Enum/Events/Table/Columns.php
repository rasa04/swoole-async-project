<?php

namespace App\Enum\Events\Table;

use OpenSwoole\Table;

enum Columns: string
{
    case EVENT_KEY = 'event_key';
    case EVENT_DATA = 'event_data';

    public static function getSize(self $column): int
    {
        return match ($column) {
            Columns::EVENT_KEY => 40,
            Columns::EVENT_DATA => 250,
        };
    }

    public static function getValue(self $column): string
    {
        return match ($column) {
            Columns::EVENT_KEY => Columns::EVENT_KEY->value,
            Columns::EVENT_DATA => Columns::EVENT_DATA->value,
        };
    }

    public static function getType(self $column): int
    {
        return match ($column) {
            Columns::EVENT_KEY, Columns::EVENT_DATA => Table::TYPE_STRING,
        };
    }
}
