<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IT Ticketing System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased">
    <nav class="bg-white shadow">
        <div class="container mx-auto px-4 h-16 flex items-center justify-between">
            <div class="text-xl font-bold text-indigo-600">IT-Ticketing</div>
            <div class="flex space-x-4">
                <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-indigo-600">Dashboard</a>
                <a href="{{ route('tickets.index') }}" class="text-gray-600 hover:text-indigo-600">Tickets</a>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>
</body>
</html>
