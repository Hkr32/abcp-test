<?php

namespace Nw\WebService\Helpers;

class Subs
{
    private static array $differences = [
        'NewPositionAdded' => 'Реселлер: RESELLER_ID. Была добавлена новая позиция.',
        'PositionStatusHasChanged' => 'Реселлер: RESELLER_ID. Позиция была изменена с FROM на TO.',
        'complaintEmployeeEmailSubject' => 'Complaint id: COMPLAINT_ID. Complaint number: COMPLAINT_NUMBER',
        'complaintEmployeeEmailBody' => '
            Complait id: COMPLAINT_ID. Complait number: COMPLAINT_NUMBER.
            Creator id: CREATOR_ID. Creator name: CREATOR_NAME.
            Expert id: EXPERT_ID. Expert name: EXPERT_NAME. 
            Client id: CLIENT_ID. Client name: CLIENT_NAME. 
            Consumption id: CONSUMPTION_ID. Consumption name: CONSUMPTION_NAME. 
            Agreement number: AGREEMENT_NUMBER. 
            Date: DATE. 
            Differences: DIFFERENCES.
        ',
        'complaintClientEmailSubject' => 'Agreement number: AGREEMENT_NUMBER',
        'complaintClientEmailBody' => '
            Complait id: COMPLAINT_ID. Complait number: COMPLAINT_NUMBER.
            Creator id: CREATOR_ID. Creator name: CREATOR_NAME.
            Expert id: EXPERT_ID. Expert name: EXPERT_NAME. 
            Client id: CLIENT_ID. Client name: CLIENT_NAME. 
            Consumption id: CONSUMPTION_ID. Consumption name: CONSUMPTION_NAME. 
            Agreement number: AGREEMENT_NUMBER. 
            Date: DATE. 
            Differences: DIFFERENCES.
        ',
    ];

    public static function __(string $lineKey, array $params)
    {
        $shouldReplace = [];
        foreach ($params as $key => $param) {
            $shouldReplace[':'.$key] = $param;
        }

        return strtr(self::$differences[$lineKey], $shouldReplace);
    }
}