<?php

namespace App\Http\Controllers;

use App\Services\ShippingEstimator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShippingController extends Controller
{
    public function __construct(private readonly ShippingEstimator $estimator)
    {
    }

    /**
     * Halaman "Cek Ongkir": kalkulator + tabel referensi 15 ekspedisi
     * + daftar API gratis untuk ongkir & lacak resi.
     */
    public function index(Request $request)
    {
        $city = $request->input('city', 'Jakarta');
        $weight = (float) $request->input('weight', 1);
        $selected = $request->input('courier');

        $cities = $this->estimator->cities();
        if (!isset($cities[$city])) {
            $city = 'Jakarta';
        }

        $results = $this->estimator->estimateAll($city, $weight);
        $selectedResult = $selected ? $this->estimator->estimate($selected, $city, $weight) : null;

        return view('frontend.pages.ongkir', [
            'origin' => $this->estimator->origin(),
            'cities' => $cities,
            'citiesByZone' => $this->estimator->citiesByZone(),
            'zoneLabels' => $this->estimator->zoneLabels(),
            'couriers' => $this->estimator->couriers(),
            'couriersByType' => $this->estimator->couriersByType(),
            'typeLabel' => fn (string $type) => $this->estimator->typeLabel($type),
            'freeApis' => config('ekspedisi.free_apis', []),
            'results' => $results,
            'highlights' => $this->estimator->highlights($results),
            'selectedResult' => $selectedResult,
            'city' => $city,
            'weight' => $weight,
        ]);
    }

    /**
     * Endpoint JSON untuk hitung ongkir tanpa reload halaman.
     */
    public function check(Request $request)
    {
        $data = $request->validate([
            'city' => ['required', 'string', Rule::in(array_keys($this->estimator->cities()))],
            'weight' => ['required', 'numeric', 'min:0.1', 'max:100'],
            'courier' => ['nullable', 'string'],
        ]);

        $results = $this->estimator->estimateAll($data['city'], (float) $data['weight']);

        return response()->json([
            'city' => $data['city'],
            'weight' => (float) $data['weight'],
            'origin' => $this->estimator->origin(),
            'selected' => isset($data['courier'])
                ? $this->estimator->estimate($data['courier'], $data['city'], (float) $data['weight'])
                : null,
            'rows' => array_map(fn (array $row) => [
                'ok' => $row['ok'],
                'code' => $row['courier']['code'],
                'name' => $row['courier']['name'],
                'type' => $row['courier']['type'],
                'type_label' => $this->estimator->typeLabel($row['courier']['type']),
                'price' => $row['price'] ?? null,
                'price_formatted' => $row['price_formatted'] ?? null,
                'etd' => $row['etd'] ?? null,
                'billable_weight' => $row['billable_weight'] ?? null,
                'tracking' => $row['tracking'] ?? false,
                'message' => $row['message'] ?? null,
            ], $results),
            'highlights' => $this->estimator->highlights($results),
        ]);
    }
}
