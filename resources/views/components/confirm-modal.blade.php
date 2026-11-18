@props([
    'title'       => 'Konfirmasi',
    'message'     => 'Apakah kamu yakin ingin melanjutkan?',
    'okLabel'     => 'Ya, lanjutkan',
    'cancelLabel' => 'Batal',
])

@php
    // Alpine data init; values are safely encoded into the JSON args.
    $t = addslashes((string) $title);
    $m = addslashes((string) $message);
    $ok = addslashes((string) $okLabel);
@endphp

<div x-data="jdConfirm('{{ $t }}','{{ $m }}','{{ $ok }}')"
     @jd-confirm.window="ask($event.detail)"
     @keydown.escape.window="open ? cancel() : null"
     x-cloak>
    <template x-if="open">
        <div class="fixed inset-0 z-[300] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50"
                 @click="cancel()"></div>
            <div class="relative w-full max-w-md rounded-[10px] border-2 border-[#141414] bg-[#FAF6EF] p-6 shadow-[6px_6px_0_#141414] animate-scale-in"
                 @click.stop>
                <h3 class="font-display text-lg font-extrabold text-[#141414]" x-text="title"></h3>
                <p class="mt-2 text-sm font-medium text-[#475569]" x-text="message"></p>
                <div class="mt-6 flex justify-end gap-3 pt-2">
                    <button type="button"
                            @click="cancel()"
                            class="nb-btn bg-white px-4 py-2 text-sm text-[#141414]">{{ $cancelLabel }}</button>
                    <button type="button"
                            @click="ok()"
                            class="nb-btn bg-[#FFC800] px-4 py-2 text-sm font-black text-[#141414]">{{ $okLabel }}</button>
                </div>
            </div>
        </div>
    </template>
</div>
