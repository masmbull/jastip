@extends('layouts.admin')
@section('title', 'Pengaturan Ongkir | ' . setting('brand_name'))

@section('content')
<div class="space-y-6">

    <x-page-header title="Pengaturan Ongkir & Ekspedisi" subtitle="Kelola tarif, aktifkan/nonaktifkan ekspedisi, dan sinkronkan harga dari API" class="animate-fade-up">
        <form action="{{ route('admin.shipping.refresh') }}" method="POST">
            @csrf
            <x-btn type="submit" icon="arrow-path">Sinkronkan Harga dari API</x-btn>
        </form>
    </x-page-header>

    @if ($errors->any())
        <div class="p-4 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 rounded-lg">
            <p class="text-red-700 dark:text-red-300 font-semibold mb-2">Terjadi kesalahan:</p>
            <ul class="list-disc list-inside space-y-1 text-red-600 dark:text-red-300">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 rounded-lg text-emerald-700 dark:text-emerald-300 flex items-center gap-2">
            <x-icon name="check" class="w-5 h-5 shrink-0" /> {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 rounded-lg text-red-700 dark:text-red-300 flex items-center gap-2">
            <x-icon name="x" class="w-5 h-5 shrink-0" /> {{ session('error') }}
        </div>
    @endif

    <x-card padded="false" class="animate-fade-up" style="animation-delay: 120ms">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px]">
                <thead>
                    <tr class="bg-[#F1F5F9] dark:bg-[#2e323b] text-left text-xs text-[#64748B] dark:text-[#cbd5e1] uppercase">
                        <th class="px-6 py-3">Ekspedisi</th>
                        <th class="px-6 py-3">Tipe</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Harga/kg</th>
                        <th class="px-6 py-3 text-right">Minimum</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#404854]">
                    @forelse ($couriers as $courier)
                        <tr class="hover:bg-[#f8fafc] dark:hover:bg-[#2e323b] transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9]">
                                {{ $courier['name'] }}
                                <span class="text-xs text-[#94A3B8] dark:text-[#64748B] font-mono">({{ $courier['code'] }})</span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <x-badge :tone="$courier['type'] === 'instant' ? 'danger' : ($courier['type'] === 'kargo' ? 'brand' : 'neutral')">
                                    {{ ucfirst($courier['type']) }}
                                </x-badge>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <form action="{{ route('admin.shipping.toggle', $courier['code']) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="enabled" value="{{ $courier['enabled'] ? '0' : '1' }}">
                                    <button type="submit" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium transition-colors
                                        {{ $courier['enabled']
                                            ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300 hover:bg-emerald-200 dark:hover:bg-emerald-500/25'
                                            : 'bg-[#F1F5F9] text-[#64748B] dark:bg-[#2e323b] dark:text-[#cbd5e1] hover:bg-[#E2E8F0] dark:hover:bg-[#404854]' }}">
                                        <span class="w-2 h-2 rounded-full {{ $courier['enabled'] ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                        {{ $courier['enabled'] ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-orange-600 dark:text-orange-400 text-right num">
                                Rp {{ number_format($courier['per_kg'], 0, ',', '.') }}
                                @if ($courier['per_kg'] !== $courier['default_per_kg'])
                                    <span class="text-xs font-normal text-[#94A3B8] dark:text-[#64748B] block">
                                        (default: Rp {{ number_format($courier['default_per_kg'], 0, ',', '.') }})
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-orange-600 dark:text-orange-400 text-right num">
                                Rp {{ number_format($courier['min_charge'], 0, ',', '.') }}
                                @if ($courier['min_charge'] !== $courier['default_min_charge'])
                                    <span class="text-xs font-normal text-[#94A3B8] dark:text-[#64748B] block">
                                        (default: Rp {{ number_format($courier['default_min_charge'], 0, ',', '.') }})
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button type="button"
                                        onclick="openEditModal('{{ $courier['code'] }}', '{{ $courier['name'] }}', {{ $courier['per_kg'] }}, {{ $courier['min_charge'] }})"
                                        class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-empty-state icon="truck" title="Belum ada ekspedisi terdaftar">
                                    Tambahkan daftar kurir di <code class="px-1.5 py-0.5 rounded bg-[#F1F5F9] dark:bg-[#2e323b] font-mono">config/ekspedisi.php</code>, lalu muat ulang halaman ini.
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>

<!-- Edit Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-lg p-6 w-full max-w-md border border-[#E2E8F0] dark:border-[#404854]">
        <h3 class="text-lg font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-4">Edit Tarif Ekspedisi</h3>

        <form id="editForm" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-2">Ekspedisi</label>
                    <p id="courierName" class="text-[#64748B] dark:text-[#cbd5e1]">-</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-2">Harga per kg (Rp)</label>
                    <input type="number" name="per_kg" id="perKg" required min="0"
                           class="w-full px-4 py-2 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-2">Biaya minimum (Rp)</label>
                    <input type="number" name="min_charge" id="minCharge" required min="0"
                           class="w-full px-4 py-2 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="button" onclick="closeEditModal()"
                        class="flex-1 px-4 py-2 border border-[#E2E8F0] dark:border-[#404854] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg font-medium hover:bg-[#F1F5F9] dark:hover:bg-[#2e323b] transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium transition-colors">
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
        document.getElementById('editForm').action = '{{ route("admin.shipping.update-pricing", ["courier" => "__CODE__"]) }}'.replace('__CODE__', code);
        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    document.getElementById('editModal').addEventListener('click', function(e) {
        if (e.target === this) closeEditModal();
    });
</script>
@endsection