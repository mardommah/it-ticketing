<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\ExcludedNumber;
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

        // Check if the sender or group is excluded
        $isExcluded = ExcludedNumber::where('number', $data['from'])
            ->orWhere('number', $data['participant'])
            ->exists();

        if ($isExcluded) {
            Log::info("Ignored message from excluded number: {$data['from']} / {$data['participant']}");
            return response()->json([
                'message' => 'Number is excluded from creating tickets',
            ], 200); // 200 OK to acknowledge receipt so bot doesn't retry
        }

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
