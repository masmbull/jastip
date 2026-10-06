<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ShippingService;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function __construct(private readonly ShippingService $shippingService)
    {
    }

    /**
     * Show shipping/ongkir settings page
     */
    public function index()
    {
        $couriers = $this->shippingService->courierSettings();

        return view('admin.shipping.index', compact('couriers'));
    }

    /**
     * Toggle courier enable/disable
     */
    public function toggle(Request $request, string $courierCode)
    {
        $request->validate([
            'enabled' => ['required', 'boolean'],
        ]);

        $this->shippingService->toggleCourier($courierCode, $request->boolean('enabled'));

        return redirect()->route('admin.shipping.index')
            ->with('success', 'Status ekspedisi berhasil diperbarui!');
    }

    /**
     * Update courier pricing
     */
    public function updatePricing(Request $request, string $courierCode)
    {
        $validated = $request->validate([
            'per_kg' => ['required', 'integer', 'min:0'],
            'min_charge' => ['required', 'integer', 'min:0'],
        ], [
            'per_kg.required' => 'Harga per kg wajib diisi.',
            'per_kg.integer' => 'Harga per kg harus angka.',
            'min_charge.required' => 'Biaya minimum wajib diisi.',
            'min_charge.integer' => 'Biaya minimum harus angka.',
        ]);

        $this->shippingService->updateCourierPricing(
            $courierCode,
            $validated['per_kg'],
            $validated['min_charge']
        );

        return redirect()->route('admin.shipping.index')
            ->with('success', 'Tarif ekspedisi berhasil diperbarui!');
    }

    /**
     * Refresh pricing from API
     */
    public function refreshPrices()
    {
        $result = $this->shippingService->updateLivePrices();

        return redirect()->route('admin.shipping.index')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }
}
