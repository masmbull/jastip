@push('scripts')
<script>
    {{-- Editor rekapan bergaya pivot/spreadsheet: header kolom, gridlines,
         baris Total otomatis, dan template siap pakai. Kolom angka (Angka/
         Rupiah) bisa ditandai ikut Total & Grand Total. --}}
    function recapSheet(initialColumns, initialRows) {
        return {
            name: {{ Js::from(old('name', $recap->name ?? '')) }},
            columns: initialColumns,
            rows: initialRows,
            colSeq: 0,
            rowSeq: 0,
            saving: false,

            templates: {
                pesanan: {
                    name: 'Rekap Pesanan',
                    columns: [
                        { key: 'pelanggan', label: 'Pelanggan', type: 'text', total: false },
                        { key: 'produk', label: 'Produk', type: 'text', total: false },
                        { key: 'qty', label: 'Qty', type: 'number', total: true },
                        { key: 'harga', label: 'Harga', type: 'currency', total: true },
                    ],
                },
                po: {
                    name: 'Rekap PO',
                    columns: [
                        { key: 'supplier', label: 'Supplier', type: 'text', total: false },
                        { key: 'item', label: 'Item', type: 'text', total: false },
                        { key: 'qty', label: 'Qty', type: 'number', total: true },
                        { key: 'modal', label: 'Modal', type: 'currency', total: true },
                        { key: 'jual', label: 'Jual', type: 'currency', total: true },
                    ],
                },
                kosong: {
                    name: 'Rekapan Baru',
                    columns: [{ key: 'keterangan', label: 'Keterangan', type: 'text', total: false }],
                },
            },

            init() {
                if (!this.columns.length) this.applyTemplate('pesanan');
                this.colSeq = this.columns.length;
                this.rowSeq = this.rows.length;
            },

            applyTemplate(key) {
                const t = this.templates[key];
                if (!t) return;
                this.columns = t.columns.map(c => ({ ...c }));
                this.rows = [];
                this.colSeq = this.columns.length;
                this.rowSeq = 0;
                this.name = this.name || t.name;
            },

            addColumn() {
                this.colSeq++;
                const key = 'c' + this.colSeq;
                this.columns.push({ key, label: '', type: 'text', total: false });
                this.rows.forEach(r => { if (!(key in r.cells)) r.cells[key] = ''; });
                this.$nextTick(() => {
                    const el = this.$el.querySelector(`[data-collabel="${key}"]`);
                    if (el) el.focus();
                });
            },
            removeColumn(idx) {
                if (this.columns.length <= 1) return;
                const key = this.columns[idx].key;
                this.columns.splice(idx, 1);
                this.rows.forEach(r => { delete r.cells[key]; });
            },
            addRow() {
                this.rowSeq++;
                const cells = {};
                this.columns.forEach(c => { cells[c.key] = ''; });
                this.rows.push({ key: 'r' + this.rowSeq, cells });
            },
            removeRow(idx) { this.rows.splice(idx, 1); },

            isNum(col) { return col.type === 'number' || col.type === 'currency'; },
            colTotal(key) { return this.rows.reduce((s, r) => s + (Number(r.cells[key]) || 0), 0); },
            grandTotal() {
                return this.columns
                    .filter(c => this.isNum(c) && c.total)
                    .reduce((s, c) => s + this.colTotal(c.key), 0);
            },
            fmt(n) { return (Number(n) || 0).toLocaleString('id-ID'); },
            formatRp(n) { return 'Rp ' + this.fmt(n); },
            display(col, val) {
                if (!this.isNum(col)) return val;
                return col.type === 'currency' ? this.formatRp(val) : this.fmt(val);
            },

            focusCell(rowIdx, colIdx) {
                if (rowIdx < 0 || rowIdx >= this.rows.length) return;
                const el = this.$el.querySelector(`[data-cell="r${rowIdx}-c${colIdx}"]`);
                if (el) { el.focus(); el.select?.(); }
            },
            move(e, rowIdx, colIdx, dir) {
                if (dir === 'down') { e.preventDefault(); this.focusCell(rowIdx + 1, colIdx); }
                if (dir === 'up') { e.preventDefault(); this.focusCell(rowIdx - 1, colIdx); }
            },
        };
    }
</script>
@endpush