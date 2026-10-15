@push('scripts')
<script>
    function manualInvoice() {
        const oldItems = {{ Js::from(collect(old('items', []))->values()) }};
        return {
            shipping: {{ (int) old('shipping_cost', 0) }},
            shipMethod: {{ Js::from(old('shipping_method', '')) }},
            mode: {{ Js::from(collect(['deposit', 'lunas'])->contains(old('mode', request('mode', 'deposit'))) ? old('mode', request('mode', 'deposit')) : 'deposit') }},
            feeAmt: {{ (int) old('fee', 0) }},
            showFee: {{ ((int) old('fee', 0)) > 0 ? 'true' : 'false' }},
            dpAmt: {{ (int) old('down_payment', 0) }},
            paidNow: false,
            city: {{ Js::from(old('shipping_city', '')) }},
            district: {{ Js::from(old('shipping_district', '')) }},
            village: {{ Js::from(old('shipping_village', '')) }},
            districts: [],
            villages: [],
            weight: {{ (float) old('weight', 0) }} ? {{ (float) old('weight', 0) }} : 1,
            weightManual: false,
            cities: [],
            quotes: [],
            quoting: false,
            quoteError: '',
            selectedCourier: '',
            items: oldItems.length ? oldItems.map((o, i) => ({
                key: i + 1, name: o.product_name || '', unit: o.unit || 'pcs',
                status: o.status || 'ready', qty: Number(o.quantity) || 1,
                price: Number(o.product_price) || 0, weight: Number(o.weight) || 0,
                open: false, loading: false, suggestions: [], _abort: null,
            })) : [{ key: 1, name: '', unit: 'pcs', status: 'ready', qty: 1, price: 0, weight: 0, open: false, loading: false, suggestions: [], _abort: null }],
            seq: oldItems.length || 1,
            saving: false,

            init() {
                // Tombol "Buat Invoice Deposit / Lunas" di luar komponen ini
                // kirim mode lewat event sebelum modal dibuka. Alpine v3 tidak
                // punya this.$on (itu API v2, dipanggil bikin init meledak),
                // jadi eventnya didengar langsung di window.
                this._onSetMode = (e) => this.setMode(e.detail);
                window.addEventListener('set-invoice-mode', this._onSetMode);
                fetch(`{{ route('admin.orders.shipping-options') }}`)
                    .then(r => r.json())
                    .then(j => { this.cities = j.cities || []; })
                    .catch(() => {});
                // Kota sudah terisi (old input / edit) -> langsung ambil ongkir
                // dan daftar kecamatan/kelurahan.
                if (this.city) {
                    this.fetchQuotes();
                    this.loadDistricts();
                    if (this.district) this.loadVillages();
                }
            },

            // Wilayah bertingkat: kota -> kecamatan -> kelurahan.
            async loadDistricts() {
                this.districts = [];
                this.villages = [];
                if (!this.city) return;
                try {
                    const res = await fetch(`{{ route('admin.orders.wilayah') }}?city=${encodeURIComponent(this.city)}`);
                    const j = await res.json();
                    this.districts = j.districts || [];
                } catch (e) { this.districts = []; }
            },
            async loadVillages() {
                this.villages = [];
                if (!this.city || !this.district) return;
                try {
                    const res = await fetch(`{{ route('admin.orders.wilayah') }}?city=${encodeURIComponent(this.city)}&district=${encodeURIComponent(this.district)}`);
                    const j = await res.json();
                    this.villages = j.villages || [];
                } catch (e) { this.villages = []; }
            },
            onCityChange() {
                this.district = '';
                this.village = '';
                this.loadDistricts();
                this.fetchQuotes();
            },
            onDistrictChange() {
                this.village = '';
                this.loadVillages();
            },

            // Modal dibuang tiap kali ditutup, jadi listener window harus
            // dilepas juga supaya tidak menumpuk.
            destroy() {
                window.removeEventListener('set-invoice-mode', this._onSetMode);
            },

            setMode(m) {
                this.mode = m === 'lunas' ? 'lunas' : 'deposit';
                if (this.mode === 'lunas') {
                    // Mode lunas: deposit dipaksa nol, jadi nggak ada sisa tagihan.
                    this.dpAmt = 0;
                }
            },

            // Berat efektif: kalau admin belum menimpa manual, dihitung dari
            // total berat produk (berat produk x qty). Ini yang dipakai untuk
            // request ongkir, jadi angka kg selalu cocok dengan isi keranjang.
            autoWeight() {
                const sum = this.items.reduce((s, i) => s + (Number(i.weight) || 0) * (Number(i.qty) || 0), 0);
                return Math.round(sum * 100) / 100;
            },
            effectiveWeight() {
                return this.weightManual ? (Number(this.weight) || 0) : this.autoWeight();
            },
            onWeightInput() {
                this.weightManual = true;
            },
            syncWeight() {
                if (!this.weightManual) this.weight = this.autoWeight() || this.weight;
            },
            recalcShipping() {
                this.syncWeight();
                if (this.city) this.fetchQuotes();
            },

            async fetchQuotes() {
                if (!this.city) return;
                this.syncWeight();
                this.quoting = true;
                this.quoteError = '';
                this.quotes = [];
                try {
                    const url = `{{ route('admin.orders.shipping-quote') }}?city=${encodeURIComponent(this.city)}&weight=${encodeURIComponent(this.effectiveWeight() || 1)}`;
                    const res = await fetch(url);
                    if (!res.ok) throw new Error('bad');
                    const j = await res.json();
                    this.quotes = (j.rows || []).filter(r => r.ok);
                    if (!this.quotes.length) this.quoteError = 'Tidak ada ekspedisi aktif untuk kota ini.';
                } catch (e) {
                    this.quoteError = 'Gagal memuat ongkir. Isi ekspedisi dan ongkir manual.';
                } finally {
                    this.quoting = false;
                }
            },
            pickQuote(q) {
                this.selectedCourier = q.code;
                this.shipMethod = q.name;
                this.shipping = q.price || 0;
            },

            addItem() {
                this.seq++;
                this.items.push({ key: this.seq, name: '', unit: 'pcs', status: 'ready', qty: 1, price: 0, weight: 0, open: false, loading: false, suggestions: [], _abort: null });
            },
            removeItem(idx) {
                if (this.items[idx]?._abort) this.items[idx]._abort.abort();
                this.items.splice(idx, 1);
                this.recalcShipping();
            },
            lineTotal(item) { return (Number(item.qty) || 0) * (Number(item.price) || 0); },
            subtotal() { return this.items.reduce((sum, i) => sum + this.lineTotal(i), 0); },
            total() { return this.subtotal() + (Number(this.shipping) || 0) + (Number(this.feeAmt) || 0); },
            dp() { return this.paidNow ? 0 : (Number(this.dpAmt) || 0); },
            toggleFee(on) {
                this.showFee = on;
                // Fee disembunyikan = fee ikut hilang dari total, jangan nyisa di belakang.
                if (!on) this.feeAmt = 0;
            },
            remaining() { return Math.max(0, this.total() - this.dp()); },
            formatRp(n) { return 'Rp ' + (Number(n) || 0).toLocaleString('id-ID'); },

            async searchProduct(idx) {
                const item = this.items[idx];
                const q = item.name.trim();
                if (q.length < 2) { item.suggestions = []; item.open = false; item.loading = false; return; }
                if (item._abort) item._abort.abort();
                const ctrl = item._abort = new AbortController();
                item.loading = true;
                item.open = true;
                try {
                    const res = await fetch(`{{ route('admin.orders.product-search') }}?q=${encodeURIComponent(q)}`, { signal: ctrl.signal });
                    item.suggestions = await res.json();
                } catch (e) {
                    if (e.name === 'AbortError') return;
                    item.suggestions = [];
                } finally {
                    if (item._abort === ctrl) { item.loading = false; item._abort = null; }
                }
            },
            pickProduct(idx, s) {
                this.items[idx].name = s.name;
                this.items[idx].price = s.price;
                if (s.unit) this.items[idx].unit = s.unit;
                this.items[idx].weight = Number(s.weight) || 0;
                this.items[idx].open = false;
                this.recalcShipping();
            },
        };
    }
</script>
@endpush
