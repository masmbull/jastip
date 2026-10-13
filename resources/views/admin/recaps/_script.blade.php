@push('scripts')
<script>
    {{-- Alpine grid editor untuk lembar rekapan. Kolom & baris bisa
         ditambah/dihapus bebas; kolom angka bisa ikut grand total. --}}
    function recapSheet(initialColumns, initialRows) {
        return {
            name: {{ Js::from(old('name', $recap->name ?? '')) }},
            columns: initialColumns,
            rows: initialRows,
            colSeq: 0,
            rowSeq: 0,
            saving: false,

            init() {
                this.colSeq = this.columns.length;
                this.rowSeq = this.rows.length;
            },

            addColumn() {
                this.colSeq++;
                const key = 'c' + this.colSeq;
                this.columns.push({ key, label: '', type: 'text', total: false });
                this.rows.forEach(r => { if (!(key in r.cells)) r.cells[key] = ''; });
            },
            removeColumn(idx) {
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

            isNum(col) { return col.type === 'number'; },
            colTotal(key) {
                return this.rows.reduce((s, r) => s + (Number(r.cells[key]) || 0), 0);
            },
            grandTotal() {
                return this.columns
                    .filter(c => c.type === 'number' && c.total)
                    .reduce((s, c) => s + this.colTotal(c.key), 0);
            },
            formatRp(n) { return 'Rp ' + (Number(n) || 0).toLocaleString('id-ID'); },
        };
    }
</script>
@endpush