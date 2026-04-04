@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Issue Analysis Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Tickets by Status</h2>
            <div class="space-y-4">
                @foreach(['open', 'pending', 'resolved'] as $status)
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-600 uppercase">{{ $status }}</span>
                            <span class="text-sm font-medium text-gray-600">{{ $statusCounts[$status] ?? 0 }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-{{ $status === 'open' ? 'red' : ($status === 'resolved' ? 'green' : 'yellow') }}-600 h-2.5 rounded-full" style="width: {{ count($statusCounts) > 0 ? (($statusCounts[$status] ?? 0) / array_sum($statusCounts->toArray()) * 100) : 0 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Tickets by Category</h2>
            <div class="space-y-2">
                @forelse($categoryCounts as $category => $count)
                    <div class="flex justify-between items-center border-b pb-2">
                        <span class="text-gray-700">{{ $category ?: 'Uncategorized' }}</span>
                        <span class="bg-indigo-100 text-indigo-800 text-xs font-semibold px-2.5 py-0.5 rounded">{{ $count }}</span>
                    </div>
                @empty
                    <p class="text-gray-500 italic">No categories tracked yet.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Recent Productivity</h2>
            <div class="space-y-2">
                @forelse($ticketsPerDay as $date => $count)
                    <div class="flex justify-between items-center border-b pb-2">
                        <span class="text-gray-700">{{ \Carbon\Carbon::parse($date)->format('D, d M') }}</span>
                        <span class="font-bold text-gray-900">{{ $count }}</span>
                    </div>
                @empty
                    <p class="text-gray-500 italic">No recent activity.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Placeholder for ChartJS or similar -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">SLA Performance (Simulation)</h2>
        <div class="flex items-center space-x-4">
            <div class="flex-1 bg-gray-200 rounded-full h-8 overflow-hidden flex">
                <div class="bg-green-500 h-full flex items-center justify-center text-white text-xs font-bold" style="width: 75%">75% Met</div>
                <div class="bg-red-500 h-full flex items-center justify-center text-white text-xs font-bold" style="width: 25%">25% Overdue</div>
            </div>
        </div>
        <p class="mt-4 text-sm text-gray-600 italic">This section will show real SLA data based on ticket resolution times in future updates.</p>
    </div>
</div>
@endsection
