<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

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

    public function show(User $user)
    {
        if ($user->isAdmin()) abort(403);
        $user->load('profile', 'orders', 'reviews');
        return view('admin.users.show', compact('user'));
    }

    public function updateRole(Request $request, User $user)
    {
        if ($user->isAdmin()) abort(403);

        $validated = $request->validate(['role' => 'required|in:customer,admin']);
        $user->update(['role' => $validated['role']]);
        return redirect()->back()->with('success', 'Role berhasil diperbarui');
    }

    public function destroy(User $user)
    {
        if ($user->isAdmin()) abort(403);

        $user->delete();
        return redirect()->back()->with('success', 'User berhasil dihapus');
    }
}
