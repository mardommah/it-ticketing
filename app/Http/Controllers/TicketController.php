<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::latest()->paginate(10);
        return view('tickets.index', compact('tickets'));
    }

    public function show(Ticket $ticket)
    {
        $users = User::all();
        return view('tickets.show', compact('ticket', 'users'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $request->validate([
            'status' => 'required|in:open,pending,resolved',
            'assigned_to' => 'nullable|exists:users,id',
            'category' => 'nullable|string',
        ]);

        $oldStatus = $ticket->status;
        $oldAssignedTo = $ticket->assigned_to;

        $ticket->update($request->only(['status', 'assigned_to', 'category']));

        // Record Logs
        if ($oldStatus !== $ticket->status) {
            $ticket->logs()->create([
                'user_id' => auth()->id(),
                'action' => 'status_change',
                'details' => "Status changed from {$oldStatus} to {$ticket->status}",
            ]);
            
            $this->notifyWhatsApp($ticket, "Status update: Your ticket is now {$ticket->status}");
        }

        if ($oldAssignedTo != $ticket->assigned_to) {
            $ticket->logs()->create([
                'user_id' => auth()->id(),
                'action' => 'assignment',
                'details' => $ticket->assigned_to ? "Assigned to user ID {$ticket->assigned_to}" : "Unassigned",
            ]);
        }

        return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket updated successfully.');
    }

    protected function notifyWhatsApp(Ticket $ticket, $message)
    {
        // For now, we just log it. In Feature 3, we would send this to the Node.js bot.
        \Log::info("WhatsApp Notification for Ticket #{$ticket->id} ({$ticket->from}): {$message}");
        
        // Example logic for later:
        // Http::post(config('services.whatsapp.bot_url') . '/send', [
        //     'to' => $ticket->from,
        //     'message' => $message,
        //     'token' => config('services.whatsapp.webhook_token'),
        // ]);
    }
}
