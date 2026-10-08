@extends('layouts.app')

@section('title', 'Notifikasi | NITIP DI END')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#ecfeff] to-[#e0f2fe] dark:from-[#1a1a1a] dark:to-[#23252b] py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-[#f1f5f9]">Notifikasi</h1>
            <div class="flex items-center gap-2">
                <button type="button" id="push-toggle"
                        class="text-sm px-4 py-2 border border-gray-300 dark:border-[#404854] rounded-lg text-gray-700 dark:text-[#cbd5e1] hover:bg-gray-50 dark:hover:bg-[#2e323b] transition">
                    Aktifkan notifikasi browser
                </button>
                @if($notifications->total() > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="text-sm px-4 py-2 border border-gray-300 dark:border-[#404854] rounded-lg text-gray-700 dark:text-[#cbd5e1] hover:bg-gray-50 dark:hover:bg-[#2e323b] transition">
                        Tandai semua terbaca
                    </button>
                </form>
                @endif
            </div>
        </div>

        <div class="space-y-3">
            @forelse($notifications as $notification)
            <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-lg p-5 border {{ $notification->read_at ? 'border-gray-200 dark:border-[#404854]' : 'border-l-4 border-l-orange-400 border-gray-200 dark:border-[#404854]' }}">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-900 dark:text-[#f1f5f9]">{{ $notification->title }}</h3>
                        <p class="text-gray-700 dark:text-[#cbd5e1] mt-1">{{ $notification->message }}</p>
                        <p class="text-sm text-gray-500 dark:text-[#9ca3af] mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    @unless($notification->read_at)
                    <form method="POST" action="{{ route('notifications.read', $notification) }}">
                        @csrf
                        <button type="submit" class="text-xs px-3 py-1 rounded-full bg-orange-100 dark:bg-orange-500/20 text-orange-700 dark:text-orange-300 hover:bg-orange-200 transition whitespace-nowrap">
                            Tandai terbaca
                        </button>
                    </form>
                    @endunless
                </div>
            </div>
            @empty
            <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-lg p-12 text-center border border-gray-200 dark:border-[#404854]">
                <p class="text-gray-500 dark:text-[#9ca3af] text-lg">Belum ada notifikasi</p>
            </div>
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
