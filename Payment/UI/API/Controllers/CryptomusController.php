<?php

namespace App\Containers\PaymentSection\Payment\UI\API\Controllers;

use App\Containers\AppSection\Account\Exceptions\FailedAddAccountBalanceException;
use App\Containers\AppSection\Subscription\Exceptions\FailedChangeInvoiceStatusToFailException;
use App\Containers\AppSection\Subscription\Exceptions\FailedChangeInvoiceStatusToPaidException;
use App\Containers\PaymentSection\Payment\Actions\CryptomusHandleWebhookAction;
use App\Containers\PaymentSection\Payment\Dtos\CryptomusHandleWebhookDto;
use App\Containers\PaymentSection\Payment\Exceptions\InvalidHashException;
use App\Containers\PaymentSection\Payment\Exceptions\InvalidWebhookIpException;
use App\Containers\PaymentSection\Payment\UI\API\Requests\CloudPayment\CryptomusHandleWebhookRequest;
use App\Ship\Parents\Controllers\ApiController as ParentApiController;
use Illuminate\Http\JsonResponse;

class CryptomusController extends ParentApiController
{
    /**
     * @throws FailedAddAccountBalanceException
     */
    public function handleWebhook(
        CryptomusHandleWebhookRequest $request,
        CryptomusHandleWebhookAction $action,
    ): JsonResponse {
        $dto = CryptomusHandleWebhookDto::fromRequest($request);

        $action->run($dto);

        return $this->responseSuccess();
    }
}
