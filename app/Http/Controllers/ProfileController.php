<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Show user profile page
     */
    public function show()
    {
        $user = Auth::user();
        $profile = $user->profile ?? new UserProfile();
        $orders = $user->orders()->latest()->paginate(10);
        $reviews = $user->reviews()->latest()->paginate(10);

        return view('frontend.profile.show', compact('user', 'profile', 'orders', 'reviews'));
    }

    /**
     * Show edit profile form
     */
    public function edit()
    {
        $user = Auth::user();
        $profile = $user->profile ?? new UserProfile();

        return view('frontend.profile.edit', compact('user', 'profile'));
    }

    /**
     * Update profile information
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $profile = $user->profile ?? new UserProfile(['user_id' => $user->id]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:15',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'bio' => 'nullable|string|max:500',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            if ($profile->profile_picture && Storage::disk('public')->exists($profile->profile_picture)) {
                Storage::disk('public')->delete($profile->profile_picture);
            }
            $validated['profile_picture'] = $request->file('profile_picture')->store('profiles', 'public');
        }

        // Update user info
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Update profile info
        $profile->fill([
            'phone' => $validated['phone'],
            'date_of_birth' => $validated['date_of_birth'],
            'gender' => $validated['gender'],
            'bio' => $validated['bio'],
            'profile_picture' => $validated['profile_picture'] ?? $profile->profile_picture,
        ])->save();

        return redirect()->route('profile.show')
            ->with('success', 'Profil Anda telah diperbarui');
    }

    /**
     * Show order history
     */
    public function orders()
    {
        $user = Auth::user();
        $orders = $user->orders()
            ->with('items')
            ->latest()
            ->paginate(15);

        return view('frontend.profile.orders', compact('orders'));
    }

    /**
     * Show order detail
     */
    public function orderDetail(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load('items', 'paymentProof');

        return view('frontend.profile.order-detail', compact('order'));
    }

    /**
     * Show reviews
     */
    public function reviews()
    {
        $user = Auth::user();
        $reviews = $user->reviews()
            ->with('product')
            ->latest()
            ->paginate(10);

        return view('frontend.profile.reviews', compact('reviews'));
    }

    /**
     * Show settings page
     */
    public function settings()
    {
        $user = Auth::user();

        return view('frontend.profile.settings', compact('user'));
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        Auth::user()->update([
            'password' => bcrypt($validated['password']),
        ]);

        return redirect()->route('profile.settings')
            ->with('success', 'Password Anda telah diperbarui');
    }

    /**
     * Update notification preferences
     */
    public function updateNotificationPrefs(Request $request)
    {
        Auth::user()->update([
            'notify_whatsapp' => $request->boolean('notify_whatsapp'),
            'notify_email' => $request->boolean('notify_email'),
        ]);

        return redirect()->route('profile.settings')
            ->with('success', 'Preferensi notifikasi diperbarui');
    }

    /**
     * Show loyalty points
     */
    public function loyalty()
    {
        $user = Auth::user();
        $profile = $user->profile ?? new UserProfile();

        $history = []; // Can add points transaction history later

        return view('frontend.profile.loyalty', compact('profile', 'history'));
    }
}
