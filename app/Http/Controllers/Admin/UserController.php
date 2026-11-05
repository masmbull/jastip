<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:30'],
            'tier' => ['nullable', 'in:bronze,silver,gold,platinum'],
            'points' => ['nullable', 'integer', 'min:0'],
        ], [
            'name.required' => 'Nama pelanggan wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'phone.required' => 'Nomor WhatsApp pelanggan wajib diisi.',
            'points.integer' => 'Poin harus berupa angka.',
        ]);

        $password = Str::password(10);
        $name = trim($validated['name']);
        $phone = trim($validated['phone']);
        $emailInput = trim((string) ($validated['email'] ?? ''));
        $email = $emailInput !== '' ? strtolower($emailInput) : null;

        $user = DB::transaction(function () use ($validated, $password, $email, $name, $phone) {
            $attributes = [
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'role' => 'customer',
            ];

            if (Schema::hasColumn('users', 'username')) {
                $attributes['username'] = $this->uniqueUsername($email, $name);
            }

            if (Schema::hasColumn('users', 'email_verified_at')) {
                $attributes['email_verified_at'] = $email ? now() : null;
            }

            $user = User::create($attributes);

            if (Schema::hasTable('user_profiles')) {
                $profileAttributes = [
                    'phone' => $phone,
                    'membership_tier' => $validated['tier'] ?? 'bronze',
                    'loyalty_points' => $validated['points'] ?? 0,
                ];

                $user->profile()->updateOrCreate(
                    ['user_id' => $user->id],
                    $profileAttributes,
                );
            }

            return $user;
        });

        return redirect()->route('admin.users.index')
            ->with('success', 'Pelanggan '.$user->name.' berhasil ditambahkan. Password sementara: '.$password);
    }

    /**
     * Username unik dari email jika tersedia, atau nama pelanggan.
     */
    private function uniqueUsername(?string $email, string $name): string
    {
        $source = $email ? Str::before($email, '@') : $name;
        $base = Str::slug($source, '.') ?: 'pelanggan';
        $base = substr($base, 0, 50);
        $username = $base;
        $i = 2;

        while (User::where('username', $username)->exists()) {
            $suffix = (string) $i++;
            $username = substr($base, 0, 50 - strlen($suffix)).$suffix;
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
