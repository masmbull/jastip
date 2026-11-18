@extends('layouts.admin')
@section('title', 'Pengaturan Ongkir | ' . setting('brand_name'))

@section('content')
@php
    $provider = $provider ?? 'local';
    $apiStatus = $apiStatus ?? ['status' => 'online', 'label' => 'Estimator lokal', 'message' => '', 'last_update' => null, 'cached_couriers' => 0];
@endphp
<div class="space-y-5">

    <x-page-header title="Pengaturan Ongkir & Ekspedisi"
                   subtitle="Tarif lokal gratis by default. Opsional sambungkan API live."
                   class="animate-fade-up">
        <form action="{{ route('admin.shipping.refresh') }}" method="POST">
            @csrf
            <x-btn type="submit" icon="arrow-path">Sinkronkan Tarif</x-btn>
        </form>
    </x-page-header>

    @if ($errors->any())
        <div role="alert" class="rounded-[8px] border-2 border-[#991B1B] bg-[#FEE2E2] px-4 py-3 text-sm text-[#7F1D1D]">
            <p class="mb-1 font-black">Terjadi kesalahan:</p>
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div role="status" class="flex items-center gap-2 rounded-[8px] border-2 border-[#141414] bg-[#D1FAE5] px-4 py-3 text-sm font-bold text-[#065F46]">
            <x-icon name="check" class="h-5 w-5 shrink-0" /> {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div role="alert" class="flex items-center gap-2 rounded-[8px] border-2 border-[#991B1B] bg-[#FEE2E2] px-4 py-3 text-sm font-bold text-[#7F1D1D]">
            <x-icon name="x" class="h-5 w-5 shrink-0" /> {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
        <x-card class="animate-fade-up" style="animation-delay: 80ms">
            <x-admin-section-title icon="bolt" title="Sumber Tarif" />
            <div class="mb-4 flex flex-wrap items-center gap-2 text-sm">
                <span @class([
                    'inline-flex items-center gap-2 rounded-[8px] border-2 border-[#141414] px-3 py-1.5 font-black',
                    'bg-[#D1FAE5] text-[#065F46]' => in_array($apiStatus['status'] ?? '', ['online', 'configured'], true),
                    'bg-[#FEF3C7] text-[#92400E]' => ($apiStatus['status'] ?? '') === 'not_configured',
                    'bg-[#FEE2E2] text-[#991B1B]' => ($apiStatus['status'] ?? '') === 'offline',
                ])>
                    <span class="h-2 w-2 rounded-full bg-current"></span>
                    {{ $apiStatus['label'] ?? 'Lokal' }}
                </span>
                @if(!empty($apiStatus['last_update']))
                    <span class="font-medium text-[#475569]">Terakhir sync: {{ $apiStatus['last_update'] }}</span>
                @endif
                @if(($apiStatus['has_key'] ?? false) && ($apiStatus['key_source'] ?? '') === 'env')
                    <span class="rounded-[8px] border-2 border-[#141414] bg-white px-2 py-1 text-xs font-bold text-[#475569]">key dari .env</span>
                @endif
            </div>
            <p class="mb-4 text-sm font-medium text-[#475569]">{{ $apiStatus['message'] ?? '' }}</p>

            <form method="POST" action="{{ route('admin.shipping.api') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="shipping_api_provider" class="mb-1.5 block text-xs font-black uppercase tracking-wide">Provider</label>
                    <select id="shipping_api_provider" name="shipping_api_provider" class="nb-input w-full">
                        <option value="local" @selected($provider === 'local')>Lokal — estimator zona (gratis, tanpa key)</option>
                        <option value="api.co.id" @selected($provider === 'api.co.id')>api.co.id — 1.000 req/bulan gratis</option>
                        <option value="rajaongkir" @selected($provider === 'rajaongkir')>RajaOngkir Starter — gratis terbatas</option>
                    </select>
                </div>
                <div>
                    <label for="shipping_api_key" class="mb-1.5 block text-xs font-black uppercase tracking-wide">API Key (opsional)</label>
                    <input id="shipping_api_key" type="password" name="shipping_api_key" autocomplete="off"
                           placeholder="Kosongkan jika pakai Lokal"
                           class="nb-input w-full"
                           value="">
                    <p class="mt-1.5 text-xs text-[#64748B]">Key tidak ditampilkan ulang setelah disimpan. Isi ulang hanya saat ganti key.</p>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row sm:justify-end">
                    <x-btn type="submit" variant="ghost" icon="check">Simpan Provider</x-btn>
                </div>
            </form>
            <p class="mt-4 text-xs text-[#64748B]">
                Rekomendasi: pakai <strong class="text-[#141414]">Lokal</strong> sehari-hari.
                Kalau butuh tarif kecamatan live, daftar gratis di
                <a href="https://api.co.id/api-cek-ongkos-kirim/" target="_blank" rel="noopener" class="font-bold underline underline-offset-2">api.co.id</a>
                atau
                <a href="https://rajaongkir.com/" target="_blank" rel="noopener" class="font-bold underline underline-offset-2">RajaOngkir</a>,
                tempel key di sini, lalu Sinkronkan.
            </p>
        </x-card>

        <x-card class="animate-fade-up" style="animation-delay: 120ms">
            <x-admin-section-title icon="search" title="Coba Hitung Cepat" />
            <p class="mb-4 text-sm font-medium text-[#475569]">Pakai tarif efektif saat ini (manual &gt; sync &gt; default).</p>
            <form method="GET" action="{{ route('shipping.check') }}" target="_blank" class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="preview-city" class="mb-1.5 block text-xs font-black uppercase tracking-wide">Kota tujuan</label>
                    <select id="preview-city" name="city" class="nb-input w-full" required>
                        @foreach($cities as $name => $meta)
                            <option value="{{ $name }}" @selected($name === 'Bandung')>{{ $name }} — {{ $meta['province'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="preview-weight" class="mb-1.5 block text-xs font-black uppercase tracking-wide">Berat (kg)</label>
                    <input id="preview-weight" type="number" name="weight" value="1" min="0.1" max="100" step="0.1" class="nb-input w-full" required>
                </div>
                <div class="flex items-end">
                    <x-btn type="submit" class="w-full" icon="external-link">Lihat JSON</x-btn>
                </div>
            </form>
            <p class="mt-3 text-xs text-[#64748B]">
                Halaman publik:
                <a href="{{ route('shipping.index') }}" target="_blank" rel="noopener" class="font-bold underline underline-offset-2">/ongkir</a>
            </p>
        </x-card>
    </div>

    <div class="nb-table-shell animate-fade-up" style="animation-delay: 160ms">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px]">
                <thead>
                    <tr class="nb-table-head text-left text-xs uppercase">
                        <th scope="col" class="px-4 py-3 font-black">Ekspedisi</th>
                        <th scope="col" class="px-4 py-3 font-black">Tipe</th>
                        <th scope="col" class="px-4 py-3 font-black">Status</th>
                        <th scope="col" class="px-4 py-3 text-right font-black">Harga/kg</th>
                        <th scope="col" class="px-4 py-3 text-right font-black">Minimum</th>
                        <th scope="col" class="px-4 py-3 text-center font-black">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#141414]/15 dark:divide-[#FAF6EF]/15">
                    @forelse ($couriers as $courier)
                        <tr class="bg-white transition-colors hover:bg-[#FFFDF0] dark:bg-[#1E1E1E] dark:hover:bg-[#262626]">
                            <td class="px-4 py-3 text-sm">
                                <p class="font-extrabold text-[#141414] dark:text-[#FAF6EF]">{{ $courier['name'] }}</p>
                                <p class="font-mono text-xs text-[#475569]">{{ $courier['code'] }}
                                    @if(($courier['source'] ?? 'default') === 'manual')
                                        · manual
                                    @elseif(($courier['source'] ?? '') === 'sync')
                                        · sync
                                    @endif
                                </p>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <x-badge :tone="$courier['type'] === 'instant' ? 'danger' : ($courier['type'] === 'kargo' ? 'brand' : 'neutral')">
                                    {{ ucfirst($courier['type']) }}
                                </x-badge>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <form action="{{ route('admin.shipping.toggle', $courier['code']) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="enabled" value="{{ $courier['enabled'] ? '0' : '1' }}">
                                    <button type="submit"
                                            class="inline-flex min-h-[40px] items-center gap-2 rounded-[8px] border-2 border-[#141414] px-3 text-xs font-black transition-colors
                                            {{ $courier['enabled']
                                                ? 'bg-[#D1FAE5] text-[#065F46] hover:bg-[#A7F3D0]'
                                                : 'bg-[#F1F5F9] text-[#64748B] hover:bg-[#E2E8F0]' }}">
                                        <span class="h-2 w-2 rounded-full {{ $courier['enabled'] ? 'bg-emerald-600' : 'bg-slate-400' }}"></span>
                                        {{ $courier['enabled'] ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-3 text-right text-sm font-black num text-[#141414] dark:text-[#FFC800]">
                                Rp {{ number_format($courier['per_kg'], 0, ',', '.') }}
                                @if ($courier['per_kg'] !== $courier['default_per_kg'])
                                    <span class="block text-xs font-medium text-[#475569]">
                                        default Rp {{ number_format($courier['default_per_kg'], 0, ',', '.') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right text-sm font-black num text-[#141414] dark:text-[#FFC800]">
                                Rp {{ number_format($courier['min_charge'], 0, ',', '.') }}
                                @if ($courier['min_charge'] !== $courier['default_min_charge'])
                                    <span class="block text-xs font-medium text-[#475569]">
                                        default Rp {{ number_format($courier['default_min_charge'], 0, ',', '.') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button type="button"
                                        onclick="openEditModal(@js($courier['code']), @js($courier['name']), {{ (int) $courier['per_kg'] }}, {{ (int) $courier['min_charge'] }})"
                                        class="nb-btn bg-white px-3 py-1.5 text-sm font-bold text-[#141414] dark:bg-[#1E1E1E] dark:text-[#FAF6EF]">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-empty-state icon="truck" title="Belum ada ekspedisi terdaftar">
                                    Tambahkan daftar kurir di <code class="rounded bg-[#FFF7D6] px-1.5 py-0.5 font-mono text-xs">config/ekspedisi.php</code>.
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-md rounded-[10px] border-2 border-[#141414] bg-[#FAF6EF] p-6 shadow-[6px_6px_0_#141414] dark:border-[#FAF6EF] dark:bg-[#1E1E1E]">
        <h3 class="font-display text-lg font-extrabold text-[#141414] dark:text-[#FAF6EF]">Edit Tarif Ekspedisi</h3>

        <form id="editForm" method="POST" class="mt-4">
            @csrf
            <div class="space-y-4">
                <div>
                    <p class="mb-1 text-xs font-black uppercase tracking-wide text-[#475569]">Ekspedisi</p>
                    <p id="courierName" class="font-extrabold text-[#141414] dark:text-[#FAF6EF]">-</p>
                </div>
                <div>
                    <label for="perKg" class="mb-1.5 block text-xs font-black uppercase tracking-wide">Harga per kg (Rp)</label>
                    <input type="number" name="per_kg" id="perKg" required min="0" class="nb-input w-full">
                </div>
                <div>
                    <label for="minCharge" class="mb-1.5 block text-xs font-black uppercase tracking-wide">Biaya minimum (Rp)</label>
                    <input type="number" name="min_charge" id="minCharge" required min="0" class="nb-input w-full">
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="button" onclick="closeEditModal()"
                        class="nb-btn flex-1 bg-white px-4 py-2 text-sm text-[#141414] dark:bg-[#121212] dark:text-[#FAF6EF]">
                    Batal
                </button>
                <button type="submit" class="nb-btn flex-1 bg-[#FFC800] px-4 py-2 text-sm font-black text-[#141414]">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditModal(code, name, perKg, minCharge) {
        document.getElementById('courierName').textContent = name;
        document.getElementById('perKg').value = perKg;
        document.getElementById('minCharge').value = minCharge;
        document.getElementById('editForm').action = @js(route('admin.shipping.update-pricing', ['courier' => '__CODE__'])).replace('__CODE__', encodeURIComponent(code));
        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeEditModal() {
        const modal = document.getElementById('editModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    document.getElementById('editModal').addEventListener('click', function (e) {
        if (e.target === this) closeEditModal();
    });
</script>
@endsection
