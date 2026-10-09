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
            shipping: {{ (int) old('shipping_cost', 0) }},
            feeAmt: {{ (int) old('fee', 0) }},
            items: [{ key: 1, name: '', unit: 'pcs', qty: 1, price: 0, open: false, suggestions: [] }],
            seq: 1,

            addItem() {
                this.seq++;
                this.items.push({ key: this.seq, name: '', unit: 'pcs', qty: 1, price: 0, open: false, suggestions: [] });
            },
            removeItem(idx) { this.items.splice(idx, 1); },
            lineTotal(item) { return (Number(item.qty) || 0) * (Number(item.price) || 0); },
            subtotal() { return this.items.reduce((sum, i) => sum + this.lineTotal(i), 0); },
            total() { return this.subtotal() + (Number(this.shipping) || 0) + (Number(this.feeAmt) || 0); },
            formatRp(n) { return 'Rp ' + (Number(n) || 0).toLocaleString('id-ID'); },

            async searchCustomer() {
                const q = this.customerName.trim();
                if (q.length < 2) { this.hits = []; this.openCustomer = false; return; }
                const res = await fetch(`{{ route('admin.orders.customer-search') }}?q=${encodeURIComponent(q)}`);
                this.hits = await res.json();
                this.openCustomer = true;
            },
            pickCustomer(h) {
                this.customerName = h.customer_name;
                this.whatsapp = h.customer_whatsapp;
                this.address = h.customer_address || '';
                this.openCustomer = false;
            },
            async searchProduct(idx) {
                const q = this.items[idx].name.trim();
                if (q.length < 2) { this.items[idx].suggestions = []; this.items[idx].open = false; return; }
                const res = await fetch(`{{ route('admin.orders.product-search') }}?q=${encodeURIComponent(q)}`);
                this.items[idx].suggestions = await res.json();
                this.items[idx].open = true;
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