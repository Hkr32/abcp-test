<?php

namespace Nw\WebService\References\Operations\Notifications;

class MessagesClient
{
    public static function sendMessage(array $params, int $resellerId, string $notificationEvent, int $clientId = 0, string $to = '')
    {
        // Send to email or add to queue for send
        return true;
    }
}