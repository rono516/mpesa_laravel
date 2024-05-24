<?php

namespace App\Http\Controllers;

use App\Events\MpesaCallbackSaved;
use App\Models\PaymentRequest;
use App\Models\User;
use App\Notifications\NewPaymentReceived;
use App\Traits\Payments;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PaymentController extends Controller
{
    use Payments;

    // public function index(Request $request)
    // {
    //     $payments = Auth::user()->payments()
    //         ->when($request->search, function ($query, $search) {
    //             $query->where('receipt', 'like', "%{$search}%")
    //                 ->orWhere('phone', 'like', "%{$search}%")
    //                 ->orWhere('amount', 'like', "%{$search}%");
    //         })
    //         ->latest()
    //         ->paginate(10)
    //         ->withQueryString()
    //         ->through(function ($payment) {
    //             return [
    //                 'id' => $payment->id,
    //                 'phone' => $payment->phone,
    //                 'amount' => $payment->amount,
    //                 'created_at' => $payment->created_at,
    //             ];
    //         });

    //     return Inertia::render('Payments/Index', [
    //         'payments' => $payments,
    //         'filters' => $request->only(['search']),
    //     ]);
    // }

    // public function create()
    // {
    //     return Inertia::render('Payments/Create');
    // }

    public function mpesa_view(){
        $user = Auth::user();
        return view('mpesa')->with([
            'user' => $user
        ]);
    }

    public function store(Request $request)
    {
        $validData = $request->validate([
            'amount' => 'required|numeric',
            'phone' => 'required|numeric|digits:10',
        ]);

        $phone = Str::substrReplace($validData['phone'], '254', 0, 1);

        $response = $this->stkPush($phone, $validData['amount']);

        if (is_object($response)) {
            try {
                Auth::user()->paymentRequests()->create([
                    'phone' => $phone,
                    'amount' => $validData['amount'],
                    'merchant' => $response->merchant,
                    'checkout' => $response->checkout,
                ]);

                return redirect()->route('credit.index')->with('status', [
                    'type' => 'alert-info',
                    'message' => 'Waiting for KES ' . $validData['amount'] . ' payment from ' . $validData['phone'] . '.',
                ]);
            } catch (Exception $ex) {
                Log::error($ex);

                return redirect()->back()->with('status', [
                    'type' => 'alert-danger',
                    'message' => 'Could not save the payment request.',
                ]);
            }
        } else {
            return redirect()->back()->with('status', [
                'type' => 'alert-danger',
                'message' => 'Could not send payment request.',
            ]);
        }
    }

    /**
     * MPESA STK callback URL.
     *
     * @param  Request  $request
     * @return void
     */
    public function stkCallback(Request $request)
    {
        if ($request->isMethod('post')) {
            $meta = $this->stkCallbackData();

            if ($meta) {
                try {
                    $paymentRequest = PaymentRequest::where('merchant', $meta['merchantRequestID'])
                        ->where('checkout', $meta['checkoutRequestID'])
                        ->first();

                    if (!$paymentRequest) {
                        Log::error('Payment request not found. Merchant: ' . $meta['merchantRequestID'] . ', Checkout: ' . $meta['checkoutRequestID']);
                    } else {
                        $user = User::findOrFail($paymentRequest->user_id);

                        $savedPayment = DB::transaction(function () use ($user, $meta, $paymentRequest) {
                            $payment = $user->payments()->create([
                                'merchant' => $meta['merchantRequestID'],
                                'checkout' => $meta['checkoutRequestID'],
                                'receipt' => $meta['mpesaReceiptNumber'],
                                'phone' => $meta['phoneNumber'],
                                'amount' => $meta['amount'],
                                'date' => $meta['transactionDate'],
                            ]);

                            $user->update([
                                'credit' => $user->credit + $meta['amount'],
                                'credit_updated_at' => now(),
                            ]);

                            $paymentRequest->delete();

                            MpesaCallbackSaved::dispatch($user, $payment);

                            return $payment;
                        });

                        // Clear cached dashboard stats
                        Cache::forget($user->uuid . '-creditUtilizationStats');

                        Notification::route('slack', config('slack.notableEventWebhook'))
                            ->notify(new NewPaymentReceived($user, $savedPayment));
                    }
                } catch (Exception $ex) {
                    Log::error($ex);
                }
            } else {
                Log::error(print_r($meta, true));
            }
        } else {
            Log::error('STK callback was not a POST request.');
        }
    }
}
