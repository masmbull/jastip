@extends('layouts.admin')

@section('title', 'Chat Pelanggan')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-[#f1f5f9]">Chat Pelanggan</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-[#cbd5e1]">Balas pertanyaan pelanggan dari halaman bantuan.</p>
    </div>

    <div class="bg-white dark:bg-[#23252b] rounded-lg shadow border border-gray-200 dark:border-[#404854] overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-[#404854]">
            <thead class="bg-gray-50 dark:bg-[#2e323b]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-[#cbd5e1] uppercase tracking-wider">Pelanggan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-[#cbd5e1] uppercase tracking-wider">Pesan Terakhir</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-[#cbd5e1] uppercase tracking-wider">Waktu</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-[#cbd5e1] uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-[#404854]">
                @forelse($conversations as $conversation)
                @php($unread = $unreadByUser[$conversation->id] ?? 0)
                <tr class="hover:bg-gray-50 dark:hover:bg-[#404854] transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900 dark:text-[#f1f5f9]">{{ $conversation->name }}</div>
                        <div class="text-xs text-gray-500 dark:text-[#9ca3af]">{{ $conversation->email }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-600 dark:text-[#cbd5e1] max-w-md truncate">
                            {{ $conversation->latestChatMessage?->body }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-[#9ca3af]">
                        {{ $conversation->latestChatMessage?->created_at?->diffForHumans() }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="{{ route('admin.chat.show', $conversation) }}" class="inline-flex items-center gap-2 text-[#06B6D4] hover:underline">
                            Buka
                            @if($unread > 0)
                                <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full">{{ $unread }}</span>
                            @endif
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-[#9ca3af]">
                        Belum ada percakapan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
