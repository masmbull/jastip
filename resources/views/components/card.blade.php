{{--
  Kartu brutalist: border 2px tinta + hard shadow. Satu motif di semua permukaan.
--}}
@props(['padded' => true])

<div {{ $attributes->merge([
        'class' => 'nb-card transition-colors'
            . ($padded ? ' p-5 sm:p-6' : ''),
    ]) }}>
    {{ $slot }}
</div>