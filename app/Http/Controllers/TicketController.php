<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    public function dashboard(Request $request)
    {
        $range = $request->get('range', '7days');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if ($range === '7days') {
            $start = now()->subDays(7)->startOfDay();
            $end = now()->endOfDay();
        } elseif ($range === '30days') {
            $start = now()->subDays(30)->startOfDay();
            $end = now()->endOfDay();
        } elseif ($range === 'custom' && $startDate && $endDate) {
            $start = \Carbon\Carbon::parse($startDate)->startOfDay();
            $end = \Carbon\Carbon::parse($endDate)->endOfDay();
        } else {
            // Default to 7 days
            $start = now()->subDays(7)->startOfDay();
            $end = now()->endOfDay();
            $range = '7days';
        }

        $query = Ticket::whereBetween('created_at', [$start, $end]);

        // Analytics Data for Dashboard (Filtered)
        $statusCounts = (clone $query)->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $categoryCounts = (clone $query)->select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->pluck('count', 'category');

        // Chart Data: Daily Counts
        $chartData = (clone $query)->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->pluck('count', 'date');

        // Ensure every day in the range has a value (even 0)
        $period = \Carbon\CarbonPeriod::create($start->toDateString(), $end->toDateString());
        $labels = [];
        $data = [];
        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            $labels[] = $date->format('d M');
            $data[] = $chartData[$dateString] ?? 0;
        }

        // Real SLA Calculation (Target: 24 hours) - Filtered by creation date
        $resolvedQuery = (clone $query)->where('status', 'resolved');
        $totalResolved = $resolvedQuery->count();
        $slaMetCount = (clone $resolvedQuery)
            ->where(function ($query) {
                if (DB::connection()->getDriverName() === 'sqlite') {
                    $query->whereRaw("(strftime('%s', resolved_at) - strftime('%s', created_at)) <= 86400");
                } else {
                    $query->whereRaw("TIMESTAMPDIFF(HOUR, created_at, resolved_at) <= 24");
                }
            })
            ->count();
        
        $slaMetPercent = $totalResolved > 0 ? round(($slaMetCount / $totalResolved) * 100) : 0;
        $slaOverduePercent = $totalResolved > 0 ? 100 - $slaMetPercent : 0;

        return view('dashboard', compact(
            'statusCounts', 
            'categoryCounts', 
            'labels',
            'data',
            'slaMetPercent', 
            'slaOverduePercent',
            'range',
            'startDate',
            'endDate'
        ));
    }

    public function index(Request $request)
    {
        $query = Ticket::latest();

        if ($request->has('group') && $request->group) {
            $query->where('from', $request->group);
        }

        $tickets = $query->paginate(10)->withQueryString();
        $uniqueGroups = Ticket::distinct()->pluck('from');

        return view('tickets.index', compact('tickets', 'uniqueGroups'));
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
            if ($ticket->status === 'resolved') {
                $ticket->resolved_at = now();
                $ticket->save();
            }

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

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();
        return redirect()->route('tickets.index')->with('success', 'Ticket deleted successfully.');
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
