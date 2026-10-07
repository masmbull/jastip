@extends('layouts.admin')
@section('title', 'Pengaturan Ongkir')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-[#1E293B]">Pengaturan Ongkir & Ekspedisi</h1>
        <p class="text-[#64748B] mt-2">Kelola tarif, aktifkan/nonaktifkan ekspedisi, dan sinkronkan harga dari API</p>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-red-700 font-semibold mb-2">Terjadi kesalahan:</p>
            <ul class="list-disc list-inside space-y-1 text-red-600">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-700">
            ✓ {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
            ✗ {{ session('error') }}
        </div>
    @endif

    <!-- Refresh Button -->
    <div class="mb-6 flex gap-3">
        <form action="{{ route('admin.shipping.refresh') }}" method="POST">
            @csrf
            <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium transition-colors">
                🔄 Sinkronkan Harga dari API
            </button>
        </form>
    </div>

    <!-- Couriers Table -->
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-[#F1F5F9] border-b border-[#E2E8F0]">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-[#1E293B]">Ekspedisi</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-[#1E293B]">Tipe</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-[#1E293B]">Status</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-[#1E293B]">Harga/kg</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-[#1E293B]">Minimum</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold text-[#1E293B]">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E2E8F0]">
                @forelse ($couriers as $courier)
                    <tr class="hover:bg-[#F8FAFC] transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-[#1E293B]">
                            {{ $courier['name'] }}
                            <span class="text-xs text-[#94A3B8]">({{ $courier['code'] }})</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-[#64748B]">
                            <span class="inline-block px-2 py-1 rounded text-xs font-semibold
                                {{ $courier['type'] === 'instant' ? 'bg-red-100 text-red-700' : 
                                   ($courier['type'] === 'kargo' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700') }}">
                                {{ ucfirst($courier['type']) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <form action="{{ route('admin.shipping.toggle', $courier['code']) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="enabled" value="{{ $courier['enabled'] ? '0' : '1' }}">
                                <button type="submit" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-medium transition-colors
                                    {{ $courier['enabled'] 
                                        ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' 
                                        : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                    <span class="w-2 h-2 rounded-full {{ $courier['enabled'] ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                    {{ $courier['enabled'] ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-orange-600">
                            Rp {{ number_format($courier['per_kg'], 0, ',', '.') }}
                            @if ($courier['per_kg'] !== $courier['default_per_kg'])
                                <span class="text-xs text-[#94A3B8] block">
                                    (default: Rp {{ number_format($courier['default_per_kg'], 0, ',', '.') }})
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-orange-600">
                            Rp {{ number_format($courier['min_charge'], 0, ',', '.') }}
                            @if ($courier['min_charge'] !== $courier['default_min_charge'])
                                <span class="text-xs text-[#94A3B8] block">
                                    (default: Rp {{ number_format($courier['default_min_charge'], 0, ',', '.') }})
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button onclick="openEditModal('{{ $courier['code'] }}', '{{ $courier['name'] }}', {{ $courier['per_kg'] }}, {{ $courier['min_charge'] }})" 
                                    class="text-blue-600 hover:text-blue-800 font-medium">
                                Edit
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-[#94A3B8]">
                            Tidak ada data ekspedisi
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-bold text-[#1E293B] mb-4">Edit Tarif Ekspedisi</h3>
        
        <form id="editForm" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-[#1E293B] mb-2">Ekspedisi</label>
                    <p id="courierName" class="text-[#64748B]">-</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#1E293B] mb-2">Harga per kg (Rp)</label>
                    <input type="number" name="per_kg" id="perKg" required min="0"
                           class="w-full px-4 py-2 border border-[#E2E8F0] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#1E293B] mb-2">Biaya minimum (Rp)</label>
                    <input type="number" name="min_charge" id="minCharge" required min="0"
                           class="w-full px-4 py-2 border border-[#E2E8F0] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="button" onclick="closeEditModal()" 
                        class="flex-1 px-4 py-2 border border-[#E2E8F0] text-[#1E293B] rounded-lg font-medium hover:bg-[#F1F5F9] transition-colors">
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
