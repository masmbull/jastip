@extends('layouts.admin')

@section('title', 'Chat: ' . $user->name)

@section('content')
<div class="space-y-6">

    <x-page-header title="{{ $user->name }}" subtitle="{{ $user->email }}" class="animate-fade-up">
        <x-btn as="a" :href="route('admin.chat.index')" variant="ghost" icon="arrow-left">Semua percakapan</x-btn>
    </x-page-header>

    <x-card padded="false" class="animate-fade-up" style="animation-delay: 120ms">
        <div class="flex flex-col">
            <div class="flex-1 space-y-3 p-5 min-h-[40vh] max-h-[60vh] overflow-y-auto" role="log" aria-label="Riwayat percakapan">
                @forelse($messages as $message)
                    <div class="flex {{ $message->isFromAdmin() ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[80%] rounded-2xl px-4 py-2.5 text-sm
                            {{ $message->isFromAdmin()
                                ? 'bg-orange-500 text-white rounded-tr-sm'
                                : 'bg-[#F1F5F9] dark:bg-[#2e323b] text-[#1E293B] dark:text-[#e2e8f0] rounded-tl-sm' }}">
                            <p class="whitespace-pre-line">{{ $message->body }}</p>
                            <p class="mt-1 text-[11px] {{ $message->isFromAdmin() ? 'text-orange-100' : 'text-[#64748B] dark:text-[#9ca3af]' }}">
                                {{ $message->isFromAdmin() ? 'Admin' : $user->name }} &middot; {{ $message->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                @empty
                    <x-empty-state icon="chat" title="Belum ada pesan di percakapan ini." />
                @endforelse
            </div>

            <form method="POST" action="{{ route('admin.chat.store', $user) }}"
                  class="border-t border-[#E2E8F0] dark:border-[#404854] p-4 flex flex-col sm:flex-row sm:items-end gap-3">
                @csrf
                <div class="flex-1">
                    <label for="chat-body" class="sr-only">Balasan untuk {{ $user->name }}</label>
                    <textarea id="chat-body" name="body" rows="2" required maxlength="2000"
                              placeholder="Tulis balasan..."
                              class="w-full rounded-xl border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#1a1a1a] text-[#1E293B] dark:text-[#f1f5f9] text-sm focus:border-orange-500 focus:ring-orange-500">{{ old('body') }}</textarea>
                    @error('body')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <x-btn type="submit" variant="brand" icon="arrow-right" class="sm:shrink-0">Kirim</x-btn>
            </form>
        </div>
    </x-card>

</div>
@endsection