<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminUserController extends Controller
{
    /**
     * Daftar semua akun admin. Pelanggan tidak ditampilkan di sini.
     */
    public function index()
    {
        $admins = User::where('role', 'admin')
            ->latest()
            ->paginate(15);

        return view('admin.admins.index', compact('admins'));
    }

    /**
     * Tambah admin baru. Password wajib diisi (min. 12 karakter, dua kali).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'username' => ['nullable', 'string', 'max:100', 'unique:users,username'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ], [
            'name.required' => 'Nama admin wajib diisi.',
            'email.required' => 'Email admin wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'username.unique' => 'Username ini sudah dipakai.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 12 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $attributes = [
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'password' => $validated['password'],
            'role' => 'admin',
            'email_verified_at' => now(),
        ];

        if (! empty($validated['username'])) {
            $attributes['username'] = trim($validated['username']);
        }

        $admin = User::create($attributes);

        Log::info('Admin account created', ['id' => $admin->id, 'name' => $admin->name, 'by' => auth()->id()]);

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin '.$admin->name.' berhasil ditambahkan.');
    }

    /**
     * Form edit profil admin + ganti kata sandi.
     */
    public function edit(User $user)
    {
        $this->ensureTargetIsAdmin($user);

        return view('admin.admins.edit', compact('user'));
    }

    /**
     * Ubah nama / email / username admin.
     */
    public function update(Request $request, User $user)
    {
        $this->ensureTargetIsAdmin($user);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'username' => ['nullable', 'string', 'max:100', 'unique:users,username,'.$user->id],
        ], [
            'name.required' => 'Nama admin wajib diisi.',
            'email.required' => 'Email admin wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah dipakai admin lain.',
            'username.unique' => 'Username ini sudah dipakai.',
        ]);

        $user->update([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'username' => $validated['username'] ? trim($validated['username']) : null,
        ]);

        Log::info('Admin account updated', ['id' => $user->id, 'by' => auth()->id()]);

        return redirect()->route('admin.admins.edit', $user)
            ->with('success', 'Data admin '.$user->name.' berhasil disimpan.');
    }

    /**
     * Ganti kata sandi admin. Bila mengganti milik sendiri, wajib memasukkan
     * kata sandi lama. Mengganti admin lain tidak memerlukannya (sifatnya
     * reset darurat oleh admin), tetapi tetap dicatat di log.
     */
    public function password(Request $request, User $user)
    {
        $this->ensureTargetIsAdmin($user);

        $isSelf = $request->user()->id === $user->id;

        $rules = [
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ];
        $messages = [
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi minimal 12 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ];

        if ($isSelf) {
            $rules['current_password'] = ['required', 'current_password'];
            $messages['current_password.required'] = 'Kata sandi lama wajib diisi.';
            $messages['current_password.current_password'] = 'Kata sandi lama salah.';
        }

        $validated = $request->validate($rules, $messages);

        $user->update(['password' => $validated['password']]);

        Log::info('Admin password changed', ['id' => $user->id, 'self' => $isSelf, 'by' => auth()->id()]);

        return redirect()->route('admin.admins.edit', $user)
            ->with('success', $isSelf ? 'Kata sandi Anda berhasil diganti.' : 'Kata sandi '.$user->name.' berhasil direset.');
    }

    /**
     * Hapus admin. Tidak bisa menghapus diri sendiri atau admin terakhir.
     */
    public function destroy(Request $request, User $user)
    {
        $this->ensureTargetIsAdmin($user);

        if ($request->user()->id === $user->id) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }

        if (User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Tidak bisa menghapus admin terakhir. Buat admin lain dulu.');
        }

        Log::warning('Admin account deleted', ['id' => $user->id, 'name' => $user->name, 'by' => auth()->id()]);

        $user->delete();

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin '.$user->name.' berhasil dihapus.');
    }

    private function ensureTargetIsAdmin(User $user): void
    {
        abort_unless($user->isAdmin(), 404);
    }
}
