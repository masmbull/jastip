<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'customer')
            ->with('profile')
            ->latest()
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Tambah pelanggan manual dari admin (akun + profil sekaligus).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'tier' => ['nullable', 'in:bronze,silver,gold,platinum'],
            'points' => ['nullable', 'integer', 'min:0'],
        ], [
            'name.required' => 'Nama pelanggan wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'points.integer' => 'Poin harus berupa angka.',
        ]);

        $password = Str::password(10);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'username' => $this->uniqueUsername($validated['email']),
            'password' => $password, // di-hash otomatis oleh cast 'hashed'
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        // Profil sudah otomatis dibuat oleh UserObserver saat user dibuat,
        // jadi di sini cukup melengkapi datanya.
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'phone' => $validated['phone'] ?? null,
                'membership_tier' => $validated['tier'] ?? 'bronze',
                'loyalty_points' => $validated['points'] ?? 0,
            ],
        );

        return redirect()->route('admin.users.index')
            ->with('success', 'Pelanggan '.$user->name.' berhasil ditambahkan. Password sementara: '.$password);
    }

    /**
     * Username unik dari bagian depan email.
     */
    private function uniqueUsername(string $email): string
    {
        $base = Str::slug(Str::before($email, '@'), '.') ?: 'pelanggan';
        $username = $base;
        $i = 2;

        while (User::where('username', $username)->exists()) {
            $username = $base.$i++;
        }

        return $username;
    }

    public function show(User $user)
    {
        if ($user->isAdmin()) {
            abort(403);
        }
        $user->load('profile', 'orders', 'reviews');

        return view('admin.users.show', compact('user'));
    }

    public function updateRole(Request $request, User $user)
    {
        if ($user->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate(['role' => 'required|in:customer,admin']);
        $user->update(['role' => $validated['role']]);

        return redirect()->back()->with('success', 'Role berhasil diperbarui');
    }

    public function destroy(User $user)
    {
        if ($user->isAdmin()) {
            abort(403);
        }

        $user->delete();

        return redirect()->back()->with('success', 'User berhasil dihapus');
    }
}
