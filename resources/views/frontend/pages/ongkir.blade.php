@extends('layouts.app')

@section('title', 'Cek Ongkir & Lacak Resi')

@php
    $metaDescription = 'Cek estimasi ongkir dari Jakarta ke seluruh Indonesia untuk 15 ekspedisi (JNE, J&T, SiCepat, Ninja, AnterAja, TIKI, POS, Wahana, Lion Parcel, ID Express, SAP, J&T Cargo, Paxel, GoSend, GrabExpress) plus cara lacak resi.';
@endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="{ tab: 'kalkulator' }">

    {{-- Header --}}
    <header class="mb-10 animate-fade-up">
        <div class="mb-4">
            <x-badge tone="brand">
                <x-icon name="truck" class="w-3.5 h-3.5" /> 15 Ekspedisi Nasional
            </x-badge>
        </div>
        <h1 class="text-3xl md:text-4xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">Cek Ongkir &amp; Lacak Resi</h1>
        <p class="mt-3 text-[#64748B] dark:text-[#cbd5e1] max-w-3xl leading-relaxed">
            Hitung estimasi biaya kirim dari <strong class="text-[#1E293B] dark:text-[#f1f5f9]">{{ $origin['label'] }}</strong> ke kota tujuanmu,
            bandingkan {{ collect($enabledByType)->flatten(1)->count() }} ekspedisi aktif, lalu lacak resi langsung dari halaman ini.
            Estimasi memakai tarif referensi pasar, angka final mengikuti tarif kurir saat order.
        </p>
    </header>

    {{-- Tab navigation --}}
    <nav class="flex flex-wrap gap-1 mb-8 border-b border-[#E2E8F0] dark:border-[#404854]" role="tablist">
        @foreach([
            'kalkulator' => 'Kalkulator Ongkir',
            'ekspedisi' => 'Daftar Ekspedisi',
            'resi' => 'Lacak Resi',
            'api' => 'API Gratis',
        ] as $key => $label)
            <button type="button" @click="tab = '{{ $key }}'" role="tab" :aria-selected="tab === '{{ $key }}'"
                    :class="tab === '{{ $key }}' ? 'border-[#06B6D4] text-[#0891B2]' : 'border-transparent text-[#64748B] dark:text-[#cbd5e1] hover:text-[#0891B2]'"
                    class="min-h-[44px] px-4 py-3 text-sm font-semibold border-b-2 -mb-px transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300 rounded-t">{{ $label }}</button>
        @endforeach
    </nav>

    {{-- ================= KALKULATOR ================= --}}
    <section x-show="tab === 'kalkulator'" x-cloak>
        <form method="GET" action="{{ route('shipping.index') }}"
              class="bg-white dark:bg-[#23252b] rounded-2xl border border-[#E2E8F0] dark:border-[#404854] p-6 sm:p-8 mb-8 transition-colors">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label for="city" class="block text-xs font-semibold uppercase tracking-wide text-[#64748B] dark:text-[#cbd5e1] mb-2">Kota Tujuan</label>
                    <select name="city" id="city"
                            class="w-full rounded-xl border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] px-3 py-2.5 text-sm text-[#1E293B] dark:text-[#f1f5f9] focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-[#0891B2]">
                        @foreach($citiesByZone as $zone => $zoneCities)
                            <optgroup label="Zona {{ $zone }}: {{ $zoneLabels[$zone] ?? '' }}">
                                @foreach($zoneCities as $zoneCity)
                                    <option value="{{ $zoneCity }}" @selected($zoneCity === $city)>{{ $zoneCity }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="weight" class="block text-xs font-semibold uppercase tracking-wide text-[#64748B] dark:text-[#cbd5e1] mb-2">Berat Paket (kg)</label>
                    <input type="number" name="weight" id="weight" min="0.1" max="100" step="0.1"
                           value="{{ $weight }}"
                           class="w-full rounded-xl border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] px-3 py-2.5 text-sm text-[#1E293B] dark:text-[#f1f5f9] num focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-[#0891B2]">
                    <p class="mt-1 text-xs text-[#64748B] dark:text-[#cbd5e1]">Pembulatan kurir: per 0,5 kg (kargo per 1 kg).</p>
                </div>

                <div>
                    <label for="courier" class="block text-xs font-semibold uppercase tracking-wide text-[#64748B] dark:text-[#cbd5e1] mb-2">Ekspedisi (opsional)</label>
                    <select name="courier" id="courier"
                            class="w-full rounded-xl border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] px-3 py-2.5 text-sm text-[#1E293B] dark:text-[#f1f5f9] focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-[#0891B2]">
                        <option value="">Bandingkan semua</option>
                        @foreach($enabledByType as $type => $group)
                            <optgroup label="{{ $typeLabel($type) }}">
                                @foreach($group as $c)
                                    <option value="{{ $c['code'] }}" @selected(request('courier') === $c['code'])>{{ $c['name'] }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                <button type="submit"
                        class="min-h-[44px] px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-full text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300">
                    Hitung Ongkir
                </button>
                <a href="{{ route('shipping.index') }}"
                   class="inline-flex items-center min-h-[44px] px-6 py-2.5 bg-white dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] text-[#64748B] dark:text-[#cbd5e1] hover:border-orange-300 hover:text-[#0891B2] font-semibold rounded-full text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300">
                    Reset
                </a>
            </div>
        </form>

        {{-- Sorotan: termurah & tercepat --}}
        @if($highlights['cheapest'])
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                <div class="bg-white dark:bg-[#23252b] rounded-2xl border border-[#E2E8F0] dark:border-[#404854] p-5 flex items-start gap-4 transition-colors">
                    <div class="w-11 h-11 rounded-xl bg-orange-100 dark:bg-orange-500/20 flex items-center justify-center shrink-0">
                        <x-icon name="banknotes" class="w-5 h-5 text-[#0E7490] dark:text-[#67e8f9]" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs uppercase tracking-wide text-[#64748B] dark:text-[#cbd5e1]">Termurah</p>
                        <p class="font-bold text-[#1E293B] dark:text-[#f1f5f9]">{{ $highlights['cheapest']['courier']['name'] }}</p>
                        <p class="text-[#0E7490] dark:text-[#67e8f9] font-bold text-xl num">{{ $highlights['cheapest']['price_formatted'] }}</p>
                        <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-1">Estimasi {{ $highlights['cheapest']['etd'] }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-[#23252b] rounded-2xl border border-[#E2E8F0] dark:border-[#404854] p-5 flex items-start gap-4 transition-colors">
                    <div class="w-11 h-11 rounded-xl bg-orange-100 dark:bg-orange-500/20 flex items-center justify-center shrink-0">
                        <x-icon name="bolt" class="w-5 h-5 text-[#0E7490] dark:text-[#67e8f9]" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs uppercase tracking-wide text-[#64748B] dark:text-[#cbd5e1]">Tercepat</p>
                        <p class="font-bold text-[#1E293B] dark:text-[#f1f5f9]">{{ $highlights['fastest']['courier']['name'] }}</p>
                        <p class="text-[#0E7490] dark:text-[#67e8f9] font-bold text-xl num">{{ $highlights['fastest']['price_formatted'] }}</p>
                        <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-1">Estimasi {{ $highlights['fastest']['etd'] }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Tabel hasil --}}
        <div class="bg-white dark:bg-[#23252b] rounded-2xl border border-[#E2E8F0] dark:border-[#404854] overflow-hidden transition-colors">
            <div class="px-6 py-4 border-b border-[#E2E8F0] dark:border-[#404854] flex flex-wrap items-center justify-between gap-3">
                <h2 class="font-bold text-[#1E293B] dark:text-[#f1f5f9]">
                    Estimasi ke {{ $city }}
                    <span class="font-normal text-[#64748B] dark:text-[#cbd5e1] text-sm num">&middot; {{ $results[0]['province'] ?? '' }} &middot; {{ $weight }} kg</span>
                </h2>
                @if($results[0]['ok'] ?? false)
                    <x-badge tone="brand">{{ $results[0]['zone_label'] }}</x-badge>
                @endif
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-[#F1F5F9] dark:bg-[#2e323b] text-[#64748B] dark:text-[#cbd5e1]">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold">Ekspedisi</th>
                            <th class="px-4 py-3 text-left font-semibold">Layanan</th>
                            <th class="px-4 py-3 text-right font-semibold">Per kg</th>
                            <th class="px-4 py-3 text-right font-semibold">Estimasi Biaya</th>
                            <th class="px-4 py-3 text-left font-semibold">ETD</th>
                            <th class="px-4 py-3 text-center font-semibold">Lacak Resi</th>
                            <th class="px-6 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#404854]">
                        @foreach($results as $row)
                            <tr class="{{ $row['ok'] ? 'hover:bg-orange-50/40 dark:hover:bg-orange-500/10' : 'bg-[#F8FAFC] dark:bg-[#2e323b]' }} transition-colors">
                                <td class="px-6 py-4">
                                    <span class="font-semibold {{ $row['ok'] ? 'text-[#1E293B] dark:text-[#f1f5f9]' : 'text-[#64748B] dark:text-[#cbd5e1]' }}">{{ $row['courier']['name'] }}</span>
                                    <span class="block text-xs text-[#64748B] dark:text-[#cbd5e1]">{{ $typeLabel($row['courier']['type']) }}</span>
                                </td>
                                <td class="px-4 py-4 text-xs text-[#64748B] dark:text-[#cbd5e1]">{{ $row['courier']['services'] }}</td>
                                <td class="px-4 py-4 text-right text-[#64748B] dark:text-[#cbd5e1] num">
                                    {{ $row['ok'] ? $row['per_kg_formatted'] : '-' }}
                                </td>
                                <td class="px-4 py-4 text-right">
                                    @if($row['ok'])
                                        <span class="font-bold text-[#0E7490] dark:text-[#67e8f9] num">{{ $row['price_formatted'] }}</span>
                                        <span class="block text-xs text-[#64748B] dark:text-[#cbd5e1] num">{{ $row['billable_weight'] }} kg terhitung</span>
                                    @else
                                        <span class="text-xs text-[#64748B] dark:text-[#cbd5e1]">Tidak tersedia</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-xs text-[#64748B] dark:text-[#cbd5e1]">
                                    {{ $row['ok'] ? $row['etd'] : ($row['message'] ?? '-') }}
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @if($row['tracking'])
                                        <x-badge tone="success">
                                            <x-icon name="check" class="w-3.5 h-3.5" /> Bisa
                                        </x-badge>
                                    @else
                                        <x-badge tone="neutral">Tidak</x-badge>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($row['ok'])
                                        <a href="{{ route('shipping.index', ['city' => $city, 'weight' => $weight, 'courier' => $row['courier']['code']]) }}"
                                           class="text-xs font-semibold text-[#0E7490] dark:text-[#67e8f9] hover:underline focus:outline-none focus:ring-2 focus:ring-orange-300 rounded">Detail</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <p class="px-6 py-4 text-xs text-[#64748B] dark:text-[#cbd5e1] border-t border-[#E2E8F0] dark:border-[#404854]">
                * Estimasi berbasis tarif referensi pasar dari {{ $origin['city'] }}, dibulatkan ke kelipatan Rp 500.
                Tarif asli tiap kurir bisa berbeda per kecamatan. Cek halaman resmi kurir atau hubungi admin.
            </p>
        </div>

        {{-- Detail ekspedisi terpilih --}}
        @if($selectedResult && ($selectedResult['ok'] ?? false))
            <div class="mt-8 bg-white dark:bg-[#23252b] rounded-2xl border border-[#E2E8F0] dark:border-[#404854] p-6 sm:p-8 transition-colors">
                <h2 class="font-bold text-[#1E293B] dark:text-[#f1f5f9] text-lg flex items-center gap-2">
                    {{ $selectedResult['courier']['name'] }} <x-icon name="arrow-right" class="w-5 h-5" /> {{ $selectedResult['city'] }}
                </h2>
                <dl class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                    <div>
                        <dt class="text-xs uppercase text-[#64748B] dark:text-[#cbd5e1]">Estimasi biaya</dt>
                        <dd class="font-bold text-[#0E7490] dark:text-[#67e8f9] text-lg num">{{ $selectedResult['price_formatted'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase text-[#64748B] dark:text-[#cbd5e1]">Berat dihitung</dt>
                        <dd class="font-semibold text-[#1E293B] dark:text-[#f1f5f9] num">{{ $selectedResult['billable_weight'] }} kg</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase text-[#64748B] dark:text-[#cbd5e1]">Zona</dt>
                        <dd class="font-semibold text-[#1E293B] dark:text-[#f1f5f9]">{{ $selectedResult['zone_label'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase text-[#64748B] dark:text-[#cbd5e1]">ETD</dt>
                        <dd class="font-semibold text-[#1E293B] dark:text-[#f1f5f9]">{{ $selectedResult['etd'] }}</dd>
                    </div>
                </dl>
                <p class="mt-4 text-sm text-[#64748B] dark:text-[#cbd5e1]">{{ $selectedResult['courier']['notes'] }}</p>

                <div class="mt-5">
                    <p class="text-xs uppercase tracking-wide text-[#64748B] dark:text-[#cbd5e1] mb-2">Link lacak resi &amp; halaman resmi</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($selectedResult['tracking_links'] as $link)
                            <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer"
                               class="text-xs font-semibold px-4 py-2.5 min-h-[44px] inline-flex items-center rounded-full border border-[#E2E8F0] dark:border-[#404854] text-[#64748B] dark:text-[#cbd5e1] hover:border-orange-300 hover:text-[#0891B2] transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300">
                                {{ $link['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <a href="{{ whatsapp_url(setting('whatsapp')) }}" target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center min-h-[44px] mt-6 px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-full text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300">
                    Tanya Admin soal Ongkir Ini
                </a>
            </div>
        @endif
    </section>

    {{-- ================= DAFTAR EKSPEDISI ================= --}}
    <section x-show="tab === 'ekspedisi'" x-cloak>
        <div class="bg-white dark:bg-[#23252b] rounded-2xl border border-[#E2E8F0] dark:border-[#404854] p-6 sm:p-8 transition-colors">
            <h2 class="text-xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">Daftar {{ count($couriers) }} Ekspedisi Indonesia</h2>
            <p class="mt-2 text-sm text-[#64748B] dark:text-[#cbd5e1] max-w-3xl">
                Tarif referensi per kg untuk paket kecil (sekitar 1 kg) dari {{ $origin['city'] }}. Semua ekspedisi di bawah
                ini mendukung lacak resi. Tarif final mengikuti berat &amp; kecamatan tujuan saat order.
            </p>

            @foreach($couriersByType as $type => $group)
                <div class="mt-8">
                    <div class="flex flex-wrap items-center gap-3 mb-3">
                        <h3 class="font-bold text-[#1E293B] dark:text-[#f1f5f9]">{{ $typeLabel($type) }}</h3>
                        <x-badge tone="brand">{{ count($group) }} ekspedisi</x-badge>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-[#E2E8F0] dark:border-[#404854]">
                        <table class="w-full text-sm">
                            <thead class="bg-[#F1F5F9] dark:bg-[#2e323b] text-[#64748B] dark:text-[#cbd5e1]">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold">Ekspedisi</th>
                                    <th class="px-4 py-3 text-left font-semibold">Layanan</th>
                                    <th class="px-4 py-3 text-right font-semibold">Tarif referensi</th>
                                    <th class="px-4 py-3 text-left font-semibold">ETD</th>
                                    <th class="px-4 py-3 text-left font-semibold">Cakupan</th>
                                    <th class="px-4 py-3 text-center font-semibold">Lacak resi</th>
                                    <th class="px-4 py-3 text-left font-semibold">Catatan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#404854]">
                                @foreach($group as $courier)
                                    <tr class="hover:bg-orange-50/40 dark:hover:bg-orange-500/10 transition-colors">
                                        <td class="px-4 py-4 font-semibold text-[#1E293B] dark:text-[#f1f5f9] whitespace-nowrap">{{ $courier['name'] }}</td>
                                        <td class="px-4 py-4 text-xs text-[#64748B] dark:text-[#cbd5e1]">{{ $courier['services'] }}</td>
                                        <td class="px-4 py-4 text-right whitespace-nowrap">
                                            <span class="font-bold text-[#0E7490] dark:text-[#67e8f9] num">{{ format_price($courier['per_kg']) }}/kg</span>
                                            @if(($courier['min_charge'] ?? 0) > ($courier['per_kg'] ?? 0))
                                                <span class="block text-xs text-[#64748B] dark:text-[#cbd5e1] num">min. {{ format_price($courier['min_charge']) }}</span>
                                            @endif
                                            @if(!empty($courier['min_weight_kg']))
                                                <span class="block text-xs text-[#64748B] dark:text-[#cbd5e1] num">min. {{ $courier['min_weight_kg'] }} kg</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-xs text-[#64748B] dark:text-[#cbd5e1] whitespace-nowrap">{{ $courier['etd'] }}</td>
                                        <td class="px-4 py-4 text-xs text-[#64748B] dark:text-[#cbd5e1]">{{ $courier['coverage'] }}</td>
                                        <td class="px-4 py-4 text-center">
                                            @if($courier['tracking'])
                                                <x-badge tone="success">
                                                    <x-icon name="check" class="w-3.5 h-3.5" /> Bisa
                                                </x-badge>
                                            @else
                                                <x-badge tone="neutral">Tidak</x-badge>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-xs text-[#64748B] dark:text-[#cbd5e1] max-w-xs">{{ $courier['notes'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ================= LACAK RESI ================= --}}
    <section x-show="tab === 'resi'" x-cloak>
        @php
            $aggregator = config('ekspedisi.tracking_aggregator');
            $resiPrefixes = config('ekspedisi.resi_prefixes', []);
            $trackingCouriers = collect($couriers)->map(fn ($c) => [
                'code' => $c['code'],
                'name' => $c['name'],
                'page' => $c['tracking_page'],
                'query' => $c['tracking_query'],
            ])->values()->all();
        @endphp

        <div class="bg-white dark:bg-[#23252b] rounded-2xl border border-[#E2E8F0] dark:border-[#404854] p-6 sm:p-8 transition-colors"
             x-data="{
                awb: '',
                code: {{ Js::from($trackingCouriers[0]['code'] ?? 'jne') }},
                couriers: {{ Js::from($trackingCouriers) }},
                prefixes: {{ Js::from($resiPrefixes) }},
                agg: {{ Js::from($aggregator['url']) }},
                aggName: {{ Js::from($aggregator['name']) }},
                autoDetected: false,
                get selected() { return this.couriers.find(c => c.code === this.code) || this.couriers[0] },
                get clean() { return this.awb.trim().replace(/\s+/g, '') },
                get ready() { return this.clean.length >= 6 },
                get aggregatorUrl() { return this.agg.replace('%s', encodeURIComponent(this.clean)) },
                get directUrl() {
                    const t = (this.selected && this.selected.query) ? this.selected.query : this.agg;
                    return t.replace('%s', encodeURIComponent(this.clean));
                },
                detect() {
                    const up = this.clean.toUpperCase();
                    for (const [code, list] of Object.entries(this.prefixes)) {
                        if (list.some(p => up.startsWith(p)) && this.couriers.some(c => c.code === code)) {
                            this.code = code; this.autoDetected = true; return;
                        }
                    }
                    this.autoDetected = false;
                }
             }">
            <h2 class="text-xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">Lacak Resi Pengiriman</h2>
            <p class="mt-2 text-sm text-[#64748B] dark:text-[#cbd5e1] max-w-3xl">
                Masukkan nomor resi, pilih ekspedisi, lalu lacak langsung ke halaman resmi kurir atau lewat aggregator
                {{ $aggregator['name'] }}. Resi kamu juga bisa ditanyakan ke admin lewat WhatsApp.
            </p>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="resi_courier" class="block text-xs font-semibold uppercase tracking-wide text-[#64748B] dark:text-[#cbd5e1] mb-2">Ekspedisi</label>
                    <select id="resi_courier" x-model="code"
                            class="w-full rounded-xl border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] px-3 py-2.5 text-sm text-[#1E293B] dark:text-[#f1f5f9] focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-[#0891B2]">
                        <template x-for="c in couriers" :key="c.code">
                            <option :value="c.code" x-text="c.name"></option>
                        </template>
                    </select>
                    <p class="mt-1 text-xs text-emerald-600 dark:text-emerald-400" x-show="autoDetected" x-cloak>
                        <x-icon name="check" class="w-4 h-4 inline-block" /> Ekspedisi terdeteksi otomatis dari nomor resi.
                    </p>
                </div>
                <div>
                    <label for="resi_awb" class="block text-xs font-semibold uppercase tracking-wide text-[#64748B] dark:text-[#cbd5e1] mb-2">Nomor Resi</label>
                    <input type="text" id="resi_awb" x-model="awb" @input="detect()" autocomplete="off"
                           placeholder="Contoh: JNE1234567890"
                           class="w-full rounded-xl border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] px-3 py-2.5 text-sm text-[#1E293B] dark:text-[#f1f5f9] focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-[#0891B2]">
                    <p class="mt-1 text-xs" :class="ready ? 'text-emerald-600 dark:text-emerald-400' : 'text-[#64748B] dark:text-[#cbd5e1]'"
                       x-text="ready ? 'Nomor resi siap dilacak.' : 'Minimal 6 karakter. Nomor resi biasanya ada di nota/chat admin.'"></p>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                <a :href="ready ? directUrl : '#'" target="_blank" rel="noopener noreferrer"
                   :class="ready ? 'bg-orange-500 hover:bg-orange-600 text-white' : 'bg-[#F1F5F9] dark:bg-[#2e323b] text-[#64748B] dark:text-[#cbd5e1] pointer-events-none'"
                   class="inline-flex items-center min-h-[44px] px-5 py-2.5 font-semibold rounded-full text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300">
                    Lacak Sekarang
                </a>
                <a :href="ready ? aggregatorUrl : '#'" target="_blank" rel="noopener noreferrer"
                   :class="ready ? 'border-orange-300 text-[#0891B2] hover:bg-orange-50 dark:hover:bg-orange-500/10' : 'border-[#E2E8F0] dark:border-[#404854] text-[#64748B] dark:text-[#cbd5e1] pointer-events-none'"
                   class="inline-flex items-center min-h-[44px] px-5 py-2.5 bg-white dark:bg-[#2e323b] border font-semibold rounded-full text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300">
                    Cek via <span x-text="aggName" class="ml-1"></span>
                </a>
                <a :href="selected ? selected.page : '#'" target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center min-h-[44px] px-5 py-2.5 bg-white dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] text-[#64748B] dark:text-[#cbd5e1] hover:border-orange-300 hover:text-[#0891B2] font-semibold rounded-full text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300">
                    Halaman Resmi <span x-text="selected ? selected.name : ''" class="ml-1"></span>
                </a>
            </div>
        </div>
    </section>

    {{-- ================= API GRATIS ================= --}}
    <section x-show="tab === 'api'" x-cloak>
        <div class="bg-white dark:bg-[#23252b] rounded-2xl border border-[#E2E8F0] dark:border-[#404854] p-6 sm:p-8 transition-colors">
            <h2 class="text-xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">API Gratis untuk Ongkir &amp; Lacak Resi</h2>
            <p class="mt-2 text-sm text-[#64748B] dark:text-[#cbd5e1] max-w-3xl">
                Halaman ini sudah jalan tanpa API, estimasi memakai tarif referensi pasar di
                <code class="text-xs bg-[#F1F5F9] dark:bg-[#2e323b] px-1.5 py-0.5 rounded text-[#64748B] dark:text-[#cbd5e1]">config/ekspedisi.php</code>.
                Kalau butuh tarif live sampai level kecamatan atau lacak resi otomatis di dalam website,
                berikut opsi gratis/murah yang paling praktis dipakai bareng Laravel.
            </p>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                @foreach($freeApis as $api)
                    <article class="rounded-xl border border-[#E2E8F0] dark:border-[#404854] p-5 hover:border-orange-300 transition-colors">
                        <div class="flex items-start justify-between gap-3">
                            <a href="{{ $api['url'] }}" target="_blank" rel="noopener noreferrer"
                               class="font-bold text-[#1E293B] dark:text-[#f1f5f9] hover:text-[#0891B2] transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300 rounded">
                                {{ $api['name'] }}
                            </a>
                            <x-badge tone="brand" class="shrink-0">{{ $api['kind'] }}</x-badge>
                        </div>

                        <dl class="mt-4 space-y-2 text-sm">
                            <div>
                                <dt class="text-xs uppercase tracking-wide text-[#64748B] dark:text-[#cbd5e1]">Gratis?</dt>
                                <dd class="text-emerald-700 dark:text-emerald-300 font-medium">{{ $api['free'] }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs uppercase tracking-wide text-[#64748B] dark:text-[#cbd5e1]">Cakupan</dt>
                                <dd class="text-[#64748B] dark:text-[#cbd5e1]">{{ $api['covers'] }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs uppercase tracking-wide text-[#64748B] dark:text-[#cbd5e1]">Catatan</dt>
                                <dd class="text-[#64748B] dark:text-[#cbd5e1]">{{ $api['notes'] }}</dd>
                            </div>
                        </dl>

                        <a href="{{ $api['url'] }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-1 mt-4 text-xs font-semibold text-[#0E7490] dark:text-[#67e8f9] hover:underline focus:outline-none focus:ring-2 focus:ring-orange-300 rounded">
                            Buka dokumentasi <x-icon name="arrow-right" class="w-4 h-4" />
                        </a>
                    </article>
                @endforeach
            </div>

            <div class="mt-8 rounded-xl bg-[#F8FAFC] dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] p-5">
                <h3 class="font-bold text-[#1E293B] dark:text-[#f1f5f9]">Cara menyambungkan tarif live (opsional)</h3>
                <ol class="mt-3 text-sm text-[#64748B] dark:text-[#cbd5e1] space-y-2 list-decimal list-inside">
                    <li>Daftar salah satu penyedia di atas (api.co.id, RajaOngkir Komerce, atau Biteship sandbox).</li>
                    <li>Simpan API key di <code class="text-xs bg-white dark:bg-[#23252b] px-1.5 py-0.5 rounded border border-[#E2E8F0] dark:border-[#404854] text-[#64748B] dark:text-[#cbd5e1]">.env</code>, contoh: <code class="text-xs bg-white dark:bg-[#23252b] px-1.5 py-0.5 rounded border border-[#E2E8F0] dark:border-[#404854] text-[#64748B] dark:text-[#cbd5e1]">ONGKIR_API_KEY=xxxx</code>.</li>
                    <li>Ganti isi <code class="text-xs bg-white dark:bg-[#23252b] px-1.5 py-0.5 rounded border border-[#E2E8F0] dark:border-[#404854] text-[#64748B] dark:text-[#cbd5e1]">ShippingEstimator::estimateAll()</code> agar memanggil API, lalu simpan respons ke cache 6-12 jam.</li>
                    <li>Nomor WhatsApp admin di semua tombol Chat Admin diambil dari pengaturan <code class="text-xs bg-white dark:bg-[#23252b] px-1.5 py-0.5 rounded border border-[#E2E8F0] dark:border-[#404854] text-[#64748B] dark:text-[#cbd5e1]">setting('whatsapp')</code>, jadi tidak perlu ubah kode.</li>
                </ol>
            </div>

            <p class="mt-4 text-xs text-[#64748B] dark:text-[#cbd5e1]">
                Data API &amp; tarif diperbarui terakhir: September 2026. Selalu cek ulang tarif resmi sebelum dipakai menagih pembeli.
            </p>
        </div>
    </section>
</div>
@endsection