@php
    $whatsapp = setting('whatsapp', '6285123456789');
    $brandName = setting('brand_name', 'NITIP DI END');
@endphp

<div class="fixed bottom-6 right-6 z-50">
    <a href="{{ whatsapp_url($whatsapp, 'Halo Kak, saya tertarik untuk nitip di ' . $brandName) }}"
       target="_blank" rel="noopener noreferrer"
       class="flex items-center justify-center w-14 h-14 bg-green-500 hover:bg-green-600 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-200 group">
        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
            <path d="M20.52 3.48A11.94 11.94 0 0012 0 11.96 11.96 0 00.01 12c0 2.11.55 4.16 1.61 6.01L0 24l6.15-1.61a12.06 12.06 0 005.85 1.51c6.62 0 12-5.38 12-12 0-3.21-1.25-6.21-3.48-8.42zM12 21.4c-1.87 0-3.7-.51-5.25-1.48l-.38-.23-3.66.96 1-2.89A9.93 9.93 0 01.94 12c0-5.52 4.48-10 10-10s10 4.48 10 10-4.48 10-10 10zm5.53-7.46c-.3-.29-1.74-.95-3.25-.46-.02.01-2.1.37-3.84 1.12-.38.13-.68.2-.99.19-2.52-.41-4.46-3-4.46-3-.62-1.14-1.2-2.25-1.63-3.48-.29-.84-.32-1.26-.34-1.36 0-.11-.02-.15-.1-.24-.07-.08-1.23-.37-1.23-.37s.85-.26 1.85-1.11c.64-.52 1.4-.87 2.15-1.11 1.06-.33 2.24-.22 3.14.03 1.16.3 2.27 1.09 3.18 1.93 1.11 1.03 1.11 1.72 1.22 2.07.11.34.22 1.02-.09 1.98-.31.96-1.1 3.28-1.94 4.15l-.01-.01z" />
            <path d="M9.5 9.5c0 .5-.4 1-1 1s-1-.5-1-1 .4-1 1-1 1 .5 1 1v1zm0 4.5c0 .5-.4 1-1 1s-1-.5-1-1 .4-1 1-1 1 .5 1 1v1zm4 0c0 .5-.4 1-1 1s-1-.5-1-1 .4-1 1-1 1 .5 1 1v1zm0-4c0 .5-.4 1-1 1s-1-.5-1-1 .4-1 1-1 1 .5 1 1v1z" />
        </svg>
        <span class="absolute opacity-0 group-hover:opacity-100 bg-gray-800 text-white text-xs rounded px-2 py-1 -top-8 -right-2" style="white-space: nowrap">Chat WhatsApp</span>
    </a>
</div>
