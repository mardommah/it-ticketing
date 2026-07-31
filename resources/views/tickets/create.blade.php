@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Create New Ticket</h1>
            <a href="{{ route('tickets.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 transition-colors">&larr; Back to List</a>
        </div>

        @if($errors->any())
            <div class="mb-4 p-4 rounded-xl bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 font-medium">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden transition-colors">
            <div class="p-6">
                <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <label for="reporter_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reporter Name</label>
                        <input id="reporter_name" name="reporter_name" type="text" value="{{ old('reporter_name') }}" required class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 transition-colors" placeholder="e.g. John Doe">
                        @error('reporter_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contact / Phone Number</label>
                        <input id="from" name="from" type="text" value="{{ old('from') }}" required class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 transition-colors" placeholder="e.g. +628123456789 or email">
                        @error('from') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Issue Description</label>
                        <textarea id="message" name="message" rows="6" required class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 transition-colors" placeholder="Describe the issue in detail...">{{ old('message') }}</textarea>
                        @error('message') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category (Optional)</label>
                        <input id="category" name="category" type="text" value="{{ old('category') }}" class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 transition-colors" placeholder="e.g. Network, Hardware, Software">
                        @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="assigned_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Assign To (Optional)</label>
                        <select id="assigned_to" name="assigned_to" class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 transition-colors">
                            <option value="">Unassigned</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('assigned_to') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                        <select id="status" name="status" required class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 transition-colors">
                            <option value="open" {{ old('status', 'open') === 'open' ? 'selected' : '' }}>Open</option>
                            <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="resolved" {{ old('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                        </select>
                        @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-6">
                        <label for="attachments" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Attachments (Optional)</label>
                        <input id="attachments" name="attachments[]" type="file" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" class="block w-full text-sm text-gray-700 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:uppercase file:bg-indigo-600 file:text-white hover:file:bg-indigo-500">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Allowed: JPG, PNG, PDF, DOC, DOCX (Max 5MB each)</p>
                        @error('attachments.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-indigo-500 dark:hover:bg-indigo-600 focus:outline-none transition-colors">
                            Create Ticket
                        </button>
                        <a href="{{ route('tickets.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 transition-colors">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
