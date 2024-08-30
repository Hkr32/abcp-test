<?php

namespace Nw\WebService\References\Operations;

use Nw\WebService\References\Operations\Notifications\MessagesClient;
use Nw\WebService\References\Operations\Notifications\NotificationEvents;
use Nw\WebService\References\Operations\Notifications\Status;
use NW\WebService\References\Staff\Contractor;
use Nw\WebService\References\Staff\Employee;
use Nw\WebService\References\Staff\Seller;
use NW\WebService\References\Operations\Notifications\NotificationManager;
use function Nw\WebService\Functions\__;

class TsReturnOperation extends ReferencesOperation
{
    private const TYPE_NEW = 1;
    private const TYPE_CHANGE = 2;


    /**
     * @return array
     * @throws \Exception
     *
     * Выполнение операции
     */
    public function doOperation(): array
    {

        $request = $this->getRequest('data');

        $resellerId = $request['resellerId'];
        $notificationType = (int) $request['notificationType'];
        $result = [
            'notificationEmployeeByEmail' => false,
            'notificationClientByEmail' => false,
            'notificationClientBySms' => [
                'isSent' => false,
                'message' => '',
            ],
        ];

        // Если нет идентификатора продавца, возвращаем ошибку, что он не заполнен
        if (empty((int) $resellerId)) {
            $result['notificationClientBySms']['message'] = 'Empty resellerId';

            return $result;
        }

        // Если нет типа уведомления, возвращаем ошибку, что он не заполнен
        if (empty($notificationType)) {
            throw new \Exception('Empty notificationType', 400);
        }

        $reseller = Seller::getById((int) $resellerId);
        // Если нет продавца, возвращаем ошибку, что он не найден
        if ($reseller === null) {
            throw new \Exception('Seller not found!', 400);
        }

        $client = Contractor::getById((int) $request['clientId']);
        // Если клиент не найден или тип клиента не совпадает с типом клиентов или идентификатор клиент из запроса не равен найденному идентификатору
        if ($client === null || $client->getType() !== Contractor::getCustomerTypeId() || $client->getId() !== (int) $request['clientId']) {
            throw new \Exception('Client not found!', 400);
        }

        $clientFullName = $client->getFullName() ?: $client->getName();

        $creator = Employee::getById((int) $request['creatorId']);
        // Если не найден создатель
        if ($creator === null) {
            throw new \Exception('Creator not found!', 400);
        }

        // Если не найден эксперт
        $expert = Employee::getById((int) $request['expertId']);
        if ($expert === null) {
            throw new \Exception('Expert not found!', 400);
        }

        $differences = '';
        // Получаем различия в зависимости от типа уведомления (Новое уведомление или статус изменился)
        if ($notificationType === self::TYPE_NEW) {
            $differences = __('NewPositionAdded', ['RESELLER_ID' => $resellerId]);
        } elseif ($notificationType === self::TYPE_CHANGE && !empty($request['differences'])) {
            $differences = __('PositionStatusHasChanged', [
                'RESELLER_ID' => $resellerId,
                'FROM' => Status::getName((int) $request['differences']['from']),
                'TO' => Status::getName((int) $request['differences']['to']),
            ]);
        }

        // Данные для шаблона
        $templateData = [
            'COMPLAINT_ID' => (int) $request['complaintId'],
            'COMPLAINT_NUMBER' => (string) $request['complaintNumber'],
            'CREATOR_ID' => (int) $request['creatorId'],
            'CREATOR_NAME' => $creator->getFullName(),
            'EXPERT_ID' => (int) $request['expertId'],
            'EXPERT_NAME' => $expert->getFullName(),
            'CLIENT_ID' => (int) $request['clientId'],
            'CLIENT_NAME' => $clientFullName,
            'CONSUMPTION_ID' => (int) $request['consumptionId'],
            'CONSUMPTION_NUMBER' => (string) $request['consumptionNumber'],
            'AGREEMENT_NUMBER' => (string) $request['agreementNumber'],
            'DATE' => (string) $request['date'],
            'DIFFERENCES' => (string) $differences,
            'RESELLER_ID' => (int) $resellerId,
        ];

        // Если хоть одна переменная для шаблона не задана, то не отправляем уведомления
        foreach ($templateData as $key => $tempData) {
            if (empty($tempData)) {
                throw new \Exception("Template Data ({$key}) is empty!", 500);
            }
        }

        $emailFrom = $reseller->getResellerEmailFrom();
        // Получаем email сотрудников из настроек
        $emails = $reseller->getEmailsByPermit($resellerId, 'tsGoodsReturn');
        // Если есть эл почта от кого отправлять и есть эл почты кому отправлять то отправляем уведомления на почту
        if (!empty($emailFrom) && count($emails) > 0) {
            foreach ($emails as $email) {
                MessagesClient::sendMessage([
                    0 => [ // MessageTypes::EMAIL
                        'emailFrom' => $emailFrom,
                        'emailTo' => $email,
                        'subject' => __('complaintEmployeeEmailSubject', $templateData),
                        'message' => __('complaintEmployeeEmailBody', $templateData),
                    ],
                ], $resellerId, NotificationEvents::CHANGE_RETURN_STATUS);
                $result['notificationEmployeeByEmail'] = true;
            }
        }

        // Шлём клиентское уведомление, только если произошла смена статуса
        if ($notificationType === self::TYPE_CHANGE && !empty($request['differences']['to'])) {
            if (!empty($emailFrom) && !empty($client->email)) {
                MessagesClient::sendMessage([
                    0 => [ // MessageTypes::EMAIL
                        'emailFrom' => $emailFrom,
                        'emailTo' => $client->email,
                        'subject' => __('complaintClientEmailSubject', $templateData),
                        'message' => __('complaintClientEmailBody', $templateData),
                    ],
                ],
                    $resellerId,
                    NotificationEvents::CHANGE_RETURN_STATUS,
                    $client->getId(),
                    $request['differences']['to']
                );
                $result['notificationClientByEmail'] = true;
            }

            if (!empty($client->mobile)) {
                $res = NotificationManager::send(
                    $resellerId,
                    $client->getId(),
                    NotificationEvents::CHANGE_RETURN_STATUS,
                    $request['differences']['to'],
                    $templateData,
                    $error ?? ''
                );
                if ($res) {
                    $result['notificationClientBySms']['isSent'] = true;
                }
                if (!empty($error)) {
                    $result['notificationClientBySms']['message'] = $error;
                }
            }
        }

        return $result;
    }
}
