<?php

namespace App\Support\Facades;

use App\Models\Customer;
use App\Models\PaymentGateway;
use App\Repository\PaymentTripayRepository;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string updateConfig(array $data) update tripay config
 * @method static void validateConfig() validate tripay config
 * @method static void createTransaction(PaymentGateway $trx, Customer $user)
 * @method static boolean getStatus(PaymentGateway $trx, Customer $user)
 *
 * @see PaymentTripayRepository
 */

 class Tripay extends Facade
 {
     protected static function getFacadeAccessor()
     {
         return PaymentTripayRepository::class;
     }
 }
 