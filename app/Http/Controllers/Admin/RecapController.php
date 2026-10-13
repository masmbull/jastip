<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Recap;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RecapController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Order::class);

        $recaps = Recap::orderByDesc('updated_at')->get();

        return view('admin.recaps.index', compact('recaps'));
    }

    /**
     * Lembar rekapan baru: kolom & baris default dari request (?from=invoice
     * atau ?customer=), supaya admin bisa rekap pesanan dengan sekali klik.
     */
    public function create(Request $request)
    {
        Gate::authorize('create', Order::class);

        $columns = [
            ['key' => 'pelanggan', 'label' => 'Pelanggan', 'type' => 'text', 'total' => false],
            ['key' => 'produk', 'label' => 'Produk', 'type' => 'text', 'total' => false],
            ['key' => 'qty', 'label' => 'Qty', 'type' => 'number', 'total' => true],
            ['key' => 'po_hari', 'label' => 'PO (hari)', 'type' => 'text', 'total' => false],
            ['key' => 'harga', 'label' => 'Harga', 'type' => 'number', 'total' => true],
        ];

        $rows = [];
        $name = 'Rekapan '.now()->format('d M Y');

        if ($request->filled('order')) {
            $order = Order::with('items')->findOrFail($request->integer('order'));
            Gate::authorize('view', $order);

            $name = 'Rekapan #'.$order->order_number;
            foreach ($order->items as $i => $item) {
                $rows[] = [
                    'key' => 'r'.($i + 1),
                    'cells' => [
                        'pelanggan' => $order->customer_name,
                        'produk' => $item->product_name,
                        'qty' => (string) $item->quantity,
                        'po_hari' => '',
                        'harga' => (string) $item->subtotal,
                    ],
                ];
            }
        }

        return view('admin.recaps.form', [
            'recap' => new Recap(['name' => $name, 'columns' => $columns, 'rows' => $rows]),
            'editing' => false,
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Order::class);

        [$data, $err] = $this->payload($request);
        if ($err) {
            return $err;
        }

        $recap = Recap::create($data);

        return redirect()->route('admin.recaps.show', $recap)
            ->with('success', 'Rekapan berhasil dibuat.');
    }

    public function show(Recap $recap)
    {
        Gate::authorize('viewAny', Order::class);

        return view('admin.recaps.show', compact('recap'));
    }

    public function edit(Recap $recap)
    {
        Gate::authorize('viewAny', Order::class);

        return view('admin.recaps.form', ['recap' => $recap, 'editing' => true]);
    }

    public function update(Request $request, Recap $recap)
    {
        Gate::authorize('viewAny', Order::class);

        [$data, $err] = $this->payload($request);
        if ($err) {
            return $err;
        }

        $recap->update($data);

        return redirect()->route('admin.recaps.show', $recap)
            ->with('success', 'Rekapan berhasil disimpan.');
    }

    public function destroy(Recap $recap)
    {
        Gate::authorize('viewAny', Order::class);
        $recap->delete();

        return redirect()->route('admin.recaps.index')
            ->with('success', 'Rekapan berhasil dihapus.');
    }

    /**
     * Validasi + normalisasi payload form jadi atribut model.
     *
     * @return array{0: array<string, mixed>, 1: RedirectResponse|null}
     */
    private function payload(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'columns' => ['required', 'array', 'min:1'],
            'columns.*.label' => ['nullable', 'string', 'max:100'],
            'columns.*.key' => ['nullable', 'string', 'max:100'],
            'columns.*.type' => ['nullable', 'in:text,number,currency'],
            'columns.*.total' => ['nullable'],
            'rows' => ['nullable', 'array'],
        ], [
            'name.required' => 'Judul rekapan wajib diisi.',
            'columns.required' => 'Tambahkan minimal satu kolom.',
            'columns.min' => 'Tambahkan minimal satu kolom.',
        ]);

        // Data kolom/baris bersarang dibaca mentah: struktur `cells` dinamis
        // sehingga tidak bisa dideklarasikan satu-satu di rules.
        $columns = Recap::normalizeColumns($validated['columns']);

        if (empty($columns)) {
            return [[], back()->withInput()->withErrors(['columns' => 'Minimal satu kolom harus punya nama.'])];
        }

        $rows = Recap::normalizeRows($columns, (array) $request->input('rows', []));

        return [['name' => $validated['name'], 'columns' => $columns, 'rows' => $rows], null];
    }
}
