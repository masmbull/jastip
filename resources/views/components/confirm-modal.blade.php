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
        <div class="fixed inset-0 z-[300] flex items-center justify-center">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"
                 @click="cancel()"></div>
            <div class="relative bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6 animate-scale-in"
                 @click.stop>
                <h3 class="text-lg font-semibold text-[#1E293B]" x-text="title"></h3>
                <p class="mt-2 text-sm text-[#64748B]" x-text="message"></p>
                <div class="mt-6 flex justify-end gap-3 pt-2">
                    <button type="button"
                            @click="cancel()"
                            class="px-4 py-2 text-sm text-[#64748B] hover:bg-[#F1F5F9] rounded-lg border border-[#E2E8F0]">{{ $cancelLabel }}</button>
                    <button type="button"
                            @click="ok()"
                            class="px-4 py-2 text-sm text-white bg-orange-500 hover:bg-orange-600 rounded-lg shadow-sm">{{ $okLabel }}</button>
                </div>
            </div>
        </div>
    </template>
</div>
