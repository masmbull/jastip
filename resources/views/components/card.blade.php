{{--
  Kartu brutalist: border 2px tinta + hard shadow. Satu motif di semua permukaan.

  `tone` opsional buat RAINBOW MODE: sun | sand | coral | bubble | grape |
  sky | mint. Dibiarkan kosong berarti kartu putih biasa, kecuali induknya
  pakai class `nb-rainbow` (lalu warnanya diacak siklus otomatis).
--}}
@props(['padded' => true, 'tone' => null])

@php($tones = ['sun', 'sand', 'coral', 'bubble', 'grape', 'sky', 'mint'])

<div {{ $attributes->merge([
        'class' => 'nb-card transition-colors'
            . ($padded ? ' p-5 sm:p-6' : '')
            . (in_array($tone, $tones, true) ? ' nb-tint-' . $tone : ''),
    ]) }}>
    {{ $slot }}
</div>
