@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 space-y-4 md:space-y-0">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Ticket Recap List</h1>
        
        <div class="flex items-center space-x-4">
            <form action="{{ route('tickets.index') }}" method="GET" class="flex items-center">
                <select name="group" onchange="this.form.submit()" class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 transition-colors">
                    <option value="">All Groups</option>
                    @foreach($uniqueGroups as $group)
                        <option value="{{ $group }}" {{ request('group') == $group ? 'selected' : '' }}>
                            {{ $group }}
                        </option>
                    @endforeach
                </select>
            </form>
            <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs font-semibold px-2.5 py-1 rounded-lg">Total: {{ $tickets->total() }}</span>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden transition-colors">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Reporter
                    </th>
                    <th class="px-5 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Message
                    </th>
                    <th class="px-5 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-5 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Created At
                    </th>
                    <th class="px-5 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tickets as $ticket)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-transparent text-sm">
                        <div class="flex items-center">
                            <div class="ml-3">
                                <p class="text-gray-900 dark:text-gray-100 whitespace-no-wrap font-bold">
                                    {{ $ticket->reporter_name }}
                                </p>
                                <p class="text-gray-600 dark:text-gray-400 whitespace-no-wrap">
                                    {{ $ticket->from }}
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm">
                        <p class="text-gray-900 dark:text-gray-100 whitespace-normal">
                            {{ Str::limit($ticket->message, 100) }}
                        </p>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm">
                        <span class="relative inline-block px-3 py-1 font-semibold text-{{ $ticket->status === 'open' ? 'red' : ($ticket->status === 'resolved' ? 'green' : 'yellow') }}-900 dark:text-{{ $ticket->status === 'open' ? 'red' : ($ticket->status === 'resolved' ? 'green' : 'yellow') }}-100 leading-tight">
                            <span aria-hidden class="absolute inset-0 bg-{{ $ticket->status === 'open' ? 'red' : ($ticket->status === 'resolved' ? 'green' : 'yellow') }}-200 dark:bg-{{ $ticket->status === 'open' ? 'red' : ($ticket->status === 'resolved' ? 'green' : 'yellow') }}-800 opacity-50 rounded-full"></span>
                            <span class="relative uppercase text-xs">{{ $ticket->status }}</span>
                        </span>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm">
                        <p class="text-gray-900 dark:text-gray-100 whitespace-no-wrap">
                            {{ $ticket->created_at->format('d M Y H:i') }}
                        </p>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm">
                        <div class="flex space-x-3">
                            <a href="{{ route('tickets.show', $ticket) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 font-semibold transition-colors">View</a>
                            <form action="{{ route('tickets.destroy', $ticket) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this ticket?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 font-semibold transition-colors">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-5 py-5 bg-white dark:bg-gray-800 border-t dark:border-gray-700 flex flex-col xs:flex-row items-center xs:justify-between transition-colors">
            {{ $tickets->links() }}
        </div>
    </div>
</div>
@endsection
