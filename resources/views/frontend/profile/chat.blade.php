@extends('layouts.app')

@section('title', 'Bantuan & Chat | NITIP DI END')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#FDF6EC] to-[#ede7de] dark:from-[#1a1a1a] dark:to-[#23252b] py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-[#f1f5f9] mb-2">Bantuan & Chat</h1>
        <p class="text-gray-600 dark:text-[#cbd5e1] mb-8">Tanya apa saja soal titipanmu. Tim kami akan membalas di sini.</p>

        <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-lg border border-gray-200 dark:border-[#404854] flex flex-col">
            <div class="flex-1 space-y-3 p-5 max-h-[60vh] overflow-y-auto">
                @forelse($messages as $message)
                    <div class="flex {{ $message->isFromAdmin() ? 'justify-start' : 'justify-end' }}">
                        <div class="max-w-[80%] rounded-2xl px-4 py-2.5 text-sm
                            {{ $message->isFromAdmin()
                                ? 'bg-gray-100 dark:bg-[#2e323b] text-gray-800 dark:text-[#e2e8f0] rounded-tl-sm'
                                : 'bg-orange-500 text-white rounded-tr-sm' }}">
                            <p class="whitespace-pre-line">{{ $message->body }}</p>
                            <p class="mt-1 text-[11px] {{ $message->isFromAdmin() ? 'text-gray-500 dark:text-[#9ca3af]' : 'text-orange-100' }}">
                                {{ $message->isFromAdmin() ? 'Admin' : 'Anda' }} &middot; {{ $message->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center">
                        <p class="text-gray-500 dark:text-[#9ca3af]">Belum ada pesan. Mulai percakapan di bawah.</p>
                    </div>
                @endforelse
            </div>

            <form method="POST" action="{{ route('chat.store') }}"
                  class="border-t border-gray-200 dark:border-[#404854] p-4 flex items-end gap-3">
                @csrf
                <div class="flex-1">
                    <textarea name="body" rows="2" required maxlength="2000"
                              placeholder="Tulis pesan..."
                              class="w-full rounded-xl border-gray-300 dark:border-[#404854] dark:bg-[#1a1a1a] dark:text-[#f1f5f9] text-sm focus:border-orange-500 focus:ring-orange-500">{{ old('body') }}</textarea>
                    @error('body')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit"
                        class="px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-xl transition">
                    Kirim
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
