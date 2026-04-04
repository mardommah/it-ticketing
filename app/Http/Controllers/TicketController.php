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

        $ticket->update($request->only(['status', 'assigned_to', 'category']));

        return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket updated successfully.');
    }
}
