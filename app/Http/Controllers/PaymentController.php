<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function index()
    {
        return view('payment');
    }

    public function initialize(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'amount' => 'required|numeric|min:100',
            'name' => 'required|string',
        ]);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('PAYSTACK_SECRET_KEY'),
            'Content-Type' => 'application/json',
        ])->post(env('PAYSTACK_PAYMENT_URL'), [
            'email' => $request->email,
            'amount' => $request->amount * 100,
            'metadata' => [
                'name' => $request->name,
                'amount' => $request->amount,
            ]
        ]);

        if ($response->failed()) {
            return back()->with('error', 'Payment initialization failed. Please try again.');
        }

        return redirect($response['data']['authorization_url']);
    }

    public function handleCallback(Request $request)
    {
        $reference = $request->query('reference');
        
        if (!$reference) {
            return redirect()->route('payment.form')->with('error', 'Payment reference not found.');
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('PAYSTACK_SECRET_KEY'),
            'Content-Type' => 'application/json',
        ])->get(env('PAYSTACK_VERIFICATION_URL') . $reference);

        $responseData = $response->json();

        if (!$response->successful() || !$responseData['status']) {
            return redirect()->route('payment.form')->with('error', 'Payment verification failed.');
        }

        if ($responseData['data']['status'] === 'success') {
            // Extract order details from Paystack response
            $orderDetails = [
                'reference' => $reference,
                'name' => $responseData['data']['metadata']['name'],
                'email' => $responseData['data']['customer']['email'],
                'amount' => $responseData['data']['amount'] / 100,
                'paid_at' => $responseData['data']['paid_at'],
                'channel' => $responseData['data']['channel'],
                'currency' => $responseData['data']['currency'],
            ];
            
            return redirect()->route('order.details')->with('order', $orderDetails);
        }

        return redirect()->route('payment.form')->with('error', 'Payment not successful.');
    }

    public function showOrderDetails()
    {
        if (!session()->has('order')) {
            return redirect()->route('payment.form');
        }
        
        return view('order', ['order' => session('order')]);
    }
}