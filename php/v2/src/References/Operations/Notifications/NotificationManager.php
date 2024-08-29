<?php

namespace Nw\WebService\References\Operations\Notifications;

class NotificationManager
{
    public static function send(
        int $resellerId,
        int $clientId,
        string $status,
        string $differencesTo,
        array $templateData,
        string $error
    ): bool {
        return true;
    }
}