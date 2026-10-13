@extends('layouts.admin')

@section('title', 'Chat Pelanggan')

@section('content')
<div class="space-y-6">

    <x-page-header title="Chat Pelanggan" subtitle="Balas pertanyaan pelanggan dari halaman bantuan." class="animate-fade-up" />

    <x-card padded="false" class="animate-fade-up" style="animation-delay: 120ms">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px]">
                <thead class="bg-[#F1F5F9] dark:bg-[#2e323b] border-b border-[#E2E8F0] dark:border-[#404854]">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#64748B] dark:text-[#cbd5e1] uppercase tracking-wider">Pelanggan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#64748B] dark:text-[#cbd5e1] uppercase tracking-wider">Pesan Terakhir</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#64748B] dark:text-[#cbd5e1] uppercase tracking-wider">Waktu</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#64748B] dark:text-[#cbd5e1] uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#404854] stagger">
                    @forelse($conversations as $conversation)
                    @php($unread = $unreadByUser[$conversation->id] ?? 0)
                    <tr class="hover:bg-[#f8fafc] dark:hover:bg-[#2e323b] transition-colors">
                        <td class="px-4 py-3">
                            <div class="text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9]">{{ $conversation->name }}</div>
                            <div class="text-xs text-[#64748B] dark:text-[#cbd5e1] break-all">{{ $conversation->email }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm text-[#64748B] dark:text-[#cbd5e1] max-w-md truncate">
                                {{ $conversation->latestChatMessage?->body }}
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-[#64748B] dark:text-[#cbd5e1]">
                            {{ $conversation->latestChatMessage?->created_at?->diffForHumans() }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            <a href="{{ route('admin.chat.show', $conversation) }}"
                               class="inline-flex items-center gap-2 text-[#0891B2] dark:text-[#22D3EE] font-medium hover:underline rounded focus:outline-none focus:ring-2 focus:ring-orange-300">
                                <x-icon name="chat" class="w-4 h-4" /> Buka
                                @if($unread > 0)
                                    <x-badge tone="danger" aria-label="{{ $unread }} pesan belum dibaca">{{ $unread }}</x-badge>
                                @endif
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4">
                            <x-empty-state icon="chat" title="Belum ada percakapan">
                                Pesan dari pelanggan akan tampil di sini.
                            </x-empty-state>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

</div>
@endsection