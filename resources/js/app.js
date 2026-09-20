/*
 | Nitip UI — shared: top progress, async forms (data-async),
 | confirm bridge (data-confirm), client toasts, cart badge. Vanilla JS.
 |--------------------------------------------------------------------------
 */
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
        if (msg) {
            confirmAction(msg).then((ok) => { if (ok) submitForm(form); });
        } else {
            submitForm(form);
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

/* ---- PWA: service worker + installability ---- */
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js').catch(function () {});
        });
    }
    let deferredPrompt = null;
    window.addEventListener('beforeinstallprompt', function (e) {
        e.preventDefault();
        deferredPrompt = e;
        window.dispatchEvent(new CustomEvent('pwa:ready'));
    });
    window.addEventListener('appinstalled', function () { deferredPrompt = null; });
    window.installApp = function () {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then(function () { deferredPrompt = null; });
        }
    };
})();

