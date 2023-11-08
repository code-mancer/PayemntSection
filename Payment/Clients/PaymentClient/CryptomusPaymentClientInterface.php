<?php

namespace App\Containers\PaymentSection\Payment\Clients\PaymentClient;

interface CryptomusPaymentClientInterface
{
    public function createPaymentPageUrl(array $data = []): CryptomusPaymentClientInterface;
}
