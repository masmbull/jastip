/*
 | Nitip UI — shared: top progress, async forms (data-async),
 | confirm bridge (data-confirm), client toasts, cart badge. Vanilla JS.
 |--------------------------------------------------------------------------
 */
/* Alpine is bundled (not CDN) so the admin sidebar, modals and dark-mode
   toggles work offline / behind a blocked CDN. Must be defined before Alpine
   walks the DOM — hence the import at the very top. */
import Alpine from 'alpinejs';
window.Alpine = Alpine;

(() => {
    "use strict";

    const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const isJson = (r) => (r.headers.get('content-type') || '').includes('application/json');

    /* ---- top progress bar ---- */
    let busy = 0;
    function progressBar() {
        let b = document.getElementById('jd-progress');
        if (!b) {
            b = document.createElement('div');
            b.id = 'jd-progress';
            b.className = 'fixed inset-x-0 top-0 h-0.5 bg-rose-500 scale-x-0 origin-left transition-transform duration-[250ms] ease-out z-[200]';
            document.body.prepend(b);
        }
        return b;
    }
    function progress(on) {
        busy += on ? 1 : -1;
        if (busy < 0) busy = 0;
        const b = progressBar();
        b.classList.toggle('scale-x-100', busy > 0);
        b.classList.toggle('scale-x-0', busy === 0);
    }

    /* ---- toasts ---- */
    function toastHost() {
        let h = document.getElementById('jd-toasts');
        if (!h) {
            h = document.createElement('div');
            h.id = 'jd-toasts';
                        h.className = 'fixed bottom-6 left-6 z-[200] flex flex-col gap-2 w-full max-w-sm';
            document.body.prepend(h);
        }
        return h;
    }
    function toast(message, type = 'success', duration = 3800) {
        const host = toastHost();
        const el = document.createElement('div');
        const colors = { success: 'bg-green-600', error: 'bg-red-600', info: 'bg-blue-600' };
        el.className = (`flex items-start gap-2.5 px-4 py-3 rounded-xl shadow-lg text-white text-sm ` +
            (colors[type] || colors.success) + ' opacity-0 translate-x-4 transition-all duration-300');
        el.innerHTML = '<span class="flex-1 break-words"></span>';
        el.firstElementChild.textContent = message;
        host.prepend(el);
        requestAnimationFrame(() => {
            el.classList.replace('opacity-0', 'opacity-100');
            el.classList.replace('translate-x-4', 'translate-x-0');
        });
        setTimeout(() => {
            el.classList.replace('opacity-100', 'opacity-0');
            el.classList.replace('translate-x-0', 'translate-x-4');
            el.addEventListener('transitionend', () => el.remove());
        }, duration);
    }
    window.toast = toast; // inline page scripts (reviews, payment) reuse the toast host

    /* ---- confirm modal bridge ---- */
    function confirmAction(message, title = 'Konfirmasi', okLabel = 'Ya, lanjutkan') {
        return new Promise((resolve) => {
            window.dispatchEvent(new CustomEvent('jd-confirm', {
                detail: { title, message, okLabel, resolve },
            }));
        });
    }

    /* ---- navbar badge ---- */
    function updateCartBadge(count) {
        const el = document.getElementById('cart-count');
        if (!el) return;
        el.textContent = count;
        el.classList.toggle('hidden', !count || count === 0);
    }
    
    /* ---- submit button spinner ---- */
    function setBusy(btn, on) {
        if (!btn) return;
        if (on) {
            btn.disabled = true;
            btn.dataset.saved = btn.innerHTML;
            btn.innerHTML = '<span class="inline-flex items-center justify-center gap-2">' +
                '<svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">' +
                '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>' +
                '<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.37 0 0 5.37 0 12h4z"></path></svg>Menyimpan…</span>';
        } else {
            btn.disabled = false;
            if (btn.dataset.saved) { btn.innerHTML = btn.dataset.saved; delete btn.dataset.saved; }
        }
    }

    async function submitForm(form) {
        const btn = form.querySelector('button[type="submit"]') || form.querySelector('input[type="submit"]');
        const isCartMutation = /update|hapus|clear/.test(form.action);
        progress(true);
        setBusy(btn, true);
        try {
            const res = await fetch(form.action, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf(),
                    'Accept': 'application/json, text/html, */*',
                },
                body: new FormData(form),
            });
            if (isJson(res)) {
                const data = await res.json();
                toast(data.message || (data.success ? 'Berhasil' : 'Gagal'),
                    data.success === false ? 'error' : 'success');
                if (typeof data.count === 'number') updateCartBadge(data.count);
                if (isCartMutation) setTimeout(() => location.reload(), 900);
            } else {
                setTimeout(() => location.reload(), 600);
            }
        } catch (err) {
            toast('Gagal menghubungi server.', 'error');
        } finally {
            setBusy(btn, false);
            progress(false);
        }
    }

    document.addEventListener('submit', (e) => {
        const form = e.target.closest('form[data-async], form[data-confirm]');
        if (!form) return;
        e.preventDefault();
        e.stopImmediatePropagation();
        const msg = form.dataset.confirm;
        if (form.target === '_blank' && msg) {
            // Keep the new-tab submission in the click so browsers do not block it.
            if (window.confirm(msg)) HTMLFormElement.prototype.submit.call(form);
            return;
        }
        if (msg) {
            confirmAction(msg).then((ok) => { if (ok) submitForm(form); });
        } else {
            submitForm(form);
        }
    });

    /* ---- wishlist toggle ---- */
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('[data-wishlist-toggle]');
        if (!btn) return;
        e.preventDefault();
        btn.disabled = true;
        progress(true);
        try {
            const res = await fetch(btn.dataset.url, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf(),
                    'Accept': 'application/json',
                },
            });
            if (res.status === 401) {
                toast('Silakan masuk untuk menyimpan wishlist', 'info');
                return;
            }
            const data = await res.json();
            btn.classList.toggle('text-red-500', data.wishlisted);
            const fill = data.wishlisted ? 'currentColor' : 'none';
            btn.querySelectorAll('svg path').forEach((p) => p.setAttribute('fill', fill));
            const label = btn.querySelector('[data-wishlist-label]');
            if (label) label.textContent = data.wishlisted ? 'Hapus dari Wishlist' : 'Tambah ke Wishlist';
            toast(data.message || 'Berhasil');
            // Wishlist page: the item row is now gone.
            if (window.location.pathname.includes('/wishlist') && !data.wishlisted) {
                setTimeout(() => location.reload(), 700);
            }
        } catch (err) {
            toast('Gagal menghubungi server.', 'error');
        } finally {
            btn.disabled = false;
            progress(false);
        }
    });

    /* ---- progress on full-page navigation ---- */
    let navigating = false;
    document.addEventListener('click', (e) => {
        const a = e.target.closest('a[href]');
        if (!a || a.target === '_blank' || a.origin !== location.origin) return;
        navigating = true;
        progress(true);
    });
    const stopProgress = () => { progress(false); navigating = false; };
    window.addEventListener('load', stopProgress);
    window.addEventListener('pageshow', stopProgress);

    /* ---- Alpine data for the confirm modal ---- */
    window.jdConfirm = function (t, m, ok) {
        return {
            open: false, title: t, message: m, okLabel: ok, resolver: null,
            ask(d) {
                if (d) {
                    this.title = d.title || this.title;
                    this.message = d.message || this.message;
                    this.okLabel = d.okLabel || this.okLabel;
                    this.resolver = d.resolve || this.resolver;
                }
                this.open = true;
            },
            ok() { this.open = false; const r = this.resolver; this.resolver = null; if (r) r(true); },
            cancel() { this.open = false; const r = this.resolver; this.resolver = null; if (r) r(false); },
        };
    };

    /* ---- counters (dashboard) ---- */
        window.JDnum = function (to) {
    return {
        n: 0,
        start() {
            const target = Number(to) || 0;
            if (this.n >= target) return;
            const step = Math.max(1, Math.ceil(target / 60));
            const t = setInterval(() => {
                this.n = Math.min(this.n + step, target);
                if (this.n >= target) { clearInterval(t); this.n = target; }
            }, 16);
        },
    };
};

/* ---- quick-view modal (orders) ---- */
    window.jdQuickview = function () {
        return {
            show: false, title: 'Detail', html: '',
            open(d) {
                this.title = d.title || 'Detail';
                this.html = '<p class="p-3 text-sm text-[#999999]">Memuat...</p>';
                this.show = true;
                const t = this;
                fetch(d.url, { headers: { 'X-Requested-With': 'true' }, credentials: 'same-origin' })
                    .then(r => r.ok ? r.text() : Promise.reject(r.status))
                    .then(h => { t.html = h; })
                    .catch(() => { t.html = '<p class="p-3 text-sm text-red-600">Gagal memuat detail.</p>'; });
            },
        };
    };

    /* ---- invoice modal (print) ----
       Invoice ditampilkan dalam <iframe> supaya cetak (Ctrl+P / window.print)
       hanya mencetak lembar invoice-nya, bukan seluruh panel admin. */
    window.jdInvoiceModal = function () {
        return {
            show: false, url: '', title: 'Invoice',
            open(d) {
                this.url = d.url;
                this.title = d.title || 'Invoice';
                this.show = true;
            },
            close() { this.show = false; this.url = ''; },
            print() {
                const f = this.$refs.frame;
                if (f && f.contentWindow) {
                    f.contentWindow.focus();
                    f.contentWindow.print();
                }
            },
        };
    };

    /* ---- Kirim invoice PDF sebagai FILE ke WhatsApp (Web Share) ----
       Link wa.me TIDAK bisa melampirkan file, itu sebabnya sebelumnya cuma
       terkirim link. Di sini PDF asli (dirender server, dompdf) diambil sebagai
       blob, dibungkus File, lalu dibagikan lewat Share Sheet Android dengan
       file terlampir. Fallback (browser tanpa file sharing): unduh PDF +
       buka chat WhatsApp dengan pesan siap kirim. Tidak mengklaim terkirim. */
    window.jdShareInvoicePdf = async function (btn, opts) {
        const { url, filename, text, waUrl } = opts;
        if (btn && btn.dataset.busy === '1') return;

        const setBusy = (on) => {
            if (!btn) return;
            btn.dataset.busy = on ? '1' : '';
            btn.disabled = !!on;
            if (on) btn.setAttribute('aria-busy', 'true');
            else btn.removeAttribute('aria-busy');
        };

        setBusy(true);
        try {
            const res = await fetch(url, { credentials: 'same-origin' });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const blob = await res.blob();
            if (!blob.size) throw new Error('kosong');

            const file = new File([blob], filename || 'invoice.pdf', { type: 'application/pdf' });

            if (navigator.canShare && navigator.canShare({ files: [file] })) {
                try {
                    await navigator.share({ files: [file], title: filename || 'Invoice', text: text || '' });
                } catch (err) {
                    // Batal share = bukan kegagalan.
                    if (err && err.name !== 'AbortError' && typeof window.toast === 'function') {
                        window.toast('Gagal membagikan invoice. Coba lagi.', 'error');
                    }
                }
            } else {
                // Fallback: unduh PDF, lalu buka chat WhatsApp (tanpa lampiran).
                const objectUrl = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = objectUrl;
                a.download = filename || 'invoice.pdf';
                document.body.appendChild(a);
                a.click();
                a.remove();
                setTimeout(() => URL.revokeObjectURL(objectUrl), 4000);
                if (waUrl) window.open(waUrl, '_blank', 'noopener');
                if (typeof window.toast === 'function') {
                    window.toast('Browser ini belum bisa lampirkan file. PDF diunduh, chat WhatsApp dibuka: lampirkan PDF-nya manual.', 'info');
                }
            }
        } catch (e) {
            console.error('[jdShareInvoicePdf]', e);
            if (typeof window.toast === 'function') window.toast('Gagal menyiapkan PDF invoice. Coba lagi.', 'error');
        } finally {
            setBusy(false);
        }
    };

    window.jdShareInvoiceImage = async function (btn) {
        if (!btn || btn.dataset.busy === '1') return;

        const { shareUrl, shareFilename, shareText, shareWaUrl } = btn.dataset;
        const shareIcon = btn.querySelector('[data-share-icon]');
        const shareSpinner = btn.querySelector('[data-share-spinner]');
        const originalLabel = btn.getAttribute('aria-label');
        let host = null;
        const setBusy = (busy) => {
            btn.dataset.busy = busy ? '1' : '';
            btn.disabled = busy;
            if (busy) {
                btn.setAttribute('aria-busy', 'true');
                btn.setAttribute('aria-label', 'Menyiapkan gambar invoice...');
            } else {
                btn.removeAttribute('aria-busy');
                if (originalLabel) btn.setAttribute('aria-label', originalLabel);
            }
            shareIcon?.classList.toggle('hidden', busy);
            shareSpinner?.classList.toggle('hidden', !busy);
        };
        setBusy(true);

        const downloadAndOpenChat = (blob) => {
            const objectUrl = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = objectUrl;
            link.download = shareFilename || 'invoice.png';
            document.body.appendChild(link);
            link.click();
            link.remove();
            setTimeout(() => URL.revokeObjectURL(objectUrl), 4000);
            if (shareWaUrl) window.open(shareWaUrl, '_blank', 'noopener');
            if (typeof window.toast === 'function') {
                window.toast('Gambar invoice diunduh. Di WhatsApp, lampirkan gambar lalu periksa dan kirim caption.', 'info');
            }
        };

        try {
            const response = await fetch(shareUrl, {
                credentials: 'same-origin',
                headers: { Accept: 'text/html' },
            });
            if (!response.ok) throw new Error('HTTP ' + response.status);

            const documentHtml = new DOMParser().parseFromString(await response.text(), 'text/html');
            const invoice = documentHtml.querySelector('.invoice-doc');
            if (!invoice) throw new Error('Template invoice tidak ditemukan');

            host = document.createElement('div');
            host.setAttribute('aria-hidden', 'true');
            host.inert = true;
            host.style.cssText = 'position:fixed;left:-10000px;top:0;width:794px;height:1123px;z-index:-1;';

            const page = document.createElement('div');
            page.style.cssText = 'position:relative;box-sizing:border-box;width:794px;height:1123px;padding:40px 42px;background:#fff;overflow:hidden;';

            const accent = document.createElement('div');
            accent.style.cssText = 'position:absolute;top:0;left:0;width:100%;height:12px;background:#FFC800;';

            const contentArea = document.createElement('div');
            contentArea.style.cssText = 'box-sizing:border-box;width:100%;height:1043px;overflow:hidden;';

            const invoiceCopy = invoice.cloneNode(true);
            invoiceCopy.style.cssText += 'box-sizing:border-box;width:100%;margin:0;font-size:14px;line-height:1.35;';
            invoiceCopy.querySelectorAll('table').forEach((table) => {
                table.style.borderCollapse = 'collapse';
                table.style.borderSpacing = '0';
                table.style.maxWidth = '100%';
            });
            invoiceCopy.querySelectorAll('td, div, span').forEach((element) => {
                element.style.boxSizing = 'border-box';
            });
            invoiceCopy.querySelectorAll('td').forEach((cell) => {
                cell.style.overflowWrap = 'anywhere';
            });
            contentArea.append(invoiceCopy);
            page.append(accent, contentArea);
            host.append(page);
            document.body.append(host);

            await Promise.all(Array.from(host.querySelectorAll('img')).map((image) => {
                if (image.complete) {
                    return image.naturalWidth > 0 ? Promise.resolve() : Promise.reject(new Error('Gambar invoice gagal dimuat'));
                }

                return new Promise((resolve, reject) => {
                    image.addEventListener('load', resolve, { once: true });
                    image.addEventListener('error', () => reject(new Error('Gambar invoice gagal dimuat')), { once: true });
                });
            }));

            const availableHeight = contentArea.clientHeight;
            const availableWidth = contentArea.clientWidth;
            const contentHeight = invoiceCopy.scrollHeight;
            if (!availableHeight || !availableWidth || !contentHeight) {
                throw new Error('Ukuran invoice tidak dapat dihitung');
            }

            // Tunggu font invoice siap supaya teks tidak jatuh ke fallback
            // saat di-render (host tidak memuat stylesheet invoice).
            if (document.fonts && document.fonts.ready) {
                await document.fonts.ready;
            }

            const { default: html2canvas } = await import('html2canvas');
            const captureOptions = {
                backgroundColor: '#FFFFFF',
                // HD: minimal 3x (2382x3369px) supaya tajam di WhatsApp.
                scale: Math.max(3, window.devicePixelRatio || 1),
                windowWidth: 794,
                windowHeight: 1123,
                useCORS: true,
                logging: false,
            };
            let canvas;

            if (contentHeight <= availableHeight) {
                canvas = await html2canvas(page, captureOptions);
            } else {
                const overflowHost = document.createElement('div');
                overflowHost.style.cssText = `position:absolute;top:0;left:0;width:${availableWidth}px;`;
                overflowHost.append(invoiceCopy);
                host.append(overflowHost);

                const contentCanvas = await html2canvas(invoiceCopy, {
                    ...captureOptions,
                    height: contentHeight,
                });
                canvas = document.createElement('canvas');
                canvas.width = 794 * captureOptions.scale;
                canvas.height = 1123 * captureOptions.scale;

                const context = canvas.getContext('2d');
                if (!context) throw new Error('Canvas invoice tidak dapat dibuat');

                context.fillStyle = '#FFFFFF';
                context.fillRect(0, 0, canvas.width, canvas.height);
                const fitScale = Math.min(
                    (availableWidth * captureOptions.scale) / contentCanvas.width,
                    (availableHeight * captureOptions.scale) / contentCanvas.height
                );
                const drawWidth = contentCanvas.width * fitScale;
                const drawHeight = contentCanvas.height * fitScale;
                const offsetX = 42 * captureOptions.scale + ((availableWidth * captureOptions.scale) - drawWidth) / 2;
                const offsetY = 40 * captureOptions.scale;
                context.drawImage(contentCanvas, offsetX, offsetY, drawWidth, drawHeight);
            }
            const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/png'));
            if (!blob) throw new Error('Gambar invoice gagal dibuat');

            const file = new File([blob], shareFilename || 'invoice.png', { type: 'image/png' });
            if (navigator.canShare?.({ files: [file] })) {
                try {
                    await navigator.share({ files: [file], title: shareFilename || 'Invoice', text: shareText || '' });
                    if (typeof window.toast === 'function') {
                        window.toast('Menu berbagi dibuka. Pilih WhatsApp, akun pengirim, dan kontak sebelum mengirim.', 'info');
                    }
                } catch (error) {
                    if (error?.name === 'AbortError') return;
                    downloadAndOpenChat(blob);
                }
            } else {
                downloadAndOpenChat(blob);
            }
        } catch (error) {
            console.error('[jdShareInvoiceImage]', error);
            if (typeof window.toast === 'function') {
                window.toast('Gagal menyiapkan gambar invoice. Coba lagi.', 'error');
            }
        } finally {
            host?.remove();
            setBusy(false);
        }
    };

    /* ---- Bagikan invoice baris Rekapan via WhatsApp (PNG + Web Share) ----
       Alur: susun invoice sebagai DOM (warna hex inline supaya html2canvas
       tidak tersandung color(oklch) milik Tailwind v4) -> render ke PNG ->
       bagikan lewat Web Share API (Android: muncul sheet, pilih WhatsApp).
       Kalau file sharing tak didukung (umumnya desktop), fallback: unduh PNG
       + buka chat WhatsApp berisi teks. TIDAK mengklaim terkirim otomatis:
       pengiriman tetap butuh aksi pengguna di WhatsApp. */
    window.waPhone = function (input) {
        let d = String(input || '').replace(/[^0-9]/g, '');
        if (!d) return '';
        if (d.startsWith('0')) return '62' + d.slice(1);
        if (d.startsWith('62')) return d;
        if (d.startsWith('8')) return '62' + d;
        return d;
    };

    (function defineRecapInvoice() {
        const esc = (s) => String(s ?? '').replace(/[&<>"]/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));

        function buildNode(doc) {
            const wrap = document.createElement('div');
            wrap.style.cssText = 'position:fixed;left:-10000px;top:0;width:720px;background:#FFFFFF;color:#141414;font-family:Arial,Helvetica,sans-serif;font-size:12px;padding:36px;box-sizing:border-box;';

            const row = (l, r, strong) => `<tr><td style="padding:2px 0;color:${strong ? '#141414' : '#475569'};${strong ? 'font-weight:bold;' : ''}">${esc(l)}</td><td style="padding:2px 0;text-align:right;${strong ? 'font-weight:bold;' : ''}">${esc(r)}</td></tr>`;
            const items = (doc.items || []).map((it) => `
                <tr>
                    <td style="border-bottom:1px solid #E2E8F0;padding:7px 8px;">${esc(it.name)}</td>
                    <td style="border-bottom:1px solid #E2E8F0;padding:7px 8px;text-align:center;">${esc(it.qty)}</td>
                    <td style="border-bottom:1px solid #E2E8F0;padding:7px 8px;text-align:right;">${esc(it.price)}</td>
                    <td style="border-bottom:1px solid #E2E8F0;padding:7px 8px;text-align:right;font-weight:bold;">${esc(it.subtotal)}</td>
                </tr>`).join('');

            const brand = esc(doc.brand || 'NITIP DI END');
            wrap.innerHTML = `
                <table style="width:100%;border-collapse:collapse;">
                    <tr>
                        <td style="vertical-align:top;">
                            <div style="font-size:20px;font-weight:bold;">${brand}</div>
                            <div style="color:#475569;">Invoice Rekapan</div>
                        </td>
                        <td style="vertical-align:top;text-align:right;">
                            <div style="font-size:20px;font-weight:bold;">INVOICE</div>
                            <div style="font-weight:bold;">#${esc(doc.row)}</div>
                            <div style="color:#475569;">${esc(doc.date)}</div>
                            <div style="margin-top:6px;display:inline-block;padding:3px 9px;border:2px solid #B45309;color:#B45309;font-weight:bold;font-size:10px;">${esc(doc.status)}</div>
                        </td>
                    </tr>
                </table>
                <div style="border-top:3px solid #141414;margin:6px 0 14px;"></div>
                <table style="width:100%;border-collapse:collapse;">
                    <tr><td style="vertical-align:top;">
                        <div style="color:#475569;font-size:10px;font-weight:bold;">DITAGIHKAN KEPADA</div>
                        <div style="font-weight:bold;padding-top:2px;">${esc(doc.customer_name)}</div>
                        ${doc.wa ? `<div style="color:#475569;">WA: ${esc(doc.wa)}</div>` : ''}
                    </td>
                    <td style="vertical-align:top;text-align:right;">
                        <div style="color:#475569;font-size:10px;font-weight:bold;">REKAPAN</div>
                        <div style="font-weight:bold;padding-top:2px;">${esc(doc.number)}</div>
                    </td></tr>
                </table>
                ${items ? `<table style="width:100%;border-collapse:collapse;margin-top:14px;">
                    <tr>
                        <td style="background:#141414;color:#FFFFFF;font-weight:bold;padding:7px 8px;">Barang</td>
                        <td style="background:#141414;color:#FFFFFF;font-weight:bold;padding:7px 8px;text-align:center;">Qty</td>
                        <td style="background:#141414;color:#FFFFFF;font-weight:bold;padding:7px 8px;text-align:right;">Harga</td>
                        <td style="background:#141414;color:#FFFFFF;font-weight:bold;padding:7px 8px;text-align:right;">Subtotal</td>
                    </tr>${items}</table>` : ''}
                <table style="width:100%;border-collapse:collapse;margin-top:14px;">
                    <tr><td style="width:55%;"></td><td><table style="width:100%;border-collapse:collapse;">
                        ${row('Total', doc.total, true)}
                        ${row('Deposit', doc.dp)}
                        ${row('Sisa', doc.sisa, true)}
                    </table></td></tr>
                </table>
                <div style="border-top:1px solid #E2E8F0;margin:16px 0 8px;"></div>
                <div style="text-align:center;color:#475569;">${esc(doc.footer || '')}</div>`;
            return wrap;
        }

        window.jdRecapInvoice = async function (btn, docsJson, index) {
            if (btn && btn.dataset.busy === '1') return; // guard double-click
            let docs;
            try {
                docs = typeof docsJson === 'string' ? JSON.parse(docsJson) : docsJson;
            } catch (e) {
                if (typeof window.toast === 'function') window.toast('Data invoice tidak terbaca.', 'error');
                return;
            }
            const doc = (docs || [])[index];
            if (!doc) {
                if (typeof window.toast === 'function') window.toast('Data baris tidak ditemukan.', 'error');
                return;
            }

            const wa = window.waPhone(doc.wa);
            if (!wa) {
                if (typeof window.toast === 'function') window.toast('Nomor WhatsApp baris ini kosong atau tidak valid. Isi dulu di Rekapan.', 'error');
                return;
            }

            if (btn) { btn.dataset.busy = '1'; btn.setAttribute('aria-busy', 'true'); }
            let node = null;
            try {
                const { default: html2canvas } = await import('html2canvas');

                node = buildNode(doc);
                document.body.appendChild(node);

                const canvas = await html2canvas(node, { backgroundColor: '#FFFFFF', scale: 3, logging: false });
                const blob = await new Promise((res) => canvas.toBlob(res, 'image/png'));
                if (!blob) throw new Error('render gagal');

                const file = new File([blob], `invoice-rekap-${doc.row}.png`, { type: 'image/png' });
                const shareText = `Halo ${doc.customer_name}, ini invoice #${doc.row} (${doc.number}). Total ${doc.total}, deposit ${doc.dp}, sisa ${doc.sisa}. Terima kasih.`;
                const waTarget = `https://wa.me/${wa}`;

                if (navigator.canShare && navigator.canShare({ files: [file] })) {
                    try {
                        await navigator.share({ files: [file], text: shareText });
                    } catch (err) {
                        // Pengguna membatalkan sheet = bukan kegagalan.
                        if (err && err.name !== 'AbortError' && typeof window.toast === 'function') {
                            window.toast('Gagal membagikan invoice. Coba lagi.', 'error');
                        }
                    }
                } else {
                    // Fallback: unduh PNG lalu buka chat WhatsApp (tanpa lampiran).
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `invoice-rekap-${doc.row}.png`;
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    setTimeout(() => URL.revokeObjectURL(url), 4000);
                    window.open(`${waTarget}?text=${encodeURIComponent(shareText)}`, '_blank', 'noopener');
                    if (typeof window.toast === 'function') {
                        window.toast('Browser ini belum bisa lampirkan file. Gambar diunduh, chat WhatsApp dibuka: lampirkan gambarnya manual.', 'info');
                    }
                }
            } catch (e) {
                console.error('[jdRecapInvoice]', e);
                if (typeof window.toast === 'function') window.toast('Gagal membuat gambar invoice. Coba lagi.', 'error');
            } finally {
                if (node && node.parentNode) node.parentNode.removeChild(node);
                if (btn) { btn.dataset.busy = ''; btn.removeAttribute('aria-busy'); }
            }
        };
    })();

    /* ---- reveal on scroll (progressive enhancement) ----
       Elemen `.reveal` muncul halus saat masuk viewport. Kalau
       IntersectionObserver tidak ada atau user minta reduced motion, kita
       tidak menyalakan mode reveal sama sekali: konten tetap tampil normal. */
    (function initReveal() {
        const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (reduce || !('IntersectionObserver' in window)) return;
        document.documentElement.setAttribute('data-reveal', 'on');
        const io = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    io.unobserve(entry.target);
                }
            });
        }, { rootMargin: '0px 0px -8% 0px', threshold: 0.05 });
        const observe = () => document.querySelectorAll('.reveal:not(.is-visible)').forEach((el) => io.observe(el));
        observe();
    })();

    /* ---- PWA: service worker + installability ---- */
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            // Self-heal: a legacy cache-first SW (/service-worker.js) may still
            // control scope "/" on existing clients and serve stale HTML
            // pointing at old build hashes (kills styles + Alpine). Purge it.
            navigator.serviceWorker.getRegistrations().then(function (regs) {
                regs.forEach(function (reg) {
                    if (reg.active && reg.active.scriptURL.indexOf('/service-worker.js') !== -1) {
                        reg.unregister();
                    }
                });
            });
            navigator.serviceWorker.register('/sw.js').catch(function () {});
        });
    }
    let deferredPrompt = null;
    window.addEventListener('beforeinstallprompt', function (e) {
        e.preventDefault();
        deferredPrompt = e;
        window.__pwaReady = true;
        window.dispatchEvent(new CustomEvent('pwa:ready'));
    });
    window.addEventListener('appinstalled', function () { deferredPrompt = null; });
    window.installApp = function () {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then(function () { deferredPrompt = null; });
        }
    };

    document.addEventListener('click', (event) => {
        const button = event.target.closest('[data-invoice-share]');
        if (button) window.jdShareInvoiceImage(button);
    });

    /* ---- Web Push: request permission + register subscription ---- */
    function urlB64ToUint8Array(base64) {
        const padding = '='.repeat((4 - (base64.length % 4)) % 4);
        const raw = atob((base64 + padding).replace(/-/g, '+').replace(/_/g, '/'));
        return Uint8Array.from([].map.call(raw, (c) => c.charCodeAt(0)));
    }

    window.JastipPush = {
        // Returns true when the browser is now subscribed.
        enable: function () {
            if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
                return Promise.resolve(false);
            }
            return Notification.requestPermission().then(function (permission) {
                if (permission !== 'granted') return false;

                return navigator.serviceWorker.ready.then(function (reg) {
                    return fetch('/push/key', { headers: { 'X-Requested-With': 'true' }, credentials: 'same-origin' })
                        .then(function (r) { return r.json(); })
                        .then(function (json) {
                            if (!json.publicKey) return false;
                            return reg.pushManager.subscribe({
                                userVisibleOnly: true,
                                applicationServerKey: urlB64ToUint8Array(json.publicKey),
                            });
                        })
                        .then(function (sub) {
                            if (!sub) return false;
                            return fetch('/push/subscribe', {
                                method: 'POST',
                                credentials: 'same-origin',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrf(),
                                    'X-Requested-With': 'true',
                                },
                                body: JSON.stringify(sub),
                            }).then(function (r) { return r.ok; });
                        });
                });
            }).catch(function () { return false; });
        },
        disable: function () {
            if (!('serviceWorker' in navigator)) return Promise.resolve();
            return navigator.serviceWorker.ready.then(function (reg) {
                return reg.pushManager.getSubscription();
            }).then(function (sub) {
                if (!sub) return;
                var endpoint = sub.endpoint;
                return sub.unsubscribe().then(function () {
                    return fetch('/push/unsubscribe', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf(),
                            'X-Requested-With': 'true',
                        },
                        body: JSON.stringify({ endpoint: endpoint }),
                    });
                });
            }).catch(function () {});
        },
    };
})();

/* Kick off Alpine after all page scripts (including @push('scripts') blocks
   like manualInvoice()) have registered. */
Alpine.start();
