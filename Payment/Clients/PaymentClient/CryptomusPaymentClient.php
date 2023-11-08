<?php

namespace App\Containers\PaymentSection\Payment\Clients\PaymentClient;

use Exception;

class CryptomusPaymentClient implements CryptomusPaymentClientInterface
{
    public string $paymentFormUrl;

    /**
     * @throws Exception
     */
    public function createPaymentPageUrl(array $data = []): CryptomusPaymentClientInterface
    {

        $data = json_encode($data, true);
        $sign = md5(base64_encode($data) . config('cryptomusPaymentClient.api_key'));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, config('cryptomusPaymentClient.service_url') . '/v1/payment');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $headers = [
            'merchant: ' . $this->getMerchantId(),
            'sign: ' . $sign,
            'Content-Type: application/json',
       ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception('Error for getting payment form url!');
        }

        $this->setPaymentFormUrl(json_decode($result, true)['result']['url']);

        curl_close($ch);

        return $this;
    }

    public function setPaymentFormUrl(string $paymentFormUrl): void
    {
        $this->paymentFormUrl = $paymentFormUrl;
    }

    public function getPaymentFormUrl(): string
    {
        return $this->paymentFormUrl;
    }

    private function getMerchantId(): string
    {
        return config('cryptomusPaymentClient.merchant_id');
    }
}
