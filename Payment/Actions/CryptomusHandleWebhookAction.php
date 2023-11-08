<?php

namespace App\Containers\PaymentSection\Payment\Actions;

use App\Containers\AppSection\Account\Exceptions\FailedAddAccountBalanceException;
use App\Containers\AppSection\Account\Tasks\AddAccountBalanceTask;
use App\Containers\AppSection\Subscription\Constants\InvoiceConstants;
use App\Containers\AppSection\Subscription\Tasks\ChangeInvoiceStatusToFailTask;
use App\Containers\AppSection\Subscription\Tasks\ChangeInvoiceStatusToPaidTask;
use App\Containers\PaymentSection\Payment\Constants\PaymentManagerConstants;
use App\Containers\PaymentSection\Payment\Dtos\CryptomusHandleWebhookDto;
use App\Containers\PaymentSection\Payment\Exceptions\InvalidHashException;
use App\Containers\PaymentSection\Payment\Exceptions\InvalidWebhookIpException;
use App\Ship\Parents\Actions\Action as ParentAction;
use App\Ship\Parents\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class CryptomusHandleWebhookAction extends ParentAction
{
    public array $data;
    public string $ip;
    public const CRYPTOMUS_IP = '91.227.144.54';

    public function __construct(
        private readonly ChangeInvoiceStatusToFailTask $changeInvoiceStatusToFailTask,
        private readonly ChangeInvoiceStatusToPaidTask $changeInvoiceStatusToPaidTask,
        private readonly AddAccountBalanceTask $addAccountBalanceTask,
    ) {
    }

    /**
     * @throws FailedAddAccountBalanceException
     */
    public function run(CryptomusHandleWebhookDto $dto): void
    {
        Log::channel('cryptomus-webhook')->info('Cryptomus-webhook action process start: ' . Carbon::now('Europe/Moscow'));

        try {
            $this->data = $dto->data;
            $this->ip = $dto->ip;
            $invoiceId = $this->data['order_id'];

            $this->saveWebhookData();

            $this->checkIp();

            $this->checkSign();
        } catch (Throwable $e) {
            Log::channel('cryptomus-webhook')->error($e);
        }

        Log::channel('cryptomus-webhook')->info('Cryptomus-webhook data: ' . json_encode($this->data));

        try {
            if (in_array($this->data['status'], [PaymentManagerConstants::PAID, PaymentManagerConstants::PAID_OVER])) {
                $invoice = $this->changeInvoiceStatusToPaidTask->run($invoiceId);

                if ($invoice->type === InvoiceConstants::PAY_FOR_ADD_BALANCE) {
                    $this->addAccountBalanceTask->run($invoice->team_id, $invoice->total_sum);
                }
            } else {
                $this->changeInvoiceStatusToFailTask->run($invoiceId);
            }
        } catch (Throwable $e) {
            throw new FailedAddAccountBalanceException($e->getMessage());
        }
    }

    /**
     * @throws InvalidWebhookIpException
     */
    private function checkIp(): void
    {
        if ($this->ip !== self::CRYPTOMUS_IP) {
            Log::channel('cryptomus-webhook')->error("$this->ip is not equal to cryptomus ip: 91.227.144.54");
            throw new InvalidWebhookIpException();
        }
    }

    /**
     * @throws InvalidHashException
     */
    private function checkSign(): void
    {
        $apiKey = config('cryptomusPaymentClient.api_key');
        $sign = $this->data['sign'];
        unset($this->data['sign']);

        $hash = md5(base64_encode(json_encode($this->data, JSON_UNESCAPED_UNICODE)) . $apiKey);

        if ($sign !== $hash) {
            Log::channel('cryptomus-webhook')->error("webhook sign: $sign is not equal with real sign: $hash");
            throw new InvalidHashException();
        }
    }

    private function saveWebhookData(): void
    {
        DB::table('webhooks')
            ->insert([
                'id' => Str::uuid()->toString(),
                'payment_system' => PaymentManagerConstants::CRYPTOMUS,
                'data' => json_encode($this->data),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
    }
}
