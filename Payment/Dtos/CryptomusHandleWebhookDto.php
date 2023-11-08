<?php

namespace App\Containers\PaymentSection\Payment\Dtos;

use App\Containers\PaymentSection\Payment\UI\API\Requests\CloudPayment\CryptomusHandleWebhookRequest;
use App\Ship\Parents\Dtos\DataTransferObject as ParentDataTransferObject;

class CryptomusHandleWebhookDto extends ParentDataTransferObject
{
    public array $data;
    public string $ip;

    public static function fromRequest(CryptomusHandleWebhookRequest $request): self
    {
        return self::from([
            'data' => $request->all(),
            'ip' => $request->getRequestIp(),
        ]);
    }
}
