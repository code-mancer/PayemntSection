<?php

namespace App\Containers\PaymentSection\Payment\UI\API\Requests\CloudPayment;

use App\Ship\Parents\Requests\Request as ParentRequest;

class CryptomusHandleWebhookRequest extends ParentRequest
{
   public function rules(): array
   {
       return [];
   }
}
