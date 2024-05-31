<?php

namespace App\Http\Controllers;

use App\Models\MpesaStkPush;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function stkPushRequest(Request $request)
    {

        $accountReference = 'Transaction#' . Str::random(10);
        // $amount = $request->input('amount');
        $phone = Str::substrReplace($request->input('phone'), '254', 0, 1);

        $mpesa = new MpesaStkPush();

        // try {
        $mpesa->lipaNaMpesa(amount: $request->input('amount'), phone: $phone);

        // } catch (\Throwable $th) {
        //     echo $th;
        // }
        // $invalid = json_decode($stk);

        // dd($invalid);
        // echo $invalid;

    }

    public function callback_url()
    {
        dd('here we are');
        header("Content-Type: application/json");

        $response = '{
         "ResultCode": 0,
         "ResultDesc": "Confirmation Received Successfully"
     }';

        // DATA
        $mpesaResponse = file_get_contents('php://input');

        // log the response
        $logFile = "M_PESAConfirmationResponse.txt";

        // write to file
        $log = fopen($logFile, "a");

        fwrite($log, $mpesaResponse);
        fclose($log);

        echo $response;
    }

}
