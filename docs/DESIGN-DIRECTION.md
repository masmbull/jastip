# Arah Desain

Dokumen ini mencatat hasil riset referensi dan **signature** yang kita pakai,
supaya arah visual tidak liar dan tidak meniru proyek lain.

## Referensi yang dipelajari

1. **neobrutalism.dev** (repo `ekmas/neobrutalism-components`) — acuan gaya
   neo-brutalism: border tebal, hard shadow, radius kecil, warna aksen pekat.
2. **jastipify.biz.id** — app manajemen jastip (Laravel, login via Google).
   Hasil ekstraksi Playwright (halaman publik, login/register/forgot):

   | Aspek | Nilai |
   | --- | --- |
   | Font | `Poppins` (heading weight 800) |
   | Latar | `#FFF8F9` (pink-putih hangat) |
   | Tinta | `#0F172A` |
   | Aksen | `#FD9602` (oranye) |
   | Permukaan | putih `#FFFFFF`, abu `#F7F8FA` |
   | Muted | `#94A3B8`, `#64748B` |
   | Pola auth | **split panel**: kiri panel marketing, kanan form |
   | Gaya | SaaS bersih, rounded, shadow lembut, judul gradient |

   Catatan: halaman dalam (dashboard, order, dsb) **tidak bisa diambil** karena
   login hanya lewat Google OAuth. Jadi inspirasinya hanya permukaan publik.

## Signature kita (jangan disamakan dengan referensi)

Kita **tidak** meniru Poppins / pink-putih / oranye `#FD9602`. Identitas kita:

- **Neo-brutalism**: border 2px tinta `#141414`, hard shadow `4px 4px 0`,
  radius kecil-konsisten, tanpa shadow lembut.
- **Font**: `DM Sans` (400/500/700/900). Bukan Poppins.
- **Kertas hangat** `#FAF6EF`, kartu putih, **tinta** `#141414`.
- **Aksen** dari Pengaturan brand (default kuning `#FFC800`), dipasangkan teks
  kontras otomatis.
- **Tint warna-warni** untuk blok/kartu: kuning `#FACC00`, oranye `#FF9F43`,
  koral `#FF4D50`, pink `#FF90E8`, ungu `#7A83FF`, biru `#5294FF`,
  hijau `#05E17A`.
- **Tanda khas**: elemen "sticker" (blok tinta + teks kertas), garis pembatas
  tebal, angka selalu rata kanan (`tabular-nums`), label huruf kapital
  ber-tracking lebar.

## Yang kita pinjam (struktur, bukan tampilan)

- Halaman auth **split panel**: sisi kiri pesan/nilai jual + sisi kanan form.
- Hierarki judul yang jelas dan satu kalimat penjelas di bawahnya.
- Titik kepercayaan (trust bullets) di panel marketing.
- Layout landing berseksi dengan heading tiap bagian.

Semua itu diterapkan dengan gaya neo-brutalist kita sendiri, bukan gaya SaaS
rounded milik referensi.
