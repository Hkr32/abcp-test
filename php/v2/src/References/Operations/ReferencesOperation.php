<?php

namespace Nw\WebService\References\Operations;


abstract class ReferencesOperation
{
    abstract public function doOperation(): array;

    public function getRequest($pName)
    {
        // Тестовые данные для запроса
        // ?data[resellerId]=11&data[notificationType]=2&data[clientId]=33&data[creatorId]=44&data[expertId]=55&data[complaintId]=66&data[complaintNumber]=77&data[consumptionId]=88&data[consumptionNumber]=99&data[agreementNumber]=111&data[date]=2024-08-08&data[differences][from]=2&data[differences][to]=1
        $request = $_REQUEST[$pName];

        // Если данные являются массивом, то возвращаем массив данных
        if (is_array($request)) {
            return $request;
        }

        // Если не массив, тогда исключение
        throw new \Exception('Not array in request!', 400);
    }
}
