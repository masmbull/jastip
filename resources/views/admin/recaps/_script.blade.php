@push('scripts')
<script>
    {{-- Grid editor rekapan ala spreadsheet: kolom & baris bebas, Enter pindah
         ke bawah, Tab pindah ke kanan. Kolom angka/rupiah bisa ikut total. --}}
    function recapSheet(initialColumns, initialRows) {
        return {
            name: {{ Js::from(old('name', $recap->name ?? '')) }},
            columns: initialColumns,
            rows: initialRows,
            colSeq: 0,
            rowSeq: 0,
            saving: false,
            focusCol: null,

            init() {
                this.colSeq = this.columns.length;
                this.rowSeq = this.rows.length;
                if (!this.columns.length) this.addColumn();
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
            colTotal(key) {
                return this.rows.reduce((s, r) => s + (Number(r.cells[key]) || 0), 0);
            },
            grandTotal() {
                return this.columns
                    .filter(c => this.isNum(c) && c.total)
                    .reduce((s, c) => s + this.colTotal(c.key), 0);
            },
            fmt(n) { return (Number(n) || 0).toLocaleString('id-ID'); },
            formatRp(n) { return 'Rp ' + this.fmt(n); },

            // Keyboard: Enter -> baris berikutnya, Tab -> kolom berikutnya (native),
            // ArrowUp/Down pindah baris. Fokus dipindah manual supaya mirip Excel.
            move(e, rowIdx, colIdx, dir) {
                const nRows = this.rows.length;
                if (dir === 'down') { e.preventDefault(); this.focusCell(rowIdx + 1, colIdx); }
                if (dir === 'up') { e.preventDefault(); this.focusCell(rowIdx - 1, colIdx); }
                if (dir === 'next') { this.focusCell(rowIdx, colIdx + 1); }
            },
            focusCell(rowIdx, colIdx) {
                if (rowIdx < 0 || rowIdx >= this.rows.length) return;
                const el = this.$el.querySelector(`[data-cell="r${rowIdx}-c${colIdx}"]`);
                if (el) { el.focus(); el.select?.(); }
                else if (rowIdx >= this.rows.length - 1) { this.addRow(); }
            },
        };
    }
</script>
@endpush