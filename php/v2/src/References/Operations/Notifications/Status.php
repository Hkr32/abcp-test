<?php

namespace Nw\WebService\References\Operations\Notifications;

class Status
{
    private static array $statuses = [
        1 => 'Completed',
        2 => 'Pending',
        3 => 'Rejected',
    ];

    public static function getName(int $id): string
    {
        return self::$statuses[$id];
    }
}