<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PublicContactController extends Controller
{
    public function send(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);

        Log::info('Public contact received', $data);

        // Optionally forward to configured mail 'from' address
        $to = config('mail.from.address');
        if ($to) {
            try {
                Mail::raw($data['message'], function ($message) use ($data, $to) {
                    $message->to($to)
                        ->subject('Contacto web: ' . ($data['subject'] ?? 'Nuevo mensaje'));
                    if (!empty($data['email'])) {
                        $message->replyTo($data['email'], $data['name'] ?? null);
                    }
                });
            } catch (\Throwable $e) {
                Log::error('Failed to send contact email: ' . $e->getMessage(), ['exception' => $e]);
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
