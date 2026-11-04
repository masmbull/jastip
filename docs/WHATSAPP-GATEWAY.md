# Kirim Invoice ke WhatsApp (Gateway Self-Hosted)

Fitur ini mengirim invoice sebagai **gambar PNG** langsung ke nomor pelanggan,
satu klik dari halaman admin. Server yang membuat gambar dan mengirimnya, jadi
admin **tidak perlu** memilih kontak atau melampirkan file.

## Penting: ini BUKAN API resmi WhatsApp

Gateway yang dipakai ([OpenWA](https://github.com/rmyndharis/OpenWA) dan yang
sejenis) menghubungkan nomor lewat **WhatsApp Web**, bukan WhatsApp Business
Cloud API. Konsekuensinya:

- **Risiko nomor diblokir selalu ada.** Jangan pakai nomor utama yang dipakai
  chat pelanggan sehari-hari.
- **Pesan pertama ke kontak baru kadang tidak terkirim** (dibuang WhatsApp
  diam-diam). Paling aman untuk pelanggan yang sudah pernah chat ke Anda.
- Session harus tetap hidup; kalau HP/logout, kirim akan gagal dan admin
  mendapat pesan error (tidak diklaim sukses).

Kalau butuh yang resmi dan andal untuk kontak baru, pakai WhatsApp Cloud API
(berbayar per pesan, perlu verifikasi Meta).

## Yang dibutuhkan di server

1. **Docker** (untuk menjalankan gateway).
2. **Chrome/Edge headless** untuk merender gambar invoice.
   - Debian/Ubuntu: `apt install chromium` (atau `chromium-browser`).
   - Kalau lokasinya tidak standar, set `INVOICE_BROWSER_PATH` di `.env`.
   - Cek dengan: `php artisan tinker --execute="echo App\Services\InvoiceImageRenderer::fromConfig()->browser();"`

## 1. Jalankan gateway

```bash
docker run -d --name openwa \
  -p 2785:3000 \
  -v openwa-data:/app/data \
  --restart unless-stopped \
  rmyndharis/openwa:latest
```

Buka dashboard `http://IP-VPS:2785`, buat **API key** (role admin) dan **session**
baru. Simpan API key dan Session ID (berbentuk UUID).

## 2. Scan QR

Di dashboard, start session lalu scan QR dari WhatsApp di HP:

WhatsApp → Pengaturan → **Perangkat tertaut** → Tautkan perangkat.

Tunggu status menjadi **ready**.

## 3. Isi di Pengaturan aplikasi

Masuk admin → **Pengaturan** → kartu **Gateway WhatsApp**:

| Field | Isi |
|---|---|
| URL Gateway | `http://IP-VPS:2785` |
| API Key | API key dari dashboard gateway |
| Session ID | UUID session yang sudah ready |

Simpan. Badge di kartu itu harus berubah menjadi **Terhubung** beserta nomor
yang dipakai gateway.

> API key disimpan sebagai setting **tidak publik** dan tidak pernah
> ditampilkan ulang. Kosongkan field-nya kalau tidak ingin mengubah key.

## 4. Pakai

Buka **Admin → Invoice**, klik **Kirim WA** pada baris invoice. Hasilnya:

- Invoice terkirim sebagai gambar + caption berisi total, sudah dibayar, sisa,
  dan rekening.
- Kolom status menampilkan **WA terkirim** (dengan waktu) atau **WA gagal**.
- Invoice yang sudah pernah terkirim akan meminta konfirmasi sebelum kirim ulang.

## Kalau gagal

Pesan error ditampilkan langsung dan disimpan di kolom `orders.wa_invoice_error`:

| Pesan | Artinya |
|---|---|
| Gateway WhatsApp belum diatur | Isi URL/key/session di Pengaturan |
| Sesi WhatsApp menunggu scan QR | Scan QR di dashboard gateway |
| Sesi WhatsApp terputus | Hubungkan ulang session |
| API key gateway ditolak | API key salah atau dicabut |
| Sesi WhatsApp tidak ditemukan | Session ID salah |
| Server belum punya Chrome/Edge headless | Pasang Chromium di server |
| Nomor WhatsApp pelanggan kosong atau tidak valid | Perbaiki nomor di pesanan |

## Alternatif tanpa gateway

Tombol share manual (ikon WhatsApp) tetap ada di daftar Invoice: merender PNG di
browser admin lalu membuka share sheet / menu WhatsApp. Berguna kalau gateway
sedang mati. Di jalur ini admin masih memilih kontak sendiri.
