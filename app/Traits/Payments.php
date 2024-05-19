<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Facades\Log;
use Safaricom\Mpesa\Mpesa;

trait Payments
{
    /**
     * Send an STK push request to a recipient.
     *
     * @param $phone
     * @param $amount
     * @return bool|object
     */
    public function stkPush($phone, $amount)
    {
        $mpesa = new Mpesa();

        $businessShortCode = config('mpesa.shortCode');
        $lipaNaMpesaPasskey = config('mpesa.passKey');
        $transactionType = 'CustomerPayBillOnline';
        $partyB = config('africastalking.paybill');
        $callBackURL = config('mpesa.callbackURL');
        $accountReference = config('africastalking.account');
        $transactionDesc = 'Buying credit using '.$phone.' in '.config('app.name');
        $remarks = '';

        try {
            $json = $mpesa->STKPushSimulation($businessShortCode, $lipaNaMpesaPasskey, $transactionType, $amount, $phone, $partyB, $phone, $callBackURL, $accountReference, $transactionDesc, $remarks);
            $response = json_decode($json);

            if (property_exists($response, 'CheckoutRequestID') && $response->ResponseCode === '0') {
                return (object) [
                    'merchant' => $response->MerchantRequestID,
                    'checkout' => $response->CheckoutRequestID,
                ];
            }

            if (property_exists($response, 'errorCode')) {
                Log::error($response->errorMessage.'.');

                return false;
            }

            Log::debug(print_r($response, true));

            return false;
        } catch (Exception $exception) {
            Log::error($exception);

            return false;
        }
    }

    /**
     * Process the callback from Safaricom
     */
    public function stkCallbackData()
    {
        $mpesa = new Mpesa();
        $response = json_decode($mpesa->getDataFromCallback());
        $stk = $response->Body->stkCallback;

        if ($stk->ResultCode === 0) {
            $meta = $stk->CallbackMetadata->Item;

            foreach ($meta as $item) {
                if (property_exists($item, 'Value')) {
                    if ($item->Name === 'MpesaReceiptNumber') {
                        $metaBuild['mpesaReceiptNumber'] = $item->Value;
                    } elseif ($item->Name === 'PhoneNumber') {
                        $metaBuild['phoneNumber'] = $item->Value;
                    } elseif ($item->Name === 'Amount') {
                        $metaBuild['amount'] = $item->Value;
                    } elseif ($item->Name === 'TransactionDate') {
                        $metaBuild['transactionDate'] = $item->Value;
                    }
                }
            }

            $metaBuild['merchantRequestID'] = $stk->MerchantRequestID;
            $metaBuild['checkoutRequestID'] = $stk->CheckoutRequestID;

            return $metaBuild;
        }

        Log::debug($stk->ResultDesc);

        return false;
    }
}
