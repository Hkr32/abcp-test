<?php

namespace Nw\WebService;

use Nw\WebService\References\Operations\TsReturnOperation;

class App
{
    public function start(): void
    {
        $operation = new TsReturnOperation();
        $result = $operation->doOperation();

        // Вывод результатов проделанных операций
        echo $result['notificationEmployeeByEmail'] ? 'notificationEmployeeByEmail' : 'not notificationEmployeeByEmail';
        echo '<br>';
        echo $result['notificationClientByEmail'] ? 'notificationClientByEmail' : 'not notificationClientByEmail';
        echo '<br>';
        echo $result['notificationClientBySms']['isSent']
            ? 'notificationClientBySms isSent'
            : 'not notificationClientBySms.';
        echo($result['notificationClientBySms']['message']
            ? 'Message: '.$result['notificationClientBySms']['message']
            : 'Not message');
        echo '<br>';
    }
}
