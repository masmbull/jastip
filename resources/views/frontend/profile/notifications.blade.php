@extends('layouts.app')

@section('title', 'Notifikasi | NITIP DI END')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#ecfeff] to-[#e0f2fe] dark:from-[#1a1a1a] dark:to-[#23252b] py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">Notifikasi</h1>
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" id="push-toggle"
                        class="min-h-[44px] text-sm px-4 py-2.5 border border-[#E2E8F0] dark:border-[#404854] rounded-lg text-[#1E293B] dark:text-[#cbd5e1] hover:bg-[#F8FAFC] dark:hover:bg-[#2e323b] transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300">
                    Aktifkan notifikasi browser
                </button>
                @if($notifications->total() > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="min-h-[44px] text-sm px-4 py-2.5 border border-[#E2E8F0] dark:border-[#404854] rounded-lg text-[#1E293B] dark:text-[#cbd5e1] hover:bg-[#F8FAFC] dark:hover:bg-[#2e323b] transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300">
                        Tandai semua terbaca
                    </button>
                </form>
                @endif
            </div>
        </div>

        <div class="space-y-3">
            @forelse($notifications as $notification)
            <x-card class="{{ $notification->read_at ? '' : 'border-l-4 border-l-orange-400' }}">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-[#1E293B] dark:text-[#f1f5f9]">{{ $notification->title }}</h3>
                        <p class="text-[#64748B] dark:text-[#cbd5e1] mt-1">{{ $notification->message }}</p>
                        <p class="text-sm text-[#64748B] dark:text-[#cbd5e1] mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    @unless($notification->read_at)
                    <form method="POST" action="{{ route('notifications.read', $notification) }}">
                        @csrf
                        <button type="submit" class="min-h-[44px] text-xs px-3 py-1.5 rounded-full bg-orange-100 dark:bg-orange-500/20 text-[#141414] dark:text-[#FAF6EF] dark:text-[#141414] dark:text-[#FAF6EF] hover:bg-orange-200 dark:hover:bg-orange-500/30 transition whitespace-nowrap focus:outline-none focus:ring-2 focus:ring-orange-300">
                            Tandai terbaca
                        </button>
                    </form>
                    @endunless
                </div>
            </x-card>
            @empty
            <x-card>
                <x-empty-state icon="bell" title="Belum ada notifikasi">
                    Pembaruan status pesanan kamu akan muncul di sini.
                </x-empty-state>
            </x-card>
            @endforelse
        </div>

        @if($notifications->hasPages())
        <div class="mt-8">{{ $notifications->links() }}</div>
        @endif
    </div>
</div>

<script>
    (function () {
        var btn = document.getElementById('push-toggle');
        if (!btn || !window.JastipPush) return;
        if (!('serviceWorker' in navigator) || !('PushManager' in window) || !('Notification' in window)) {
            btn.style.display = 'none';
            return;
        }
        navigator.serviceWorker.ready.then(function (reg) {
            return reg.pushManager.getSubscription();
        }).then(function (sub) {
            if (sub) btn.textContent = 'Notifikasi browser aktif';
        }).catch(function () {});

        btn.addEventListener('click', function () {
            btn.disabled = true;
            window.JastipPush.enable().then(function (ok) {
                btn.disabled = false;
                btn.textContent = ok ? 'Notifikasi browser aktif' : 'Aktifkan notifikasi browser';
                if (!ok) alert('Tidak dapat mengaktifkan notifikasi. Pastikan izin browser diizinkan.');
            });
        });
    })();
</script>
@endsection