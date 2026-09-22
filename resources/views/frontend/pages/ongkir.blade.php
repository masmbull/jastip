@extends('layouts.app')

@section('title', 'Cek Ongkir & Lacak Resi')

@php
    $metaDescription = 'Cek estimasi ongkir dari Jakarta ke seluruh Indonesia untuk 15 ekspedisi (JNE, J&T, SiCepat, Ninja, AnterAja, TIKI, POS, Wahana, Lion Parcel, ID Express, SAP, J&T Cargo, Paxel, GoSend, GrabExpress) plus cara lacak resi.';
@endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="{ tab: 'kalkulator' }">

    {{-- Header --}}
    <header class="mb-10 animate-fade-up">
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-100 text-orange-700 text-xs font-bold uppercase tracking-wide">
            🚚 15 Ekspedisi Nasional
        </span>
        <h1 class="mt-4 text-3xl md:text-4xl font-bold text-[#1E293B]">Cek Ongkir &amp; Lacak Resi</h1>
        <p class="mt-3 text-[#475569] max-w-3xl leading-relaxed">
            Hitung estimasi biaya kirim dari <strong>{{ $origin['label'] }}</strong> ke kota tujuanmu,
            bandingkan {{ count($couriers) }} ekspedisi, lalu lacak resi langsung dari halaman ini.
            Estimasi memakai tarif referensi pasar — angka final mengikuti tarif kurir saat order.
        </p>
    </header>

    {{-- Tab navigation --}}
    <nav class="flex flex-wrap gap-2 mb-8 border-b border-[#E2E8F0]" role="tablist">
        @foreach([
            'kalkulator' => 'Kalkulator Ongkir',
            'ekspedisi' => 'Daftar Ekspedisi',
            'resi' => 'Lacak Resi',
            'api' => 'API Gratis',
        ] as $key => $label)
            <button type="button" @click="tab = '{{ $key }}'"
                    :class="tab === '{{ $key }}' ? 'border-orange-500 text-orange-600' : 'border-transparent text-[#64748B] hover:text-orange-500'"
                    class="px-4 py-3 text-sm font-semibold border-b-2 -mb-px transition-colors">{{ $label }}</button>
        @endforeach
    </nav>

    {{-- ================= KALKULATOR ================= --}}
    <section x-show="tab === 'kalkulator'" x-cloak>
        <form method="GET" action="{{ route('shipping.index') }}"
              class="bg-white rounded-2xl border border-[#E2E8F0] p-6 sm:p-8 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label for="city" class="block text-xs font-semibold uppercase tracking-wide text-[#64748B] mb-2">Kota Tujuan</label>
                    <select name="city" id="city"
                            class="w-full rounded-xl border-[#E2E8F0] text-sm text-[#1E293B] focus:border-orange-500 focus:ring-orange-500">
                        @foreach($citiesByZone as $zone => $zoneCities)
                            <optgroup label="Zona {{ $zone }} — {{ $zoneLabels[$zone] ?? '' }}">
                                @foreach($zoneCities as $zoneCity)
                                    <option value="{{ $zoneCity }}" @selected($zoneCity === $city)>{{ $zoneCity }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="weight" class="block text-xs font-semibold uppercase tracking-wide text-[#64748B] mb-2">Berat Paket (kg)</label>
                    <input type="number" name="weight" id="weight" min="0.1" max="100" step="0.1"
                           value="{{ $weight }}"
                           class="w-full rounded-xl border-[#E2E8F0] text-sm text-[#1E293B] focus:border-orange-500 focus:ring-orange-500">
                    <p class="mt-1 text-xs text-[#94A3B8]">Pembulatan kurir: per 0,5 kg (kargo per 1 kg).</p>
                </div>

                <div>
                    <label for="courier" class="block text-xs font-semibold uppercase tracking-wide text-[#64748B] mb-2">Ekspedisi (opsional)</label>
                    <select name="courier" id="courier"
                            class="w-full rounded-xl border-[#E2E8F0] text-sm text-[#1E293B] focus:border-orange-500 focus:ring-orange-500">
                        <option value="">— Bandingkan semua —</option>
                        @foreach($couriersByType as $type => $group)
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
                        class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-full text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300">
                    Hitung Ongkir
                </button>
                <a href="{{ route('shipping.index') }}"
                   class="px-6 py-2.5 bg-white border border-[#E2E8F0] text-[#64748B] hover:border-orange-300 hover:text-orange-600 font-semibold rounded-full text-sm transition-colors">
                    Reset
                </a>
            </div>
        </form>

        {{-- Sorotan: termurah & tercepat --}}
        @if($highlights['cheapest'])
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                <div class="bg-white rounded-2xl border border-orange-200 p-5 flex items-start gap-4">
                    <div class="w-11 h-11 rounded-xl bg-orange-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-[#94A3B8]">Termurah</p>
                        <p class="font-bold text-[#1E293B]">{{ $highlights['cheapest']['courier']['name'] }}</p>
                        <p class="text-orange-600 font-bold text-xl">{{ $highlights['cheapest']['price_formatted'] }}</p>
                        <p class="text-xs text-[#64748B] mt-1">Estimasi {{ $highlights['cheapest']['etd'] }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-[#E2E8F0] p-5 flex items-start gap-4">
                    <div class="w-11 h-11 rounded-xl bg-orange-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-[#94A3B8]">Tercepat</p>
                        <p class="font-bold text-[#1E293B]">{{ $highlights['fastest']['courier']['name'] }}</p>
                        <p class="text-orange-600 font-bold text-xl">{{ $highlights['fastest']['price_formatted'] }}</p>
                        <p class="text-xs text-[#64748B] mt-1">Estimasi {{ $highlights['fastest']['etd'] }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Tabel hasil --}}
        <div class="bg-white rounded-2xl border border-[#E2E8F0] overflow-hidden">
            <div class="px-6 py-4 border-b border-[#E2E8F0] flex flex-wrap items-center justify-between gap-3">
                <h2 class="font-bold text-[#1E293B]">
                    Estimasi ke {{ $city }}
                    <span class="font-normal text-[#64748B] text-sm">· {{ $results[0]['province'] ?? '' }} · {{ $weight }} kg</span>
                </h2>
                @if($results[0]['ok'] ?? false)
                    <span class="text-xs px-3 py-1 rounded-full bg-orange-100 text-orange-700 font-semibold">
                        {{ $results[0]['zone_label'] }}
                    </span>
                @endif
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-[#F1F5F9] text-[#64748B]">
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
                    <tbody class="divide-y divide-[#E2E8F0]">
                        @foreach($results as $row)
                            <tr class="{{ $row['ok'] ? 'hover:bg-orange-50/40' : 'bg-[#F8FAFC]' }} transition-colors">
                                <td class="px-6 py-4">
                                    <span class="font-semibold {{ $row['ok'] ? 'text-[#1E293B]' : 'text-[#94A3B8]' }}">{{ $row['courier']['name'] }}</span>
                                    <span class="block text-xs text-[#94A3B8]">{{ $typeLabel($row['courier']['type']) }}</span>
                                </td>
                                <td class="px-4 py-4 text-xs text-[#64748B]">{{ $row['courier']['services'] }}</td>
                                <td class="px-4 py-4 text-right text-[#64748B]">
                                    {{ $row['ok'] ? $row['per_kg_formatted'] : '—' }}
                                </td>
                                <td class="px-4 py-4 text-right">
                                    @if($row['ok'])
                                        <span class="font-bold text-orange-600">{{ $row['price_formatted'] }}</span>
                                        <span class="block text-xs text-[#94A3B8]">{{ $row['billable_weight'] }} kg terhitung</span>
                                    @else
                                        <span class="text-xs text-[#94A3B8]">Tidak tersedia</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-xs text-[#64748B]">
                                    {{ $row['ok'] ? $row['etd'] : ($row['message'] ?? '—') }}
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @if($row['tracking'])
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"></path></svg>
                                            Bisa
                                        </span>
                                    @else
                                        <span class="text-xs px-2.5 py-1 rounded-full bg-slate-100 text-slate-600">Tidak</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($row['ok'])
                                        <a href="{{ route('shipping.index', ['city' => $city, 'weight' => $weight, 'courier' => $row['courier']['code']]) }}"
                                           class="text-xs font-semibold text-orange-600 hover:text-orange-700">Detail</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <p class="px-6 py-4 text-xs text-[#94A3B8] border-t border-[#E2E8F0]">
                * Estimasi berbasis tarif referensi pasar dari {{ $origin['city'] }}, dibulatkan ke kelipatan Rp 500.
                Tarif asli tiap kurir bisa berbeda per kecamatan — cek halaman resmi kurir atau hubungi admin.
            </p>
        </div>

        {{-- Detail ekspedisi terpilih --}}
        @if($selectedResult && ($selectedResult['ok'] ?? false))
            <div class="mt-8 bg-white rounded-2xl border border-orange-200 p-6 sm:p-8">
                <h2 class="font-bold text-[#1E293B] text-lg">
                    {{ $selectedResult['courier']['name'] }} → {{ $selectedResult['city'] }}
                </h2>
                <dl class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                    <div>
                        <dt class="text-xs uppercase text-[#94A3B8]">Estimasi biaya</dt>
                        <dd class="font-bold text-orange-600 text-lg">{{ $selectedResult['price_formatted'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase text-[#94A3B8]">Berat dihitung</dt>
                        <dd class="font-semibold text-[#1E293B]">{{ $selectedResult['billable_weight'] }} kg</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase text-[#94A3B8]">Zona</dt>
                        <dd class="font-semibold text-[#1E293B]">{{ $selectedResult['zone_label'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase text-[#94A3B8]">ETD</dt>
                        <dd class="font-semibold text-[#1E293B]">{{ $selectedResult['etd'] }}</dd>
                    </div>
                </dl>
                <p class="mt-4 text-sm text-[#475569]">{{ $selectedResult['courier']['notes'] }}</p>

                <div class="mt-5">
                    <p class="text-xs uppercase tracking-wide text-[#94A3B8] mb-2">Link lacak resi &amp; halaman resmi</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($selectedResult['tracking_links'] as $link)
                            <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer"
                               class="text-xs font-semibold px-4 py-2 rounded-full border border-[#E2E8F0] text-[#64748B] hover:border-orange-300 hover:text-orange-600 transition-colors">
                                {{ $link['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <a href="{{ whatsapp_url(setting('whatsapp')) }}" target="_blank" rel="noopener noreferrer"
                   class="inline-block mt-6 px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-full text-sm transition-colors">
                    Tanya Admin soal Ongkir Ini
                </a>
            </div>
        @endif
    </section>

    {{-- ================= DAFTAR EKSPEDISI ================= --}}
    <section x-show="tab === 'ekspedisi'" x-cloak>
        <div class="bg-white rounded-2xl border border-[#E2E8F0] p-6 sm:p-8">
            <h2 class="text-xl font-bold text-[#1E293B]">Daftar {{ count($couriers) }} Ekspedisi Indonesia</h2>
            <p class="mt-2 text-sm text-[#475569] max-w-3xl">
                Tarif referensi per kg untuk paket kecil (± 1 kg) dari {{ $origin['city'] }}. Semua ekspedisi di bawah
                ini mendukung lacak resi. Tarif final mengikuti berat &amp; kecamatan tujuan saat order.
            </p>

            @foreach($couriersByType as $type => $group)
                <div class="mt-8">
                    <div class="flex items-center gap-3 mb-3">
                        <h3 class="font-bold text-[#1E293B]">{{ $typeLabel($type) }}</h3>
                        <span class="text-xs px-2.5 py-1 rounded-full bg-orange-100 text-orange-700 font-semibold">{{ count($group) }} ekspedisi</span>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-[#E2E8F0]">
                        <table class="w-full text-sm">
                            <thead class="bg-[#F1F5F9] text-[#64748B]">
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
                            <tbody class="divide-y divide-[#E2E8F0]">
                                @foreach($group as $courier)
                                    <tr class="hover:bg-orange-50/40 transition-colors">
                                        <td class="px-4 py-4 font-semibold text-[#1E293B] whitespace-nowrap">{{ $courier['name'] }}</td>
                                        <td class="px-4 py-4 text-xs text-[#64748B]">{{ $courier['services'] }}</td>
                                        <td class="px-4 py-4 text-right whitespace-nowrap">
                                            <span class="font-bold text-orange-600">{{ format_price($courier['per_kg']) }}/kg</span>
                                            @if(($courier['min_charge'] ?? 0) > ($courier['per_kg'] ?? 0))
                                                <span class="block text-xs text-[#94A3B8]">min. {{ format_price($courier['min_charge']) }}</span>
                                            @endif
                                            @if(!empty($courier['min_weight_kg']))
                                                <span class="block text-xs text-[#94A3B8]">min. {{ $courier['min_weight_kg'] }} kg</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-xs text-[#64748B] whitespace-nowrap">{{ $courier['etd'] }}</td>
                                        <td class="px-4 py-4 text-xs text-[#64748B]">{{ $courier['coverage'] }}</td>
                                        <td class="px-4 py-4 text-center">
                                            @if($courier['tracking'])
                                                <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"></path></svg>
                                                    Bisa
                                                </span>
                                            @else
                                                <span class="text-xs px-2.5 py-1 rounded-full bg-slate-100 text-slate-600">Tidak</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-xs text-[#64748B] max-w-xs">{{ $courier['notes'] }}</td>
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
            $trackingCouriers = collect($couriers)->map(fn ($c) => [
                'code' => $c['code'],
                'name' => $c['name'],
                'page' => $c['tracking_page'],
                'query' => $c['tracking_query'],
            ])->values()->all();
        @endphp

        <div class="bg-white rounded-2xl border border-[#E2E8F0] p-6 sm:p-8"
             x-data="{
                awb: '',
                code: {{ Js::from($trackingCouriers[0]['code'] ?? 'jne') }},
                couriers: {{ Js::from($trackingCouriers) }},
                agg: {{ Js::from($aggregator['url']) }},
                aggName: {{ Js::from($aggregator['name']) }},
                get selected() { return this.couriers.find(c => c.code === this.code) || this.couriers[0] },
                get ready() { return this.awb.trim().length >= 6 },
                get aggregatorUrl() { return this.agg.replace('%s', encodeURIComponent(this.awb.trim())) },
                get directUrl() {
                    const t = (this.selected && this.selected.query) ? this.selected.query : this.agg;
                    return t.replace('%s', encodeURIComponent(this.awb.trim()));
                }
             }">
            <h2 class="text-xl font-bold text-[#1E293B]">Lacak Resi Pengiriman</h2>
            <p class="mt-2 text-sm text-[#475569] max-w-3xl">
                Masukkan nomor resi, pilih ekspedisi, lalu lacak langsung ke halaman resmi kurir atau lewat aggregator
                {{ $aggregator['name'] }}. Resi kamu juga bisa ditanyakan ke admin lewat WhatsApp.
            </p>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="resi_courier" class="block text-xs font-semibold uppercase tracking-wide text-[#64748B] mb-2">Ekspedisi</label>
                    <select id="resi_courier" x-model="code"
                            class="w-full rounded-xl border-[#E2E8F0] text-sm text-[#1E293B] focus:border-orange-500 focus:ring-orange-500">
                        <template x-for="c in couriers" :key="c.code">
                            <option :value="c.code" x-text="c.name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label for="resi_awb" class="block text-xs font-semibold uppercase tracking-wide text-[#64748B] mb-2">Nomor Resi</label>
                    <input type="text" id="resi_awb" x-model="awb" autocomplete="off"
                           placeholder="Contoh: JNE1234567890"
                           class="w-full rounded-xl border-[#E2E8F0] text-sm text-[#1E293B] focus:border-orange-500 focus:ring-orange-500">
                    <p class="mt-1 text-xs text-[#94A3B8]">Minimal 6 karakter. Nomor resi biasanya ada di nota/chat admin.</p>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                <a :href="ready ? directUrl : '#'" target="_blank" rel="noopener noreferrer"
                   :class="ready ? 'bg-orange-500 hover:bg-orange-600 text-white' : 'bg-[#F1F5F9] text-[#94A3B8] cursor-not-allowed'"
                   class="px-5 py-2.5 font-semibold rounded-full text-sm transition-colors">
                    Lacak Sekarang
                </a>
                <a :href="ready ? aggregatorUrl : '#'" target="_blank" rel="noopener noreferrer"
                   :class="ready ? 'border-orange-300 text-orange-600 hover:bg-orange-50' : 'border-[#E2E8F0] text-[#94A3B8] cursor-not-allowed'"
                   class="px-5 py-2.5 bg-white border font-semibold rounded-full text-sm transition-colors">
                    Cek via <span x-text="aggName"></span>
                </a>
                <a :href="selected ? selected.page : '#'" target="_blank" rel="noopener noreferrer"
                   class="px-5 py-2.5 bg-white border border-[#E2E8F0] text-[#64748B] hover:border-orange-300 hover:text-orange-600 font-semibold rounded-full text-sm transition-colors">
                    Halaman Resmi <span x-text="selected ? selected.name : ''"></span>
                </a>
            </div>
        </div>
    </section>

    {{-- ================= API GRATIS ================= --}}
    <section x-show="tab === 'api'" x-cloak>
        <div class="bg-white rounded-2xl border border-[#E2E8F0] p-6 sm:p-8">
            <h2 class="text-xl font-bold text-[#1E293B]">API Gratis untuk Ongkir &amp; Lacak Resi</h2>
            <p class="mt-2 text-sm text-[#475569] max-w-3xl">
                Halaman ini sudah jalan tanpa API — estimasi memakai tarif referensi pasar di
                <code class="text-xs bg-[#F1F5F9] px-1.5 py-0.5 rounded text-[#64748B]">config/ekspedisi.php</code>.
                Kalau butuh tarif live sampai level kecamatan atau lacak resi otomatis di dalam website,
                berikut opsi gratis/murah yang paling praktis dipakai bareng Laravel.
            </p>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                @foreach($freeApis as $api)
                    <article class="rounded-xl border border-[#E2E8F0] p-5 hover:border-orange-300 transition-colors">
                        <div class="flex items-start justify-between gap-3">
                            <a href="{{ $api['url'] }}" target="_blank" rel="noopener noreferrer"
                               class="font-bold text-[#1E293B] hover:text-orange-600 transition-colors">
                                {{ $api['name'] }}
                            </a>
                            <span class="shrink-0 text-xs px-2.5 py-1 rounded-full bg-orange-100 text-orange-700 font-semibold">{{ $api['kind'] }}</span>
                        </div>

                        <dl class="mt-4 space-y-2 text-sm">
                            <div>
                                <dt class="text-xs uppercase tracking-wide text-[#94A3B8]">Gratis?</dt>
                                <dd class="text-emerald-700 font-medium">{{ $api['free'] }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs uppercase tracking-wide text-[#94A3B8]">Cakupan</dt>
                                <dd class="text-[#475569]">{{ $api['covers'] }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs uppercase tracking-wide text-[#94A3B8]">Catatan</dt>
                                <dd class="text-[#64748B]">{{ $api['notes'] }}</dd>
                            </div>
                        </dl>

                        <a href="{{ $api['url'] }}" target="_blank" rel="noopener noreferrer"
                           class="inline-block mt-4 text-xs font-semibold text-orange-600 hover:text-orange-700">
                            Buka dokumentasi →
                        </a>
                    </article>
                @endforeach
            </div>

            <div class="mt-8 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] p-5">
                <h3 class="font-bold text-[#1E293B]">Cara menyambungkan tarif live (opsional)</h3>
                <ol class="mt-3 text-sm text-[#475569] space-y-2 list-decimal list-inside">
                    <li>Daftar salah satu penyedia di atas (api.co.id, RajaOngkir Komerce, atau Biteship sandbox).</li>
                    <li>Simpan API key di <code class="text-xs bg-white px-1.5 py-0.5 rounded border border-[#E2E8F0] text-[#64748B]">.env</code> — contoh: <code class="text-xs bg-white px-1.5 py-0.5 rounded border border-[#E2E8F0] text-[#64748B]">ONGKIR_API_KEY=xxxx</code>.</li>
                    <li>Ganti isi <code class="text-xs bg-white px-1.5 py-0.5 rounded border border-[#E2E8F0] text-[#64748B]">ShippingEstimator::estimateAll()</code> agar memanggil API, lalu simpan respons ke cache 6–12 jam.</li>
                    <li>Nomor WhatsApp admin di semua tombol Chat Admin diambil dari pengaturan <code class="text-xs bg-white px-1.5 py-0.5 rounded border border-[#E2E8F0] text-[#64748B]">setting('whatsapp')</code>, jadi tidak perlu ubah kode.</li>
                </ol>
            </div>

            <p class="mt-4 text-xs text-[#94A3B8]">
                Data API &amp; tarif diperbarui terakhir: September 2026. Selalu cek ulang tarif resmi sebelum dipakai menagih pembeli.
            </p>
        </div>
    </section>
</div>
@endsection

