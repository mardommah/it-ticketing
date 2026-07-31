@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 space-y-4 md:space-y-0">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Kelola User</h1>

        <div class="flex flex-wrap items-center gap-3">
            <button type="button" x-data @click="$dispatch('open-modal', 'import-users')" class="text-gray-700 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-4 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 transition-colors">
                Import CSV
            </button>
            <a href="{{ route('users.create') }}" class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 font-medium rounded-lg text-sm px-4 py-2.5 dark:bg-indigo-500 dark:hover:bg-indigo-600 focus:outline-none transition-colors">
                + Tambah User
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 rounded-xl bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 font-medium">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 rounded-xl bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 font-medium">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-4 rounded-xl bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 font-medium">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('import_errors') && count(session('import_errors')))
        <div class="mb-4 p-4 rounded-xl bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 text-yellow-800 dark:text-yellow-300 font-medium text-sm">
            <p class="font-semibold mb-1">Baris gagal diimport:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach(session('import_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden transition-colors">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama</th>
                    <th class="px-5 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Username</th>
                    <th class="px-5 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                    <th class="px-5 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
                    <th class="px-5 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 text-sm text-gray-900 dark:text-gray-100">{{ $user->name }}</td>
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 text-sm text-gray-900 dark:text-gray-100">{{ $user->username }}</td>
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 text-sm text-gray-900 dark:text-gray-100">{{ $user->email }}</td>
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 text-sm">
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $user->role === 'admin' ? 'bg-purple-100 dark:bg-purple-900/50 text-purple-800 dark:text-purple-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 text-sm">
                        @if($user->is_active)
                            <span class="text-green-600 dark:text-green-400 font-semibold">Aktif</span>
                        @else
                            <span class="text-red-600 dark:text-red-400 font-semibold">Tidak Aktif</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 text-sm">
                        <div class="flex space-x-3">
                            <a href="{{ route('users.edit', $user) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 font-semibold transition-colors">Edit</a>
                            @if(auth()->id() !== $user->id)
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 font-semibold transition-colors">Hapus</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-4 text-center text-gray-500 dark:text-gray-400">Belum ada user</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-5 bg-white dark:bg-gray-800 border-t dark:border-gray-700 flex flex-col xs:flex-row items-center xs:justify-between transition-colors">
            {{ $users->links() }}
        </div>
    </div>
</div>

<div x-data="{ open: false }"
     @open-modal.window="if ($event.detail === 'import-users') open = true"
     @close-modal.window="if ($event.detail === 'import-users') open = false"
     @keydown.escape.window="open = false">
    <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 opacity-75" @click="open = false"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6 z-10">
                <form method="POST" action="{{ route('users.import') }}" enctype="multipart/form-data">
                    @csrf
                    <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">Import User via CSV</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Kolom CSV: <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">name, username, email, role, is_active</code>. Password otomatis = username.
                        <a href="{{ route('users.import-template') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Download template</a>.
                    </p>
                    <div class="mb-4">
                        <input type="file" name="csv_file" accept=".csv,text/csv" required class="block w-full text-sm text-gray-700 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:uppercase file:bg-indigo-600 file:text-white hover:file:bg-indigo-500" />
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="open = false" class="text-gray-700 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 font-medium rounded-lg text-sm px-4 py-2.5 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 font-medium rounded-lg text-sm px-4 py-2.5 dark:bg-indigo-500 dark:hover:bg-indigo-600 focus:outline-none transition-colors">
                            Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
