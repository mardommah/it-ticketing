<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::latest()->paginate(10);

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        return view('users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'unique:users'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:user,admin'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username,'.$user->id],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['required', 'in:user,admin'],
            'is_active' => ['boolean'],
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }

    public function importTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template-import-users.csv"',
        ];

        return Response::stream(function () {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['name', 'username', 'email', 'password', 'role', 'is_active']);
            fputcsv($out, ['Contoh Nama', 'contoh_user', 'contoh@email.com', 'password123', 'user', '1']);
            fclose($out);
        }, 200, $headers);
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $handle = fopen($request->file('csv_file')->getRealPath(), 'r');
        $header = array_map(
            fn ($h) => strtolower(trim(str_replace("\xEF\xBB\xBF", '', (string) $h))),
            fgetcsv($handle)
        );

        $hasHeader = in_array('username', $header, true) || in_array('name', $header, true);
        if (! $hasHeader) {
            rewind($handle);
            $header = ['name', 'username', 'email', 'password', 'role', 'is_active'];
        }

        $created = 0;
        $errors = [];
        $rowNum = $hasHeader ? 1 : 0;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;

            if (count(array_filter($row, fn ($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }

            $data = array_combine($header, array_pad(array_slice($row, 0, count($header)), count($header), null));

            $name = trim((string) ($data['name'] ?? ''));
            $username = trim((string) ($data['username'] ?? ''));
            $email = trim((string) ($data['email'] ?? ''));
            $password = (string) ($data['password'] ?? '');
            $role = strtolower(trim((string) ($data['role'] ?? ''))) ?: 'user';
            $isActiveRaw = strtolower(trim((string) ($data['is_active'] ?? '1')));
            $isActive = ! in_array($isActiveRaw, ['0', 'false', 'no', 'tidak', 'nonaktif', 'non-aktif'], true);

            $validator = Validator::make(
                [
                    'name' => $name,
                    'username' => $username,
                    'email' => $email,
                    'password' => $password,
                    'role' => $role,
                ],
                [
                    'name' => ['required', 'string', 'max:255'],
                    'username' => ['required', 'string', 'max:50', 'unique:users,username'],
                    'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                    'password' => ['required', 'string', 'min:6'],
                    'role' => ['required', 'in:user,admin'],
                ]
            );

            if ($validator->fails()) {
                $errors[] = "Baris {$rowNum}: ".implode(', ', $validator->errors()->all());

                continue;
            }

            User::create([
                'name' => $name,
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'role' => $role,
                'is_active' => $isActive,
            ]);

            $created++;
        }

        fclose($handle);

        return redirect()->route('users.index')
            ->with($created > 0 ? 'success' : 'error', "{$created} user berhasil diimport.".($errors ? ' '.count($errors).' baris gagal.' : ''))
            ->with('import_errors', $errors);
    }
}
