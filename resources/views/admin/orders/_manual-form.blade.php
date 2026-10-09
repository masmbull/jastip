{{-- $cancel = 'modal' -> tombol Batal menutup modal; else link ke daftar pesanan. --}}
@php($cancel = $cancel ?? null)
    <form method="POST" action="{{ route('admin.orders.store') }}" class="space-y-6">
        @csrf

        {{-- Lembar invoice --}}
        <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-sm border border-[#E2E8F0] dark:border-[#404854] overflow-hidden">

            {{-- Kop --}}
            <div class="p-6 border-b border-[#E2E8F0] dark:border-[#404854] bg-[#F8FAFC] dark:bg-[#2e323b] flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="h-11 w-11 rounded-full bg-orange-500 flex items-center justify-center text-white font-bold text-xl shrink-0">
                        {{ strtoupper(substr(setting('brand_name', 'NITIP DI END'), 0, 1)) }}
                    </div>
                    <div class="text-sm">
                        <div class="font-bold text-[#1E293B] dark:text-[#f1f5f9] leading-tight">{{ setting('brand_name', 'NITIP DI END') }}</div>
                        @if(setting('brand_owner'))<div class="text-xs text-[#64748B] dark:text-[#cbd5e1]">{{ setting('brand_owner') }}</div>@endif
                        @if(setting('whatsapp'))<div class="text-xs text-[#94A3B8]">WA: {{ setting('whatsapp') }}</div>@endif
                    </div>
                </div>
                <div class="sm:text-right">
                    <div class="text-xl font-bold tracking-wide text-orange-600 dark:text-orange-400">INVOICE</div>
                    <div class="text-xs text-[#94A3B8] mt-1">Nomor &amp; tanggal dibuat otomatis</div>
                </div>
            </div>

            <div class="p-6 space-y-6">
                {{-- Pelanggan + pengiriman --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <h2 class="text-xs font-semibold text-[#64748B] dark:text-[#cbd5e1] uppercase tracking-wide">Tagihan untuk</h2>
                        <div class="relative">
                            <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-1">Nama Pelanggan *</label>
                            <input type="text" name="customer_name" x-model="customerName" @input.debounce.400ms="searchCustomer()"
                                   @focus="hits.length && (openCustomer = true)" @click.away="openCustomer = false"
                                   autocomplete="off" required value="{{ old('customer_name') }}"
                                   class="w-full px-3 py-2 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#23252b] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300 @error('customer_name') border-red-500 @enderror">
                            <div x-show="openCustomer && hits.length" x-cloak
                                 class="absolute z-20 mt-1 w-full bg-white dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                <template x-for="h in hits" :key="h.customer_whatsapp">
                                    <button type="button" @click="pickCustomer(h)"
                                            class="w-full text-left px-3 py-2 text-sm hover:bg-orange-50 dark:hover:bg-orange-500/10">
                                        <span class="font-medium" x-text="h.customer_name"></span>
                                        <span class="text-[#94A3B8]" x-text="' · ' + h.customer_whatsapp"></span>
                                    </button>
                                </template>
                            </div>
                            <p class="text-xs text-[#94A3B8] mt-1">Ketik untuk memilih pelanggan lama, atau isi manual.</p>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-1">No. WhatsApp *</label>
                                <input type="text" name="customer_whatsapp" x-model="whatsapp" required value="{{ old('customer_whatsapp') }}"
                                       class="w-full px-3 py-2 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#23252b] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300 @error('customer_whatsapp') border-red-500 @enderror">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-1">Email</label>
                                <input type="email" name="customer_email" value="{{ old('customer_email') }}"
                                       class="w-full px-3 py-2 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#23252b] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-1">Alamat</label>
                            <textarea name="customer_address" x-model="address" rows="2"
                                      class="w-full px-3 py-2 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#23252b] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">{{ old('customer_address') }}</textarea>
                        </div>
                    </div>
<div class="space-y-3">
                        <h2 class="text-xs font-semibold text-[#64748B] dark:text-[#cbd5e1] uppercase tracking-wide">Pengiriman &amp; Biaya</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-1">Ekspedisi</label>
                                <input type="text" name="shipping_method" value="{{ old('shipping_method') }}" placeholder="mis. JNE Reguler"
                                       class="w-full px-3 py-2 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#23252b] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-1">Ongkir</label>
                                <input type="number" min="0" name="shipping_cost" x-model.number="shipping" value="{{ old('shipping_cost', 0) }}"
                                       class="w-full px-3 py-2 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#23252b] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-1">Biaya Layanan</label>
                            <input type="number" min="0" name="fee" x-model.number="feeAmt" value="{{ old('fee', 0) }}"
                                   class="w-full px-3 py-2 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#23252b] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-1">Catatan untuk pelanggan</label>
                            <textarea name="customer_notes" rows="2"
                                      class="w-full px-3 py-2 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#23252b] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">{{ old('customer_notes') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-1">Catatan internal (admin)</label>
                            <textarea name="admin_notes" rows="2"
                                      class="w-full px-3 py-2 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#23252b] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">{{ old('admin_notes') }}</textarea>
                        </div>
                    </div>
                </div>
{{-- Item --}}
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xs font-semibold text-[#64748B] dark:text-[#cbd5e1] uppercase tracking-wide">Item</h2>
                        <button type="button" @click="addItem()"
                                class="inline-flex items-center gap-1 px-3 py-1.5 text-sm bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium">
                            <x-icon name="plus" class="w-4 h-4" /> Tambah Item
                        </button>
                    </div>

                    {{-- Header baris (invoice table) --}}
                    <div class="hidden sm:grid grid-cols-12 gap-2 px-1 text-xs font-semibold text-[#64748B] dark:text-[#cbd5e1] uppercase tracking-wide">
                        <div class="col-span-5">Produk</div>
                        <div class="col-span-2">Satuan</div>
                        <div class="col-span-1 text-center">Qty</div>
                        <div class="col-span-2 text-right">Harga</div>
                        <div class="col-span-2 text-right">Subtotal</div>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(item, idx) in items" :key="item.key">
                            <div class="grid grid-cols-12 gap-2 items-start border-b border-[#E2E8F0] dark:border-[#404854] pb-3">
                                <div class="col-span-12 sm:col-span-5 relative">
                                    <input type="text" :name="`items[${idx}][product_name]`" x-model="item.name"
                                           @input.debounce.400ms="searchProduct(idx)" @click.away="item.open = false"
                                           @focus="item.suggestions.length && (item.open = true)"
                                           placeholder="Nama barang *" autocomplete="off"
                                           class="w-full px-3 py-2 text-sm border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#23252b] rounded-lg">
                                    <div x-show="item.open && item.suggestions.length" x-cloak
                                         class="absolute z-20 mt-1 w-full bg-white dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                        <template x-for="s in item.suggestions" :key="s.id">
                                            <button type="button" @click="pickProduct(idx, s)"
                                                    class="w-full text-left px-3 py-2 text-sm hover:bg-orange-50 dark:hover:bg-orange-500/10">
                                                <span x-text="s.name"></span>
                                                <span class="text-[#94A3B8]" x-text="' · ' + formatRp(s.price)"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                                <div class="col-span-4 sm:col-span-2">
                                    <input type="text" :name="`items[${idx}][unit]`" x-model="item.unit" placeholder="Satuan"
                                           class="w-full px-3 py-2 text-sm border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#23252b] rounded-lg">
                                </div>
                                <div class="col-span-4 sm:col-span-1">
                                    <input type="number" min="1" :name="`items[${idx}][quantity]`" x-model.number="item.qty"
                                           placeholder="Qty *"
                                           class="w-full px-3 py-2 text-sm text-center border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#23252b] rounded-lg">
                                </div>
                                <div class="col-span-4 sm:col-span-2">
                                    <input type="number" min="0" :name="`items[${idx}][product_price]`" x-model.number="item.price"
                                           placeholder="Harga *"
                                           class="w-full px-3 py-2 text-sm text-right border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#23252b] rounded-lg">
                                </div>
                                <div class="col-span-10 sm:col-span-1 text-sm font-medium text-right text-[#1E293B] dark:text-[#f1f5f9] py-2" x-text="formatRp(lineTotal(item))"></div>
                                <div class="col-span-2 sm:col-span-1 text-right">
                                    <button type="button" @click="removeItem(idx)" x-show="items.length > 1"
                                            class="text-red-500 hover:text-red-700 p-1" aria-label="Hapus item">
                                        <x-icon name="trash" class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
{{-- Ringkasan (rata kanan ala invoice) --}}
                <div class="flex justify-end pt-2">
                    <div class="w-full sm:w-72 text-sm space-y-1.5">
                        <div class="flex justify-between"><span class="text-[#64748B] dark:text-[#cbd5e1]">Subtotal</span><span class="text-[#1E293B] dark:text-[#f1f5f9]" x-text="formatRp(subtotal())"></span></div>
                        <div class="flex justify-between"><span class="text-[#64748B] dark:text-[#cbd5e1]">Ongkir</span><span class="text-[#1E293B] dark:text-[#f1f5f9]" x-text="formatRp(shipping)"></span></div>
                        <div class="flex justify-between"><span class="text-[#64748B] dark:text-[#cbd5e1]">Biaya Layanan</span><span class="text-[#1E293B] dark:text-[#f1f5f9]" x-text="formatRp(feeAmt)"></span></div>
                        <div class="flex justify-between pt-2 mt-1 border-t border-[#E2E8F0] dark:border-[#404854] text-base font-bold">
                            <span class="text-[#1E293B] dark:text-[#f1f5f9]">Total</span>
                            <span class="text-orange-600 dark:text-orange-400" x-text="formatRp(total())"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="mark_paid" value="1" class="w-4 h-4 text-orange-600 border-[#E2E8F0] rounded">
                <span class="text-sm text-[#1E293B] dark:text-[#f1f5f9]">Tandai sudah lunas</span>
            </label>
            <div class="flex items-center justify-end gap-3">
                @if($cancel === 'modal')
                    <button type="button" @click="$dispatch('close-manual-invoice')" class="px-4 py-2 text-sm text-[#94A3B8] hover:text-[#1E293B] dark:hover:text-[#f1f5f9]">Batal</button>
                @else
                    <a href="{{ route('admin.invoices.index') }}" class="px-4 py-2 text-sm text-[#94A3B8] hover:text-[#1E293B] dark:hover:text-[#f1f5f9]">Batal</a>
                @endif
                <button type="submit" class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg shadow-sm">
                    Simpan &amp; Buat Invoice
                </button>
            </div>
        </div>
    </form>