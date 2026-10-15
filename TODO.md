# TODO JASTIP
Daftar kerja aktif. Coret yang sudah kelar dan tinggalkan catatannya, jangan dihapus.

## Baru masuk

- [ ] **RAINBOW MODE** — tema warna-warni ceria, gaya "pelangi terstruktur"
  (geometri brutalist tetap, cuma warna isinya yang pelangi).
  Sudah kelar:
  - Palet Tailwind dilepas dari pemadatan biru tunggal — `green-500`
    tombol WhatsApp akhirnya benar-benar hijau, `red-500` error jadi merah.
  - Token 7 warna blok (`--nb-sun` … `--nb-mint`) + utilitas `.nb-rainbow`
    (siklus otomatis di grid) dan `.nb-tint-<warna>` (manual).
  - Prop `tone` di `<x-card>` dan `<x-stat-card>`.
  - Sidebar admin: strip pelangi + tiap kelompok menu punya warna sendiri.
  - Grid: dashboard, ringkasan Rekapan, produk & kategori (depan + list).
  Belum: halaman checkout, profil, dan halaman statis. Catatan: halaman-halaman
  itu masih pakai kartu gaya lama (`bg-white rounded-xl shadow_sm` cards, bukan
  `nb-card`), jadi urutannya harus diconvert ke brutalist dulu — kalau
  `nb-rainbow` dipasang duluan hasilnya kotak warna datar tanpa garis tinta.

## Bug ketemu & diperbaiki

- [x] `this.$on(...)` di `_manual-form-script.blade.php` — itu API Alpine v2.
  Di v3 bikin `init()` meledak, sehingga ongkir otomatis tidak termuat dan
  tombol "Buat Invoice Lunas/Deposit" tidak mengubah mode sama sekali.
  Diganti listener `window` + dilepas di `destroy()`.

- [x] **Nomor WhatsApp invoice tidak terdaftar** (lapor: "pas kirim invoice
  lewat WA, nomor ga terdaftar"). Penyebab: link invoice dibuat inline
  `wa.me/{{ preg_replace('/[^0-9]/','',$nomor) }}` TANPA konversi awalan `0`
  ke `62`. Ditambah helper tunggal `wa_phone()` (menangani `08…`, `+62…`,
  `62…`, `8…`, spasi/tanda hubung/kurung) dan semua pemakaian `wa.me`
  (invoice list, cetak invoice, detail pesanan, bukti bayar, status pesanan,
  halaman kontak) dialihkan ke `whatsapp_url()`/`wa_phone()`.

- [x] **Halaman Pelanggan HTTP 500** — `admin/users/index.blade.php` punya
  ekspresi Blade rusak `route('admin.users.show', $user)}` (kurang `)`),
  kelas `dark.text-…`/`dark.bg-…` (titik, bukan titik dua), markup modal
  kacau, dan modal tanpa `<form>`/tanpa JS buka-tutup. Halaman ditulis ulang.

- [x] **Tambah Pelanggan benar-benar berfungsi** (sebelumnya UI doang).
  Route `POST /admin/user` (`admin.users.store`) + `UserController@store`
  membuat User (role customer, username unik dari email, password acak) dan
  melengkapi UserProfile (telepon, tier, poin). Profil dibuat otomatis oleh
  `UserObserver`, jadi store pakai `updateOrCreate` (hindari duplikat
  `user_profiles.user_id`). Modal Alpine + validasi + tampil error.

- [x] **Warna bertabrakan (UI berantakan)** — app.css punya 3 palet tumpang
  tindih: hex literal + `html.dark .bg-white`, patch `[class*="bg-[#...]"]`
  dengan `--dt-*` (biru-slate #111827), dan neo-brutalist `--nb-*` (netral
  #121212). Plus aksen bentrok: cyan `#0E7490`/`#0891B2` (127 tempat) vs
  aksen kuning. Perbaikan:
  - `--dt-*` disetel netral & senada `--nb-*`; blok dark lama yang duplikat
    dibuang; patch `[class*="bg-[#2e323b]/#404854/#1E1E1E/...]` ditambah
    supaya ~500 hex biru-slate di view ikut jadi netral.
  - cyan dipetakan ke `var(--nb-brand-accent)` (latar aksi, teks pasangan
    kontras) dan `var(--nb-brand-ink)` (teks/ikon/garis) lewat CSS, jadi
    brand tunggal tanpa edit 127 tempat.
  - `--nb-brand-ink`/`--nb-brand-sub` ikut dibalik di mode gelap (ini yang
    bikin teks #141414 menghilang di atas kartu #1E1E1E).
  - tint pelangi dapat varian gelap; focus ring disatukan jadi satu.

- [x] **Kirim invoice ke WhatsApp sebagai PDF.** Tombol WhatsApp di tiap baris
  daftar Invoice membuka chat WhatsApp berisi **link PDF invoice** (bertanda
  tangan, berlaku 30 hari, tanpa login) + pesan. Pelanggan klik link → PDF
  langsung terbuka/tersimpan. Tidak ada sheet berbagi dan tidak ada langkah
  unduh untuk admin.
  - PDF dihasilkan server dengan `dompdf/dompdf` (template table-based +
    CSS inline; dompdf tidak mendukung flex/grid).
  - Route publik `invoice.pdf` memakai middleware `signed` supaya link tak
    bisa ditebak; data pelanggan tidak terbuka untuk umum.
  - Pendekatan lama (html2canvas + Web Share sheet / PNG unduh) DIHAPUS.

- [ ] Catatan: auto-attach tanpa buka WhatsApp sama sekali hanya mungkin lewat
  WhatsApp Cloud API (butuh akun Meta Business + nomor + token). Belum dipakai.

## Tema neo-brutalism (warna + font)

Referensi global yang dipakai: **neobrutalism.dev** (repo `ekmas/neobrutalism-components`)
dan palet Gumroad. Yang diadopsi:

- Palet aksen warna-warni (menggantikan tint pastel yang kusam):
  kuning `#FACC00`, oranye `#FF9F43`, koral `#FF4D50`, pink `#FF90E8`,
  ungu `#7A83FF`, biru `#5294FF`, hijau `#05E17A`. Semua lolos kontras AA
  dengan tinta `#141414` di atasnya.
- Tinta `#141414` + kertas `#FAF6EF` + kartu putih, border 2px + hard shadow
  `4px 4px 0` (sudah ada, dipertahankan).
- **Font diganti total**: `Instrument Sans` -> **DM Sans** (400/500/700/900),
  self-host lewat plugin Bunny di `vite.config.js`.
- Tint pelangi dapat varian mode gelap (dulu tidak ada, ini salah satu
  penyebab "nabrak" saat tema gelap: pastel terang + teks krem).

Catatan: neobrutalism.dev aslinya light-only (tanpa mode gelap). App ini tetap
menyediakan mode gelap, tapi kini diturunkan dari token yang sama supaya tidak
bentrok.

## Tombol Kirim Invoice via WhatsApp di halaman Rekapan

- [x] Tombol **Kirim** per baris di `admin/recaps/show` (kolom "Aksi").
  Alur: data baris diubah jadi dokumen invoice **di server**
  (`Recap::invoiceForRow()`, pakai aturan kolom yang sama dengan `totals()`
  supaya angka konsisten) -> di-embed sebagai JSON -> JS membangun invoice
  (DOM + warna hex inline, hindari `oklch` Tailwind v4) -> `html2canvas`
  render PNG (lazy chunk) -> **Web Share API** (`navigator.canShare({files})`)
  agar sheet Android muncul dan pengguna pilih WhatsApp.
- [x] Fallback bila file sharing tak didukung: unduh PNG + buka chat `wa.me`
  (tanpa lampiran), dengan pesan jelas bahwa gambar harus dilampirkan manual.
- [x] Normalisasi nomor Indonesia di JS (`waPhone()`: 08.. / +62.. / 62.. / 8..),
  validasi nomor kosong/tidak valid, guard double-click, loading state,
  pembatalan sheet (AbortError) dianggap bukan kegagalan.
- [x] Tidak ada API berbayar / WhatsApp Business API / automation. Tidak
  mengklaim terkirim otomatis: pengiriman tetap butuh aksi pengguna di WhatsApp.
- [x] Test: `RecapTest` (dok invoice per baris + total/DP/sisa/status).

## Sisa TODO

- [x] **Sweep warna (sebagian besar) + verifikasi visual.** Cara kerja: jalan
  app lokal + screenshot Playwright/Chromium (admin+frontend), perbaiki, ulang.
  Yang diperbaiki:
  - sisa cyan/teal (`#0E7490`, `#0891B2`, `#155E75`, `#083344`, `#67e8f9`,
    `#22D3EE`) dipetakan ke aksen+pasangan tinta / tinta (lewat CSS, jadi
    ratusan tempat ikut beres): tombol, link, garis.
  - permukaan biru-slate & biru muda (kotak info, body auth lama) -> netral.
  - sidebar admin: item aktif sekarang SATU aksen (kuning + tinta + shadow),
    label kelompok netral. Dulu tiap kelompok beda warna (merah/kuning/hijau/
    biru) sehingga sidebar terasa "nabrak".
  - strip pelangi 7 warna (sidebar + footer) -> satu garis aksen kuning.
  - **KUNCI**: `.nb-rainbow > *` tidak lagi mengisi kartu dengan 7 warna penuh
    (itu penyebab utama "berantakan"), melainkan strip aksen tipis di atas
    kartu; badan kartu tetap netral. Efeknya kelihatan di 15 tempat sekaligus.
    Kartu ringkasan Invoice "Sisa" dan kartu trust di hero juga diseragamkan.
- [x] **Sisa halaman checkout & profil (brutalist)** — kartu-kartunya ternyata
  sudah `nb-card` (ganti `x-card` dulu ke brutalist), yang ketinggalan adalah
  **input** bergaya lama (`border-[#E2E8F0] bg-white rounded-lg`). Semua input di
  `checkout` + `profile` (address-form, edit, settings, chat) diganti `nb-input`;
  atribut `@error(...)` dipertahankan. Diverifikasi visual (screenshot) +
  axe-core 0 pelanggaran di 8 halaman profil.

- [ ] Landmark/ARIA audit menyeluruh — **SUDAH DIKERJAKAN** (lihat catatan di
  atas: axe-core 20 halaman + 8 halaman profil, 0 pelanggaran). Baris ini
  tinggal penanda.

Catatan alur: verifikasi visual pakai Playwright (chromium) di server lokal
`artisan serve`; halaman admin login `admin` / `admin123` (seeder).


## Riset referensi eksternal
- [x] Scrape **jastipify.biz.id** (Playwright/Chromium). Login situs hanya via
  Google OAuth, jadi halaman dalam tidak bisa diambil; yang terekam hanya
  permukaan publik (landing, register, forgot-password). Hasil ekstraksi:
  font `Poppins`, latar `#FFF8F9`, tinta `#0F172A`, aksen `#FD9602`, halaman
  auth berupa **split panel** (panel marketing + form).
- [x] Putuskan **signature kita sendiri** (tidak meniru): tetap neo-brutalism,
  `DM Sans`, kertas `#FAF6EF`, tinta `#141414`, aksen dari Pengaturan brand,
  tint warna-warni. Struktur split panel dipinjam, tampilan tidak.
  Detail: `docs/DESIGN-DIRECTION.md`.
- [x] Terapkan split panel neo-brutalist di `layouts/auth` + `auth/login` +
  `auth/register`.
- [ ] Sisa: sweep ~500 hex ad-hoc di view ke token + audit kontras menyeluruh.

- [ ] **Catatan: perubahan warna di-REVERT.** Upaya menyatukan palet di
  `app.css` (cyan -> aksen kuning, remap ~500 hex biru-slate) ternyata bikin
  tampilan "unexpected". `app.css` dikembalikan ke versi sebelum perubahan itu;
  fitur foto invoice WhatsApp tetap dipertahankan. Perbaikan warna akan
  dikerjakan ulang dengan pendekatan yang tidak mengubah warna yang sudah
  benar-benar dipakai.

## Sudah kelar

- [x] Warna brand di Pengaturan → CSS variable (`--nb-brand-*`), sweep kontras 4.096 warna lulus.
- [x] Saklar `qris_enabled` benar-benar berpengaruh (satu pintu `qris_enabled()`/`qris_url()`).
- [x] Footer invoice: frasa "sah tanpa tanda tangan basah" dihapus.
- [x] DP → Deposit di seluruh label (kolom DB `down_payment` sengaja tidak disentuh).
- [x] Checklist "Munculkan Fee" di form/modal invoice.
- [x] Edit invoice: rute `admin.orders.edit` / `admin.orders.update` + tombol di Rekapan & halaman pesanan.
- [x] Dua mode invoice (Deposit / Lunas) + judul cetak "TAGIHAN DP" vs "INVOICE".
- [x] Tombol WhatsApp di list invoice (sudah ada sejak awal).
- [x] Judul halaman login tidak lagi kosong.
- [x] **WhatsApp number format** — lihat "Bug ketemu & diperbaiki": helper `wa_phone()` baru, semua link `wa.me` memakai `whatsapp_url()`.
- [x] **Merge "Daftar Layanan" + Status Sistem** → gabung ke halaman Status Sistem.
  Nav item "Daftar Layanan" dihapus, route `/admin/services` dipindaharah ke
  `admin.system.status`, `$monitorServices` di-load dari tabel `Service`,
  "Cek Semua" button + "Layanan Terpantau" table di-status page, `check()`
  return redirect back (fix raw JSON UX bug), `checkAll()` redirect ke `system.status`.
- [x] **Button + Pop-up Modal Daftar Pelanggan** — berfungsi penuh sekarang
  (lihat "Bug ketemu & diperbaiki"). Field: nama, email, telepon, tier, poin.

## Antrean

- [ ] **QRIS** — Setelah admin upload QRIS lewat Pengaturan → payment page 
  masih nggak muncul. **Suspek utama**: `setting()` cache 1 jam, `Setting::set()` 
  baru lupa cache; `config/filesystems.php` default disk `local` tapi filenya 
  dipakai disk `public`; dan/atau checkbox `qris_enabled` lagi mati. 
  Butuh verifikasi manual di production server (check: `public/storage` symlink,
  `Setting::set()` dipakai di settings save controller, cek checkbox `qris_enabled`).

- [ ] **Brutalist-convert static pages** (about, cara-nitip, contact) dari
  Tailwind `x-card` ke `nb-card` (2px ink border, hard shadow, paper `#FAF6EF`).
  - `about.blade` ✅ sudah dikonversi ke nb-card + tint `#FFC800` + ink `#141414`.
  - `cara-nitip.blade` ✅ dikonversi ke nb-card + tint `#FFC800` + ink `#141414`.
  - `contact.blade` ✅ dikonversi ke nb-card + struktur form kustom + ink `#141414`.

- [x] **Kecamatan/kelurahan**: migrasi `orders.district` + `orders.village`,
  `WilayahService` (baca `database/data/wilayah.json`, di-cache), endpoint
  `GET /admin/pesanans/wilayah`, dropdown bertingkat (kota -> kecamatan ->
  kelurahan) di `_manual-form.blade.php`, persist di store/update, test
  `WilayahTest`.

- [x] **Hapus semua produk dummy** — seeder produk/order (dan viral) sekarang
  HANYA jalan di env `local`/`testing`. Deployment produksi tidak lagi membuat
  katalog contoh; bini lo isi sendiri. (Seeder tetap dipakai test.)

- [x] **Modal tambah produk**: sudah ada halaman penuh
  `admin/products/create` (field lengkap + upload foto + status Ready/PO),
  dijangkau dari tombol "Tambah Produk". Tidak diubah ke modal karena halaman
  penuh lebih nyaman untuk banyak field.

- [x] **QRIS tidak muncul (bug nyata)** — `qris_url()` memakai `Storage::url()`
  polos (mengikuti disk default, di `.env` = `local`), padahal file diunggah ke
  disk `public`. URL jadi salah -> gambar QRIS tidak tampil. Diperbaiki ke
  `Storage::disk('public')->url()`, plus pemakaian serupa di
  `payment-proofs/show`. Terverifikasi lokal: URL jadi `/storage/settings/...`.

- [ ] **Full suite test + `npm run build`** sebelum rilis final.

- [ ] **Commit + push + deployment ke server**:
  `git pull`, `php artisan migrate --force`, `php artisan optimize:clear`,
  `php artisan optimize`, `sudo systemctl reload php8.3-fpm`.

- [ ] `php artisan test` full suite hijau.
- [ ] `npm run build`.
- [ ] Commit + push, lalu di server: `git pull`,
  `php artisan migrate --force`, `php artisan optimize:clear`,
  `php artisan optimize`, `sudo systemctl reload php8.3-fpm`.

## Rilis

- [ ] `php artisan test` full suite hijau.
- [ ] `npm run build`.
- [ ] Commit + push, lalu di server: `git pull`,
  `php artisan migrate --force`, `php artisan optimize:clear`,
  `php artisan optimize`, `sudo systemctl reload php8.3-fpm`.