<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class MpesaStkPush extends Model
{
    // use HasFactory;
    protected $consumer_key;
    protected $consumer_secret;
    protected $passKey;
    protected $amount;
    protected $accountReference;
    protected $phone;
    protected $env;
    protected $short_code;
    protected $parent_short_code;
    protected $initiatorName;
    protected $initiatorPassword;

    public function __construct()
    {
        $this->short_code = '174379';
        $this->parent_short_code = '174379';
        $this->consumer_key = 'nk16Y74eSbTaGQgc9WF8j6FigApqOMWr';
        $this->consumer_secret = '40fD1vRXCq90XFaU';
        $this->passKey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
        $this->CallBackUrl = env('MPESA_STK_URL') . env('MPESA_STK_CALLBACK_URL');
        $this->env = 'sandbox';
        $this->initiatorName = 'testapi';
        $this->initiatorPassword = 'safaricom2000';

    }

    public function getPassword()
    {
        $timestamp = Carbon::now()->format('YmdHis');
        $password = base64_encode($this->short_code . "" . $this->passKey . "" . $timestamp);
        return ['password' => $password, 'timestamp' => $timestamp];

    }

    // public function lipaNaMpesa($amount, $phone, $accountReference)
    // {
    //     $this->phone = $phone;
    //     $this->amount = $amount;
    //     $this->accountReference = $accountReference;

    //     $credentials = $this->getPassword();
    //     $Password = $credentials['password'];
    //     $timestamp = $credentials['timestamp'];

    //     $headers = ['Content-Type:application/json; charset=utf8'];

    //     $access_token_url = "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";
    //     // = ($this->env == 'live') ? "https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials" :

    //     $initiate_url = "https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest";
    //     //  ($this->env == "live") ? "https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest" :

    //     $curl = curl_init($access_token_url);
    //     curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    //     curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($curl, CURLOPT_HEADER, false);
    //     curl_setopt($curl, CURLOPT_USERPWD, $this->consumer_key . ':' . $this->consumer_secret);
    //     $result = curl_exec($curl);
    //     $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    //     $result = json_decode($result);
    //     $access_token = $result->access_token;
    //     curl_close($curl);

    //     // Header for STK Push

    //     $stkheader = ['Content-Type:application/json', 'Authorization:Bearer ' . $access_token];
    //     // Initiate transaction
    //     $curl = curl_init();
    //     curl_setopt($curl, CURLOPT_URL, $initiate_url);
    //     curl_setopt($curl, CURLOPT_HEADER, $stkheader); //Setting custom header

    //     $curl_post_data = array(
    //         'BusinessShortCode' => $this->short_code,
    //         'Password' => $Password,
    //         'Timestamp' => $this->timestamp,
    //         'TransactionType' => 'CustomerBuyGoodsOnline',
    //         'Amount' => $this->amount,
    //         'PartyA' => $phone,
    //         'PartyB' => $this->parent_short_code,
    //         'PhoneNumber' => $phone,
    //         'CallBackUrl' => $this->CallBackUrl,
    //         'AccountReference' => $this->accountReference,
    //         'TransactionDesc' => $phone . "has paid" . $this->amount,
    //     );

    //     $data_string = json_encode($curl_post_data);
    //     curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($curl, CURLOPT_POST, true);
    //     curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
    //     $response = curl_exec($curl);

    //     echo $response;

    //     return $response;

    // }

    public function lipaNaMpesa($phone, $amount)
    {

        date_default_timezone_set('Africa/Nairobi');

        # access token
        $consumerKey = 'Keypt7N48B9aSa7P05c3XEY9tTrMO3nw'; //Fill with your app Consumer Key
        $consumerSecret = 'SQhtNXAk1U2ni28V'; // Fill with your app Secret

        # define the variales
        # provide the following details, this part is found on your test credentials on the developer account
        $BusinessShortCode = '174379';
        $Passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';

        /*
        This are your info, for
        $PartyA should be the ACTUAL clients phone number or your phone number, format 2547********
        $AccountRefference, it maybe invoice number, account number etc on production systems, but for test just put anything
        TransactionDesc can be anything, probably a better description of or the transaction
        $Amount this is the total invoiced amount, Any amount here will be
        actually deducted from a clients side/your test phone number once the PIN has been entered to authorize the transaction.
        for developer/test accounts, this money will be reversed automatically by midnight.
         */

        // $PartyA = $_POST['phone']; // This is your phone number,
        // $AccountReference = '2255';
        // $TransactionDesc = 'Test Payment';
        // $Amount = $_POST['amount'];

        # Get the timestamp, format YYYYmmddhms -> 20181004151020
        $Timestamp = date('YmdHis');

        # Get the base64 encoded string -> $password. The passkey is the M-PESA Public Key
        $Password = base64_encode($BusinessShortCode . $Passkey . $Timestamp);

        # header for access token
        $headers = ['Content-Type:application/json; charset=utf8'];

        # M-PESA endpoint urls
        $access_token_url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
        $initiate_url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

        # callback url
        $CallBackURL = env('MPESA_STK_URL').env('MPESA_STK_CALLBACK_URL');
        // dd($CallBackURL);


        $curl = curl_init($access_token_url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_USERPWD, $consumerKey . ':' . $consumerSecret);
        $result = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $result = json_decode($result);
        $access_token = $result->access_token;
        curl_close($curl);

        # header for stk push
        $stkheader = ['Content-Type:application/json', 'Authorization:Bearer ' . $access_token];

        # initiating the transaction
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $initiate_url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $stkheader); //setting custom header

        $curl_post_data = array(
            //Fill in the request parameters with valid values
            'BusinessShortCode' => $BusinessShortCode,
            'Password' => $Password,
            'Timestamp' => $Timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amount,
            'PartyA' => $phone,
            'PartyB' => $BusinessShortCode,
            'PhoneNumber' => $phone,
            'CallBackURL' => $CallBackURL,
            'AccountReference' => '2255',
            'TransactionDesc' => 'Test Payment',

            // $PartyA = $_POST['phone']; // This is your phone number,
            // $AccountReference = '2255';
            // $TransactionDesc = 'Test Payment';
            // $Amount = $_POST['amount'];
        );

        $data_string = json_encode($curl_post_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        $curl_response = curl_exec($curl);
        print_r($curl_response);
        return redirect()->back();

        // echo $curl_response;
    }

    // public function status($transactionCode)
    // {
    //     $type = 4;
    //     $command = 'TransactionStatusQuery';
    //     $remarks = "Transaction Status Query";
    //     $oocassion = "Transaction Status Query";

    // }
    public function callback_url()
    {
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
