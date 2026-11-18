{{--
  Tombol pasang aplikasi (PWA). Muncul hanya bila instalasi memungkinkan:
  Chromium (beforeinstallprompt) atau Safari iOS (petunjuk manual).
--}}
@props([
    'context' => 'store', // store | admin
])

@php
    $isAdmin = $context === 'admin';
    $title = $isAdmin ? 'Pasang panel admin' : 'Pasang sebagai aplikasi';
    $chromiumCopy = $isAdmin
        ? 'Akses pesanan, ongkir, dan bukti bayar dari layar utama iPhone/Android.'
        : 'Buka toko lebih cepat dari layar utama, tanpa unduh dari store.';
    $dismissKey = $isAdmin ? 'pwa-admin-dismissed' : 'pwa-dismissed';
@endphp

<div x-data="{
        show: false,
        mode: 'chromium',
        dismissKey: @js($dismissKey),
        storeGet(k) {
            try {
                const v = localStorage.getItem(k);
                if (v !== null) return v;
            } catch (e) {}
            const m = document.cookie.match(new RegExp('(?:^|; )' + k + '=([^;]*)'));
            return m ? decodeURIComponent(m[1]) : null;
        },
        storeSet(k, v) {
            try { localStorage.setItem(k, v); return; } catch (e) {}
            try { document.cookie = k + '=' + encodeURIComponent(v) + '; path=/; max-age=31536000'; } catch (e) {}
        },
        init() {
            try {
                const standalone = window.matchMedia('(display-mode: standalone)').matches
                    || window.navigator.standalone === true;
                if (standalone) return;
                if (this.storeGet(this.dismissKey) === '1') return;
                const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent || '');
                if (window.__pwaReady) {
                    this.mode = 'chromium';
                    this.show = true;
                } else if (isIOS) {
                    this.mode = 'ios';
                    this.show = true;
                } else {
                    window.addEventListener('pwa:ready', () => {
                        if (this.storeGet(this.dismissKey) === '1') return;
                        this.mode = 'chromium';
                        this.show = true;
                    }, { once: true });
                }
                window.addEventListener('appinstalled', () => { this.show = false; });
            } catch (e) { this.show = false; }
        },
        install() {
            try { if (typeof window.installApp === 'function') window.installApp(); } catch (e) {}
            this.show = false;
        },
        dismiss() {
            this.show = false;
            this.storeSet(this.dismissKey, '1');
        },
     }"
     x-show="show" x-cloak x-transition
     @keydown.escape.window="dismiss()"
     class="fixed left-3 bottom-3 z-[450] max-w-[calc(100vw-1.5rem)] sm:left-6 sm:bottom-6 sm:max-w-sm">
    <div class="flex items-start gap-3 rounded-[12px] border-2 border-[#141414] bg-white p-4 dark:border-[#FAF6EF] dark:bg-[#1E1E1E]" style="box-shadow: 4px 4px 0 #141414">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-[8px] border-2 border-[#141414] bg-[#FFC800] text-[#141414]">
            <x-icon name="download" class="h-5 w-5" />
        </div>
        <div class="min-w-0 text-sm">
            <p class="font-black text-[#141414] dark:text-[#FAF6EF]">{{ $title }}</p>
            <template x-if="mode === 'chromium'">
                <p class="mt-0.5 font-medium text-[#475569] dark:text-[#D6D3D1]">{{ $chromiumCopy }}</p>
            </template>
            <template x-if="mode === 'ios'">
                <p class="mt-0.5 font-medium text-[#475569] dark:text-[#D6D3D1]">
                    Ketuk <span class="font-black">Bagikan</span> lalu <span class="font-black">Tambah ke Layar Utama</span>.
                </p>
            </template>
            <div class="mt-2.5 flex items-center gap-2" x-show="mode === 'chromium'">
                <button type="button" @click.stop="install()" class="nb-btn bg-[#FFC800] px-4 py-1.5 text-sm text-[#141414]">Pasang</button>
                <button type="button" @click.stop="dismiss()" class="min-h-[44px] px-3 py-1.5 text-sm font-bold text-[#475569]">Nanti</button>
            </div>
        </div>
        <button type="button" @click.stop="dismiss()" class="-m-1 inline-flex h-11 w-11 shrink-0 items-center justify-center" aria-label="Tutup">
            <x-icon name="x" class="h-4 w-4" />
        </button>
    </div>
</div>
