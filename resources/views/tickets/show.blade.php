@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Ticket #{{ $ticket->id }}</h1>
            <a href="{{ route('tickets.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 transition-colors">&larr; Back to List</a>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden p-6 transition-colors">
            @if(session('success'))
                <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-200 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-sm font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">Reporter Information</h2>
                    <p class="mb-3 text-gray-700 dark:text-gray-300"><span class="font-bold text-gray-900 dark:text-white mr-2">Name:</span> {{ $ticket->reporter_name }}</p>
                    <p class="mb-3 text-gray-700 dark:text-gray-300"><span class="font-bold text-gray-900 dark:text-white mr-2">Phone:</span> {{ $ticket->from }}</p>
                    <p class="mb-3 text-gray-700 dark:text-gray-300"><span class="font-bold text-gray-900 dark:text-white mr-2">Group:</span> {{ $ticket->participant ?? 'N/A' }}</p>
                    <p class="text-gray-700 dark:text-gray-300"><span class="font-bold text-gray-900 dark:text-white mr-2">Date:</span> {{ $ticket->created_at->format('d M Y H:i') }}</p>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">Ticket Status</h2>
                    <form action="{{ route('tickets.update', $ticket) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="status">
                                Status
                            </label>
                            <select name="status" id="status" class="shadow appearance-none border dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline transition-colors">
                                <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                                <option value="pending" {{ $ticket->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="assigned_to">
                                Assign To
                            </label>
                            <select name="assigned_to" id="assigned_to" class="shadow appearance-none border dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline transition-colors">
                                <option value="">Unassigned</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $ticket->assigned_to == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="category">
                                Category
                            </label>
                            <input type="text" name="category" id="category" value="{{ $ticket->category }}" class="shadow appearance-none border dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline transition-colors" placeholder="e.g. Network, Hardware, Software">
                        </div>

                        <div class="flex items-center justify-between">
                            <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition-colors" type="submit">
                                Update Ticket
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-8">
                <h2 class="text-sm font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">Issue Description</h2>
                <div class="bg-gray-50 dark:bg-gray-900/50 p-6 rounded-2xl text-gray-800 dark:text-gray-200 whitespace-pre-wrap italic border border-gray-100 dark:border-gray-700 transition-colors">
                    "{{ $ticket->message }}"
                </div>
            </div>

            @if($ticket->attachments && count($ticket->attachments) > 0)
            <div class="mt-8">
                <h2 class="text-sm font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">Attachments</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($ticket->attachments as $attachment)
                    <a href="{{ Storage::url($attachment['path']) }}" target="_blank" class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <div class="flex-shrink-0">
                            @if(str_contains($attachment['mime'], 'image'))
                            <svg class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                            </svg>
                            @else
                            <svg class="w-8 h-8 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                            </svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $attachment['name'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ round($attachment['size'] / 1024, 1) }} KB</p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="mt-6">
                <h2 class="text-sm font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">Add Attachments</h2>
                <form action="{{ route('tickets.update', $ticket) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="{{ $ticket->status }}">
                    <input type="hidden" name="assigned_to" value="{{ $ticket->assigned_to }}">
                    <input type="hidden" name="category" value="{{ $ticket->category }}">
                    <div class="mb-4">
                        <input name="attachments[]" type="file" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" class="block w-full text-sm text-gray-700 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:uppercase file:bg-indigo-600 file:text-white hover:file:bg-indigo-500">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Allowed: JPG, PNG, PDF, DOC, DOCX (Max 5MB each)</p>
                    </div>
                    <button type="submit" class="text-white bg-indigo-600 hover:bg-indigo-700 font-medium rounded-lg text-sm px-4 py-2 transition-colors">Upload</button>
                </form>
            </div>
        </div>
    </div>
</div>
    </div>
</div>
@endsection
