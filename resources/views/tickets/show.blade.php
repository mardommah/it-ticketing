@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Ticket #{{ $ticket->id }}</h1>
            <a href="{{ route('tickets.index') }}" class="text-indigo-600 hover:text-indigo-900">&larr; Back to List</a>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2">Reporter Information</h2>
                    <p class="mb-2"><span class="font-bold">Name:</span> {{ $ticket->reporter_name }}</p>
                    <p class="mb-2"><span class="font-bold">Phone:</span> {{ $ticket->from }}</p>
                    <p class="mb-2"><span class="font-bold">Group:</span> {{ $ticket->participant ?? 'N/A' }}</p>
                    <p><span class="font-bold">Date:</span> {{ $ticket->created_at->format('d M Y H:i') }}</p>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2">Ticket Status</h2>
                    <form action="{{ route('tickets.update', $ticket) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="status">
                                Status
                            </label>
                            <select name="status" id="status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                                <option value="pending" {{ $ticket->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="assigned_to">
                                Assign To
                            </label>
                            <select name="assigned_to" id="assigned_to" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="">Unassigned</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $ticket->assigned_to == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="category">
                                Category
                            </label>
                            <input type="text" name="category" id="category" value="{{ $ticket->category }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="e.g. Network, Hardware, Software">
                        </div>

                        <div class="flex items-center justify-between">
                            <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                                Update Ticket
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2">Issue Description</h2>
                <div class="bg-gray-50 p-4 rounded-lg text-gray-800 whitespace-pre-wrap italic">
                    "{{ $ticket->message }}"
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
