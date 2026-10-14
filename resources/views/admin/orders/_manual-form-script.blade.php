@push('scripts')
<script>
    function manualInvoice() {
        const oldItems = {{ Js::from(collect(old('items', []))->values()) }};
        return {
            shipping: {{ (int) old('shipping_cost', 0) }},
            shipMethod: {{ Js::from(old('shipping_method', '')) }},
            feeAmt: {{ (int) old('fee', 0) }},
            dpAmt: {{ (int) old('down_payment', 0) }},
            paidNow: false,
            city: {{ Js::from(old('shipping_city', '')) }},
            weight: 1,
            cities: [],
            quotes: [],
            quoting: false,
            quoteError: '',
            selectedCourier: '',
            items: oldItems.length ? oldItems.map((o, i) => ({
                key: i + 1, name: o.product_name || '', unit: o.unit || 'pcs',
                status: o.status || 'ready', qty: Number(o.quantity) || 1,
                price: Number(o.product_price) || 0,
                open: false, loading: false, suggestions: [], _abort: null,
            })) : [{ key: 1, name: '', unit: 'pcs', status: 'ready', qty: 1, price: 0, open: false, loading: false, suggestions: [], _abort: null }],
            seq: oldItems.length || 1,
            saving: false,

            init() {
                fetch(`{{ route('admin.orders.shipping-options') }}`)
                    .then(r => r.json())
                    .then(j => { this.cities = j.cities || []; })
                    .catch(() => {});
                // Kota sudah terisi (old input / edit) -> langsung ambil ongkir.
                if (this.city) this.fetchQuotes();
            },

            async fetchQuotes() {
                if (!this.city) return;
                this.quoting = true;
                this.quoteError = '';
                this.quotes = [];
                try {
                    const url = `{{ route('admin.orders.shipping-quote') }}?city=${encodeURIComponent(this.city)}&weight=${encodeURIComponent(this.weight || 1)}`;
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
                this.items.push({ key: this.seq, name: '', unit: 'pcs', status: 'ready', qty: 1, price: 0, open: false, loading: false, suggestions: [], _abort: null });
            },
            removeItem(idx) {
                if (this.items[idx]?._abort) this.items[idx]._abort.abort();
                this.items.splice(idx, 1);
            },
            lineTotal(item) { return (Number(item.qty) || 0) * (Number(item.price) || 0); },
            subtotal() { return this.items.reduce((sum, i) => sum + this.lineTotal(i), 0); },
            total() { return this.subtotal() + (Number(this.shipping) || 0) + (Number(this.feeAmt) || 0); },
            dp() { return this.paidNow ? 0 : (Number(this.dpAmt) || 0); },
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
                this.items[idx].open = false;
            },
        };
    }
</script>
@endpush
