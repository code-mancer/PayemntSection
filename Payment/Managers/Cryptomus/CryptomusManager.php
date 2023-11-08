<?php

namespace App\Containers\PaymentSection\Payment\Managers\Cryptomus;

use App\Containers\PaymentSection\Payment\Abstracts\AbstractPaymentManager;
use App\Containers\PaymentSection\Payment\Clients\PaymentClient\CryptomusPaymentClientInterface;

class CryptomusManager extends AbstractPaymentManager
{
    public function __construct(private readonly CryptomusPaymentClientInterface $cryptomusPaymentClient)
    {
    }

    public function createPaymentPageUrl(array $apiData): array
    {
        $additionalData = [
            'url_callback' => config('cryptomusPaymentClient.url_callback')
        ];

        $sendData = array_merge($additionalData, $apiData);

        $this->cryptomusPaymentClient->createPaymentPageUrl($sendData);

        return ['url' => $this->cryptomusPaymentClient->getPaymentFormUrl()];
    }

}
