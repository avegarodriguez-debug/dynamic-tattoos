<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Stripe\StripeClient;

class PublicPaymentController extends Controller
{
    public function createPaymentIntent(Request $request): JsonResponse
    {
        $data = $request->validate([
            'plan' => 'nullable|string',
            'name' => 'nullable|string',
            'email' => 'nullable|email',
        ]);

        $map = [
            'basico' => 3500,
            'premium' => 5500,
            'top' => 6500,
        ];

        $planKey = $data['plan'] ?? 'premium';
        $amount = $map[$planKey] ?? $map['premium'];

        $stripe = new StripeClient(config('services.stripe.secret'));

        $pi = $stripe->paymentIntents->create([
            'amount' => $amount,
            'currency' => 'eur',
            'payment_method_types' => ['card'],
            'receipt_email' => $data['email'] ?? null,
            'metadata' => [
                'plan' => $planKey,
                'name' => $data['name'] ?? null,
            ],
        ]);

        return response()->json(['clientSecret' => $pi->client_secret]);
    }
}
