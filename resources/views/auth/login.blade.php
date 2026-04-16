@extends('layouts.app')

@section('content')
<div class="min-h-[calc(100vh-64px)] flex items-center justify-center bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <div class="max-w-md w-full bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-xl transition-colors">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Admin Login</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2">Enter your credentials to access the dashboard</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Username</label>
                <input type="text" name="username" id="username" value="{{ old('username') }}" 
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition-all outline-none @error('username') border-red-500 @enderror"
                    placeholder="Enter username" required autofocus>
                @error('username')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Password</label>
                <input type="password" name="password" id="password" 
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition-all outline-none"
                    placeholder="••••••••" required>
            </div>

            <button type="submit" 
                class="w-full bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-white font-bold py-3 rounded-lg transition-colors shadow-lg hover:shadow-indigo-500/30">
                Sign In
            </button>
        </form>
    </div>
</div>
@endsection
