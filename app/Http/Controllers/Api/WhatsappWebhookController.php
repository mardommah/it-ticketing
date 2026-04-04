<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsappWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Simple Bearer Token check
        $token = $request->bearerToken();
        if ($token !== config('services.whatsapp.webhook_token')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $data = $request->validate([
            'whatsapp_id' => 'required|string',
            'from' => 'required|string',
            'participant' => 'required|string',
            'pushName' => 'required|string',
            'message' => 'required|string',
            'timestamp' => 'required|numeric',
        ]);

        try {
            $ticket = Ticket::firstOrCreate(
                ['whatsapp_id' => $data['whatsapp_id']],
                [
                    'from' => $data['from'],
                    'participant' => $data['participant'],
                    'reporter_name' => $data['pushName'],
                    'message' => $data['message'],
                    'whatsapp_timestamp' => $data['timestamp'],
                    'status' => 'open',
                ]
            );

            return response()->json([
                'message' => 'Ticket processed successfully',
                'ticket_id' => $ticket->id,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Webhook logic error: ' . $e->getMessage());
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
}
