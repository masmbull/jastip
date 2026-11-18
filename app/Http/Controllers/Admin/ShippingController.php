<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ShippingEstimator;
use App\Services\ShippingService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShippingController extends Controller
{
    public function __construct(
        private readonly ShippingService $shippingService,
        private readonly ShippingEstimator $estimator,
    ) {}

    public function index()
    {
        $couriers = $this->shippingService->courierSettings();
        $apiStatus = $this->shippingService->getApiStatus();
        $cities = $this->estimator->cities();
        $provider = $this->shippingService->resolveProvider();

        return view('admin.shipping.index', compact('couriers', 'apiStatus', 'cities', 'provider'));
    }

    public function toggle(Request $request, string $courier)
    {
        $request->validate([
            'enabled' => ['required', 'boolean'],
        ]);

        $this->shippingService->toggleCourier($courier, $request->boolean('enabled'));

        return redirect()->route('admin.shipping.index')
            ->with('success', 'Status ekspedisi berhasil diperbarui.');
    }

    public function updatePricing(Request $request, string $courier)
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
            $courier,
            $validated['per_kg'],
            $validated['min_charge']
        );

        return redirect()->route('admin.shipping.index')
            ->with('success', 'Tarif ekspedisi berhasil diperbarui.');
    }

    public function refreshPrices()
    {
        $result = $this->shippingService->updateLivePrices();

        return redirect()->route('admin.shipping.index')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function saveApiSettings(Request $request)
    {
        $validated = $request->validate([
            'shipping_api_provider' => ['required', Rule::in(['local', 'api.co.id', 'rajaongkir'])],
            'shipping_api_key' => ['nullable', 'string', 'max:255'],
        ]);

        $this->shippingService->saveApiSettings(
            $validated['shipping_api_provider'],
            $validated['shipping_api_key'] ?? null,
        );

        return redirect()->route('admin.shipping.index')
            ->with('success', 'Pengaturan API ongkir disimpan.');
    }
}
