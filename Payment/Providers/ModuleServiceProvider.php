<?php

namespace App\Containers\PaymentSection\Payment\Providers;

use GuzzleHttp\Client;
use App\Containers\PaymentSection\Payment\Clients\PaymentClient\CryptomusPaymentClient;
use App\Ship\Parents\Providers\ModuleServiceProvider as ParentModuleServiceProvider;
use App\Containers\PaymentSection\Payment\Clients\PaymentClient\CryptomusPaymentClientInterface;

class ModuleServiceProvider extends ParentModuleServiceProvider
{
    public array $serviceProviders = [
        //
    ];

    public function register(): void
    {
        parent::register();

        $this->app->bind(
            CryptomusPaymentClientInterface::class,
            CryptomusPaymentClient::class,
        );
    }
}
