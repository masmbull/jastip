{{--
  Tombol pasang aplikasi (PWA). Muncul hanya bila instalasi memungkinkan:
  Chromium (event beforeinstallprompt) atau Safari iOS (petunjuk manual).
  Sembunyi bila sudah standalone, sudah dipasang, atau sudah ditutup.
--}}
<div x-data="{
        show: false,
        mode: 'chromium',
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
                if (this.storeGet('pwa-dismissed') === '1') return;
                const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent || '');
                if (window.__pwaReady) {
                    this.mode = 'chromium';
                    this.show = true;
                } else if (isIOS) {
                    this.mode = 'ios';
                    this.show = true;
                } else {
                    window.addEventListener('pwa:ready', () => {
                        if (this.storeGet('pwa-dismissed') === '1') return;
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
            this.storeSet('pwa-dismissed', '1');
        },
     }"
     x-show="show" x-cloak x-transition
     @keydown.escape.window="dismiss()"
     class="fixed left-3 bottom-3 sm:left-6 sm:bottom-6 z-[450] max-w-[calc(100vw-1.5rem)] sm:max-w-sm">
    <div class="bg-white dark:bg-[#1E1E1E] border-2 border-[#141414] dark:border-[#FAF6EF] rounded-[12px] p-4 flex items-start gap-3" style="box-shadow: 4px 4px 0 #141414">
        <div class="shrink-0 w-10 h-10 bg-[#FFC800] border-2 border-[#141414] rounded-[8px] flex items-center justify-center text-[#141414]">
            <x-icon name="download" class="w-5 h-5" />
        </div>
        <div class="text-sm min-w-0">
            <p class="font-black">Pasang sebagai aplikasi</p>
            <template x-if="mode === 'chromium'">
                <p class="font-medium text-[#475569] dark:text-[#D6D3D1] mt-0.5">Buka toko lebih cepat dari layar utama, tanpa unduh dari store.</p>
            </template>
            <template x-if="mode === 'ios'">
                <p class="font-medium text-[#475569] dark:text-[#D6D3D1] mt-0.5">Ketuk <span class="font-black">Bagikan</span> lalu <span class="font-black">Tambah ke Layar Utama</span>.</p>
            </template>
            <div class="flex items-center gap-2 mt-2.5" x-show="mode === 'chromium'">
                <button type="button" @click.stop="install()" class="nb-btn px-4 py-1.5 text-sm bg-[#FFC800] text-[#141414]">Pasang</button>
                <button type="button" @click.stop="dismiss()" class="px-3 py-1.5 min-h-[44px] text-sm font-bold text-[#475569]">Nanti</button>
            </div>
        </div>
        <button type="button" @click.stop="dismiss()" class="shrink-0 inline-flex items-center justify-center w-11 h-11 -m-1" aria-label="Tutup">
            <x-icon name="x" class="w-4 h-4" />
        </button>
    </div>
</div>
