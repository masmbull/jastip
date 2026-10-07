<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    /**
     * Show all addresses
     */
    public function index()
    {
        $addresses = Auth::user()->addresses()
            ->latest()
            ->paginate(10);

        return view('frontend.profile.addresses', compact('addresses'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('frontend.profile.address-form');
    }

    /**
     * Store new address
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => 'nullable|string|max:50',
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'street_address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'notes' => 'nullable|string|max:500',
            'is_default' => 'nullable|boolean',
        ]);

        $address = Auth::user()->addresses()->create($validated);

        if ($request->boolean('is_default')) {
            $address->setAsDefault();
        }

        return redirect()->route('profile.addresses')
            ->with('success', 'Alamat berhasil ditambahkan');
    }

    /**
     * Show edit form
     */
    public function edit(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        return view('frontend.profile.address-form', compact('address'));
    }

    /**
     * Update address
     */
    public function update(Request $request, Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'label' => 'nullable|string|max:50',
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'street_address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'notes' => 'nullable|string|max:500',
            'is_default' => 'nullable|boolean',
        ]);

        $address->update($validated);

        if ($request->boolean('is_default')) {
            $address->setAsDefault();
        }

        return redirect()->route('profile.addresses')
            ->with('success', 'Alamat berhasil diperbarui');
    }

    /**
     * Delete address
     */
    public function destroy(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $address->delete();

        return redirect()->back()
            ->with('success', 'Alamat berhasil dihapus');
    }

    /**
     * Set address as default
     */
    public function setDefault(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $address->setAsDefault();

        return redirect()->back()
            ->with('success', 'Alamat default telah diubah');
    }
}
