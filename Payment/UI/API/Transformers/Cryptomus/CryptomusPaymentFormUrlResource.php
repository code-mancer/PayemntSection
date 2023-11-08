<?php

namespace App\Containers\PaymentSection\Payment\UI\API\Transformers\Cryptomus;

use App\Ship\Parents\Resources\JsonResource as ParentJsonResource;

class CryptomusPaymentFormUrlResource extends ParentJsonResource
{
    public function toArray($request): array
    {
        return [
            'url' => $this->resource->url,
        ];
    }
}
