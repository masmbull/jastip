{{-- Tiga warna brand dijadikan variabel CSS supaya picker di Pengaturan benar-benar
     mengubah situs, tanpa perlu mengganti literal hex di lebih dari 100 tempat view. --}}
@php
    $clean = static fn (string $value, string $fallback): string =>
        preg_match('/^#[0-9A-Fa-f]{6}$/', $value) ? $value : $fallback;

    $brandInk = $clean((string) setting('primary_color', '#141414'), '#141414');
    $brandSub = $clean((string) setting('secondary_color', '#64748B'), '#64748B');
    $brandAccent = $clean((string) setting('accent_color', '#FFC800'), '#FFC800');

    $channel = static fn (int $c): float => ($c / 255) <= 0.03928
        ? ($c / 255) / 12.92
        : pow((($c / 255) + 0.055) / 1.055, 2.4);

    $r = (int) hexdec(substr($brandAccent, 1, 2));
    $g = (int) hexdec(substr($brandAccent, 3, 2));
    $b = (int) hexdec(substr($brandAccent, 5, 2));

    // Teks di atas aksen diturunkan dari luminansnya, bukan ditulis tetap.
    // Titik 0.179 adalah persilangan kurva hitam/putih; memakai hitam murni
    // supaya salah satu dari keduanya selalu lolos 4.5:1 di semua aksen (R-25).
    $luminance = 0.2126 * $channel($r) + 0.7152 * $channel($g) + 0.0722 * $channel($b);
    $accentInk = $luminance > 0.179 ? '#000000' : '#FFFFFF';

    // Tint hover aksen adalah aksen yang dicairkan ke putih, jadi ikut berubah
    // saat aksen diganti dan tidak meninggalkan sisa warna kuning lama.
    $accentTint = sprintf(
        '#%02X%02X%02X',
        (int) round($r * 0.16 + 255 * 0.84),
        (int) round($g * 0.16 + 255 * 0.84),
        (int) round($b * 0.16 + 255 * 0.84),
    );
@endphp
<style>
    :root {
        --nb-brand-ink: {{ $brandInk }};
        --nb-brand-sub: {{ $brandSub }};
        --nb-brand-accent: {{ $brandAccent }};
        --nb-brand-accent-ink: {{ $accentInk }};
        --nb-brand-accent-tint: {{ $accentTint }};
    }
</style>
