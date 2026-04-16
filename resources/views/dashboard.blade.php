@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 gap-4">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">System Summary</h1>
        
        <!-- Date Filter Form -->
        <form action="{{ route('dashboard') }}" method="GET" x-data="{ range: '{{ $range }}' }" class="flex flex-wrap items-center gap-2 bg-white dark:bg-gray-800 p-2 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 transition-colors">
            <select name="range" x-model="range" @change="if(range !== 'custom') $el.form.submit()" class="bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-gray-100 text-sm rounded-lg focus:ring-indigo-500 block p-2 transition-colors">
                <option value="7days" {{ $range == '7days' ? 'selected' : '' }}>Last 7 Days</option>
                <option value="30days" {{ $range == '30days' ? 'selected' : '' }}>Last 30 Days</option>
                <option value="custom" {{ $range == 'custom' ? 'selected' : '' }}>Custom Range</option>
            </select>
            
            <div x-show="range === 'custom'" x-cloak class="flex items-center gap-2">
                <input type="date" name="start_date" value="{{ $startDate }}" class="bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-gray-100 text-sm rounded-lg focus:ring-indigo-500 block p-2 transition-colors">
                <span class="text-gray-400">to</span>
                <input type="date" name="end_date" value="{{ $endDate }}" class="bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-gray-100 text-sm rounded-lg focus:ring-indigo-500 block p-2 transition-colors">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white p-2 rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
         <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-colors">
            <h3 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">SLA Performance</h3>
            <div class="flex items-center">
                <div class="flex-1 bg-gray-100 dark:bg-gray-700 rounded-full h-4 overflow-hidden flex">
                    <div class="bg-green-500 h-full" style="width: {{ $slaMetPercent }}%"></div>
                    <div class="bg-red-500 h-full" style="width: {{ $slaOverduePercent }}%"></div>
                </div>
                <span class="ml-3 text-lg font-black text-gray-700 dark:text-gray-200">{{ $slaMetPercent }}%</span>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-colors">
            <h3 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Period Issues</h3>
            <p class="text-4xl font-black text-indigo-600 dark:text-indigo-400">{{ array_sum($statusCounts->toArray()) }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-colors">
            <h3 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Open Issues</h3>
            <p class="text-4xl font-black text-red-500">{{ $statusCounts['open'] ?? 0 }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-colors">
            <h3 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Resolved</h3>
            <p class="text-4xl font-black text-green-500">{{ $statusCounts['resolved'] ?? 0 }}</p>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 mb-8 transition-colors">
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-6">Issue Trends</h2>
        <div class="h-64">
            <canvas id="issueChart"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-colors">
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-6">Status Breakdown</h2>
            <div class="space-y-4">
                @foreach(['open', 'pending', 'resolved'] as $status)
                    <div class="mb-4">
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400 uppercase">{{ $status }}</span>
                            <span class="text-sm font-bold text-gray-700 dark:text-gray-200">{{ $statusCounts[$status] ?? 0 }}</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-{{ $status === 'open' ? 'red' : ($status === 'resolved' ? 'green' : 'yellow') }}-500 h-2 rounded-full" style="width: {{ count($statusCounts) > 0 ? (($statusCounts[$status] ?? 0) / (array_sum($statusCounts->toArray()) ?: 1) * 100) : 0 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-colors">
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-6">Category Distribution</h2>
            <div class="space-y-3">
                @forelse($categoryCounts as $category => $count)
                    <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-900/50 p-3 rounded-lg border border-gray-100 dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $category ?: 'General' }}</span>
                        <span class="bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 text-xs font-black px-2 py-1 rounded-md">{{ $count }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 italic text-center py-4">No data for this period.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let issueChart;
    const ctx = document.getElementById('issueChart').getContext('2d');
    
    function getChartTheme() {
        const isDark = document.documentElement.classList.contains('dark');
        return {
            gridColor: isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)',
            textColor: isDark ? '#9ca3af' : '#6b7280',
            pointBg: '#4f46e5',
            lineColor: '#6366f1',
            fillColor: isDark ? 'rgba(99, 102, 241, 0.1)' : 'rgba(79, 70, 229, 0.1)'
        };
    }

    function initChart() {
        if (issueChart) issueChart.destroy();
        
        const theme = getChartTheme();
        
        issueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [{
                    label: 'Issues Detected',
                    data: {!! json_encode($data) !!},
                    borderColor: theme.lineColor,
                    backgroundColor: theme.fillColor,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: theme.pointBg,
                    pointBorderColor: isDarkmode() ? '#1f2937' : '#ffffff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: isDarkmode() ? '#111827' : '#ffffff',
                        titleColor: isDarkmode() ? '#ffffff' : '#111827',
                        bodyColor: isDarkmode() ? '#d1d5db' : '#4b5563',
                        borderColor: isDarkmode() ? '#374151' : '#e5e7eb',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { 
                            stepSize: 1,
                            color: theme.textColor
                        },
                        grid: { color: theme.gridColor }
                    },
                    x: {
                        ticks: { color: theme.textColor },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    function isDarkmode() {
        return document.documentElement.classList.contains('dark');
    }

    initChart();

    window.addEventListener('dark-mode-toggled', () => {
        setTimeout(initChart, 100); // Small delay for class to propagate
    });
});
</script>
@endsection
