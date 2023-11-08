<?php

namespace App\Containers\PaymentSection\Payment\Data\Factories;

use App\Containers\PaymentSection\Payment\Abstracts\AbstractPaymentManager;
use App\Containers\PaymentSection\Payment\Constants\PaymentManagerConstants;
use App\Containers\PaymentSection\Payment\Managers\Cryptomus\CryptomusManager;

class PaymentManagerFactory
{
    public function getPaymentManager(string $manager): AbstractPaymentManager
    {
        return match ($manager) {
            PaymentManagerConstants::CRYPTOMUS  => resolve(CryptomusManager::class),
        };
    }
}
