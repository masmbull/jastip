{{-- $cancel = 'modal' -> tombol Batal menutup modal; else link ke daftar pesanan. --}}
@php($cancel = $cancel ?? null)
    <form method="POST" action="{{ route('admin.orders.store') }}" class="space-y-5" @submit="saving = true">
        @csrf

        <div class="nb-card !p-0 overflow-hidden">

            <div class="p-5 border-b-2 border-[#141414] dark:border-[#FAF6EF] bg-[#FAF6EF] dark:bg-[#262626] flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="h-11 w-11 bg-[#FFC800] border-2 border-[#141414] dark:border-[#FAF6EF] rounded-[10px] flex items-center justify-center text-[#141414] font-black text-xl shrink-0">
                        {{ strtoupper(substr(setting('brand_name', 'NITIP DI END'), 0, 1)) }}
                    </div>
                    <div class="text-sm">
                        <div class="font-black text-[#141414] dark:text-[#FAF6EF] leading-tight">{{ setting('brand_name', 'NITIP DI END') }}</div>
                        @if(setting('brand_owner'))<div class="text-xs font-bold text-[#475569] dark:text-[#D6D3D1]">{{ setting('brand_owner') }}</div>@endif
                        @if(setting('whatsapp'))<div class="text-xs font-medium text-[#475569] num">WA: {{ setting('whatsapp') }}</div>@endif
                    </div>
                </div>
                <div class="sm:text-right">
                    <div class="inline-block bg-[#141414] dark:bg-[#FAF6EF] text-white dark:text-[#141414] font-black tracking-tight text-xl px-3 py-1.5 rounded-[8px]">INVOICE</div>
                    <div class="text-xs font-medium text-[#475569] dark:text-[#D6D3D1] mt-1.5">Nomor dan tanggal dibuat otomatis</div>
                </div>
            </div>

            <div class="p-5 space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="space-y-3.5">
                        <h2 class="text-[11px] font-black text-[#475569] dark:text-[#D6D3D1] uppercase tracking-[0.12em]">Tagihan untuk</h2>
                        <div>
                            <label class="block text-sm font-bold mb-1" for="mi-name">Nama Pelanggan *</label>
                            <input id="mi-name" type="text" name="customer_name" required value="{{ old('customer_name') }}"
                                   placeholder="Nama pelanggan, diisi manual"
                                   class="nb-input w-full px-3 py-2 text-sm @error('customer_name') border-[#991B1B]! @enderror">
                            @error('customer_name')<p class="text-xs font-bold text-[#991B1B] mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-bold mb-1" for="mi-wa">No. WhatsApp *</label>
                                <input id="mi-wa" type="text" name="customer_whatsapp" required value="{{ old('customer_whatsapp') }}"
                                       placeholder="08..."
                                       class="nb-input w-full px-3 py-2 text-sm num @error('customer_whatsapp') border-[#991B1B]! @enderror">
                                @error('customer_whatsapp')<p class="text-xs font-bold text-[#991B1B] mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-bold mb-1" for="mi-email">Email</label>
                                <input id="mi-email" type="email" name="customer_email" value="{{ old('customer_email') }}"
                                       placeholder="Opsional"
                                       class="nb-input w-full px-3 py-2 text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-1" for="mi-address">Alamat *</label>
                            <textarea id="mi-address" name="customer_address" rows="2" required
                                      placeholder="Alamat lengkap pelanggan"
                                      class="nb-input w-full px-3 py-2 text-sm @error('customer_address') border-[#991B1B]! @enderror">{{ old('customer_address') }}</textarea>
                            <p class="text-xs font-medium text-[#475569] dark:text-[#D6D3D1] mt-1">Wajib diisi, dipakai untuk hitung ongkir.</p>
                            @error('customer_address')<p class="text-xs font-bold text-[#991B1B] mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="space-y-3.5">
                        <h2 class="text-[11px] font-black text-[#475569] dark:text-[#D6D3D1] uppercase tracking-[0.12em]">Pengiriman dan Biaya</h2>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-sm font-bold mb-1" for="mi-city">Kota Tujuan</label>
                                <select id="mi-city" name="shipping_city" x-model="city"
                                        class="nb-input w-full px-3 py-2 text-sm font-bold">
                                    <option value="">Pilih kota</option>
                                    <template x-for="c in cities" :key="c">
                                        <option :value="c" x-text="c" :selected="c === {{ Js::from(old('shipping_city', '')) }}"></option>
                                    </template>
                                </select>
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-sm font-bold mb-1" for="mi-weight">Berat (kg)</label>
                                <div class="flex gap-2">
                                    <input id="mi-weight" type="number" min="0.1" max="100" step="0.1" x-model.number="weight"
                                           class="nb-input flex-1 min-w-0 px-3 py-2 text-sm num">
                                    <button type="button" @click="fetchQuotes()" :disabled="quoting || !city"
                                            class="nb-btn px-3 py-2 text-sm bg-[#0E7490] text-white shrink-0">
                                        <span x-text="quoting ? 'Mencari...' : 'Cek Ongkir'">Cek Ongkir</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div x-show="quoteError" x-cloak class="text-xs font-bold text-[#991B1B] bg-[#FEE2E2] border-2 border-[#141414] rounded-[8px] px-3 py-2" x-text="quoteError"></div>

                        <div x-show="quotes.length" x-cloak>
                            <p class="text-xs font-black uppercase tracking-wider text-[#475569] dark:text-[#D6D3D1] mb-1.5">Pilih ekspedisi</p>
                            <div class="border-2 border-[#141414] dark:border-[#FAF6EF] rounded-[10px] overflow-hidden divide-y divide-[#141414]/15 dark:divide-[#FAF6EF]/15 max-h-52 overflow-y-auto" role="radiogroup" aria-label="Pilihan ekspedisi">
                                <template x-for="q in quotes" :key="q.code">
                                    <button type="button" @click="pickQuote(q)" role="radio" :aria-checked="selectedCourier === q.code"
                                            :class="selectedCourier === q.code ? 'bg-[#FFF7D6] dark:bg-[#333]' : 'bg-white dark:bg-[#1E1E1E]'"
                                            class="w-full flex items-center justify-between gap-3 px-3 py-2.5 text-left min-h-[52px]">
                                        <span class="min-w-0">
                                            <span class="block text-sm font-black truncate" x-text="q.name"></span>
                                            <span class="block text-[11px] font-bold text-[#475569]" x-text="(q.type_label || '') + (q.etd ? ' · ' + q.etd : '')"></span>
                                        </span>
                                        <span class="shrink-0 text-sm font-black num" x-text="q.price_formatted || q.message"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-bold mb-1" for="mi-method">Ekspedisi</label>
                                <input id="mi-method" type="text" name="shipping_method" x-model="shipMethod" value="{{ old('shipping_method') }}" placeholder="mis. JNE Reguler"
                                       class="nb-input w-full px-3 py-2 text-sm">
                                <p class="text-[11px] font-medium text-[#475569] mt-1">Terisi otomatis, bisa diubah manual.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-bold mb-1" for="mi-cost">Ongkir</label>
                                <input id="mi-cost" type="number" min="0" name="shipping_cost" x-model.number="shipping" value="{{ old('shipping_cost', 0) }}"
                                       class="nb-input w-full px-3 py-2 text-sm num">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-bold mb-1" for="mi-fee">Fee Jastip</label>
                                <input id="mi-fee" type="number" min="0" name="fee" x-model.number="feeAmt" value="{{ old('fee', 0) }}"
                                       class="nb-input w-full px-3 py-2 text-sm num">
                            </div>
                            <div>
                                <label class="block text-sm font-bold mb-1" for="mi-dp">Down Payment (DP)</label>
                                <input id="mi-dp" type="number" min="0" name="down_payment" x-model.number="dpAmt" :disabled="paidNow" value="{{ old('down_payment', 0) }}"
                                       placeholder="0 = tanpa DP"
                                       class="nb-input w-full px-3 py-2 text-sm num disabled:opacity-50">
                                @error('down_payment')<p class="text-xs font-bold text-[#991B1B] mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-1" for="mi-notes">Catatan untuk pelanggan</label>
                            <textarea id="mi-notes" name="customer_notes" rows="2"
                                      class="nb-input w-full px-3 py-2 text-sm">{{ old('customer_notes') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-1" for="mi-admin">Catatan internal (admin)</label>
                            <textarea id="mi-admin" name="admin_notes" rows="2"
                                      class="nb-input w-full px-3 py-2 text-sm">{{ old('admin_notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-[11px] font-black text-[#475569] dark:text-[#D6D3D1] uppercase tracking-[0.12em]">Item</h2>
                        <button type="button" @click="addItem()"
                                class="nb-btn px-3 py-2 text-sm bg-[#0E7490] text-white">
                            <x-icon name="plus" class="w-4 h-4" /> Tambah Item
                        </button>
                    </div>

                    <div class="hidden sm:grid grid-cols-12 gap-2 px-1 text-[11px] font-black text-[#475569] dark:text-[#D6D3D1] uppercase tracking-wider">
                        <div class="col-span-4">Produk</div>
                        <div class="col-span-1">Satuan</div>
                        <div class="col-span-2">Status</div>
                        <div class="col-span-1 text-center">Qty</div>
                        <div class="col-span-2 text-right">Harga</div>
                        <div class="col-span-2 text-right">Subtotal</div>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(item, idx) in items" :key="item.key">
                            <div class="grid grid-cols-12 gap-2 items-start border-2 border-[#141414]/15 dark:border-[#FAF6EF]/15 rounded-[10px] p-2.5">
                                <div class="col-span-12 sm:col-span-4 relative">
                                    <span class="sm:hidden text-[11px] font-black uppercase text-[#475569]">Produk *</span>
                                    <input type="text" :name="`items[${idx}][product_name]`" x-model="item.name"
                                           @input.debounce.400ms="searchProduct(idx)" @click.away="item.open = false"
                                           @focus="item.suggestions.length && (item.open = true)"
                                           placeholder="Nama barang *" autocomplete="off" required
                                           class="nb-input w-full px-3 py-2 text-sm">
                                    <div x-show="item.open && (item.loading || item.suggestions.length)" x-cloak
                                         class="absolute z-20 mt-1 w-full bg-white dark:bg-[#1E1E1E] border-2 border-[#141414] dark:border-[#FAF6EF] rounded-[8px] overflow-hidden max-h-48 overflow-y-auto" style="box-shadow: 3px 3px 0 #141414">
                                        <div x-show="item.loading" class="flex items-center gap-2 px-3 py-2 text-sm font-bold text-[#475569]">
                                            <x-spinner /> Mencari produk...
                                        </div>
                                        <template x-for="s in item.suggestions" :key="s.id">
                                            <button type="button" @click="pickProduct(idx, s)"
                                                    class="w-full text-left px-3 py-2.5 text-sm font-bold hover:bg-[#FFF7D6] min-h-[44px]">
                                                <span x-text="s.name"></span>
                                                <span class="text-[#475569] num" x-text="' · ' + formatRp(s.price)"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                                <div class="col-span-4 sm:col-span-1">
                                    <span class="sm:hidden text-[11px] font-black uppercase text-[#475569]">Satuan</span>
                                    <input type="text" :name="`items[${idx}][unit]`" x-model="item.unit" placeholder="pcs"
                                           class="nb-input w-full px-3 py-2 text-sm">
                                </div>
                                <div class="col-span-8 sm:col-span-2">
                                    <span class="sm:hidden text-[11px] font-black uppercase text-[#475569]">Status</span>
                                    <select :name="`items[${idx}][status]`" x-model="item.status"
                                            class="nb-input w-full px-2 py-2 text-sm font-bold">
                                        <option value="ready">Ready</option>
                                        <option value="po">PO</option>
                                    </select>
                                </div>
                                <div class="col-span-4 sm:col-span-1">
                                    <span class="sm:hidden text-[11px] font-black uppercase text-[#475569]">Qty *</span>
                                    <input type="number" min="1" :name="`items[${idx}][quantity]`" x-model.number="item.qty" required
                                           class="nb-input w-full px-2 py-2 text-sm text-center num">
                                </div>
                                <div class="col-span-8 sm:col-span-2">
                                    <span class="sm:hidden text-[11px] font-black uppercase text-[#475569]">Harga *</span>
                                    <input type="number" min="0" :name="`items[${idx}][product_price]`" x-model.number="item.price" required
                                           class="nb-input w-full px-3 py-2 text-sm text-right num">
                                </div>
                                <div class="col-span-10 sm:col-span-1 text-sm font-black text-right py-2.5 num" x-text="formatRp(lineTotal(item))"></div>
                                <div class="col-span-2 sm:col-span-1 text-right">
                                    <button type="button" @click="removeItem(idx)" x-show="items.length > 1"
                                            class="inline-flex items-center justify-center w-11 h-11 text-[#991B1B]" aria-label="Hapus item" title="Hapus item">
                                        <x-icon name="trash" class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex justify-end pt-1">
                    <div class="w-full sm:w-80 text-sm border-2 border-[#141414] dark:border-[#FAF6EF] rounded-[10px] overflow-hidden bg-white dark:bg-[#1E1E1E]">
                        <div class="p-4 space-y-1.5">
                            <div class="flex justify-between"><span class="font-bold text-[#475569]">Subtotal</span><span class="font-black num" x-text="formatRp(subtotal())"></span></div>
                            <div class="flex justify-between"><span class="font-bold text-[#475569]">Ongkir</span><span class="font-black num" x-text="formatRp(shipping)"></span></div>
                            <div class="flex justify-between"><span class="font-bold text-[#475569]">Fee Jastip</span><span class="font-black num" x-text="formatRp(feeAmt)"></span></div>
                            <div class="flex justify-between" x-show="dpAmt > 0 && !paidNow" x-cloak><span class="font-bold text-[#475569]">DP Masuk</span><span class="font-black text-[#166534] num" x-text="'-' + formatRp(paidNow ? 0 : dpAmt)"></span></div>
                        </div>
                        <div class="nb-money-strip px-4 py-3 space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-black uppercase tracking-wide">Total</span>
                                <span class="text-lg font-black num" x-text="formatRp(total())"></span>
                            </div>
                            <div class="flex items-center justify-between" x-show="dpAmt > 0 && !paidNow" x-cloak>
                                <span class="text-xs font-black uppercase tracking-wide">Sisa Bayar</span>
                                <span class="text-base font-black num" x-text="formatRp(remaining())"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3">
            <label class="flex items-center gap-2 cursor-pointer min-h-[44px]">
                <input type="checkbox" name="mark_paid" value="1" x-model="paidNow" class="w-5 h-5 accent-[#0E7490]">
                <span class="text-sm font-bold">Tandai sudah lunas</span>
            </label>
            <div class="flex items-center justify-end gap-2">
                @if($cancel === 'modal')
                    <button type="button" @click="$dispatch('close-manual-invoice')" class="nb-btn px-4 py-2 text-sm bg-white dark:bg-[#1E1E1E]">Batal</button>
                @else
                    <a href="{{ route('admin.invoices.index') }}" class="nb-btn px-4 py-2 text-sm bg-white dark:bg-[#1E1E1E]">Batal</a>
                @endif
                <button type="submit" :disabled="saving"
                        class="nb-btn px-6 py-2.5 bg-[#0E7490] text-white">
                    <x-spinner x-show="saving" x-cloak />
                    <span x-text="saving ? 'Menyimpan...' : 'Simpan dan Buat Invoice'">Simpan dan Buat Invoice</span>
                </button>
            </div>
        </div>
    </form>
