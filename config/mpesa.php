<?php

return [
    'consumerKey' => env('MPESA_CONSUMER_KEY'),
    'consumerSecret' => env('MPESA_CONSUMER_SECRET'),
    'shortCode' => env('MPESA_SHORT_CODE'),
    'passKey' => env('MPESA_PASS_KEY'),
    'callbackURL' => env('MPESA_STK_URL').env('MPESA_STK_CALLBACK_URL'),
];