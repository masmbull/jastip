@push('scripts')
<script>
    {{-- Alpine component for the manual-invoice form. Pushed to the `scripts`
         stack so both the full page and the modal reuse one definition. --}}
    function manualInvoice() {
        return {
            customerName: {{ Js::from(old('customer_name', '')) }},
            whatsapp: {{ Js::from(old('customer_whatsapp', '')) }},
            address: {{ Js::from(old('customer_address', '')) }},
            hits: [],
            openCustomer: false,
            loadingCustomer: false,
            _customerAbort: null,
            shipping: {{ (int) old('shipping_cost', 0) }},
            feeAmt: {{ (int) old('fee', 0) }},
            items: [{ key: 1, name: '', unit: 'pcs', qty: 1, price: 0, open: false, loading: false, suggestions: [], _abort: null }],
            seq: 1,
            saving: false,

            addItem() {
                this.seq++;
                this.items.push({ key: this.seq, name: '', unit: 'pcs', qty: 1, price: 0, open: false, loading: false, suggestions: [], _abort: null });
            },
            removeItem(idx) {
                if (this.items[idx]?._abort) this.items[idx]._abort.abort();
                this.items.splice(idx, 1);
            },
            lineTotal(item) { return (Number(item.qty) || 0) * (Number(item.price) || 0); },
            subtotal() { return this.items.reduce((sum, i) => sum + this.lineTotal(i), 0); },
            total() { return this.subtotal() + (Number(this.shipping) || 0) + (Number(this.feeAmt) || 0); },
            formatRp(n) { return 'Rp ' + (Number(n) || 0).toLocaleString('id-ID'); },

            async searchCustomer() {
                const q = this.customerName.trim();
                if (q.length < 2) { this.hits = []; this.openCustomer = false; this.loadingCustomer = false; return; }
                if (this._customerAbort) this._customerAbort.abort();
                const ctrl = this._customerAbort = new AbortController();
                this.loadingCustomer = true;
                this.openCustomer = true;
                try {
                    const res = await fetch(`{{ route('admin.orders.customer-search') }}?q=${encodeURIComponent(q)}`, { signal: ctrl.signal });
                    this.hits = await res.json();
                } catch (e) {
                    if (e.name === 'AbortError') return;
                    this.hits = [];
                } finally {
                    if (this._customerAbort === ctrl) { this.loadingCustomer = false; this._customerAbort = null; }
                }
            },
            pickCustomer(h) {
                this.customerName = h.customer_name;
                this.whatsapp = h.customer_whatsapp;
                this.address = h.customer_address || '';
                this.openCustomer = false;
            },
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