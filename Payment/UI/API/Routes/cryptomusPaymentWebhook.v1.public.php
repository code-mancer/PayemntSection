<?php

use Illuminate\Support\Facades\Route;
use App\Containers\PaymentSection\Payment\UI\API\Controllers\CryptomusController;

Route::post('/payments/cryptomus/webhook', [CryptomusController::class, 'handleWebhook']);
