@extends('layouts.app')

@section('title', 'Program Loyalitas | NITIP DI END')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#ecfeff] to-[#e0f2fe] dark:from-[#1a1a1a] dark:to-[#23252b] py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-[#f1f5f9] mb-8">Program Loyalitas</h1>

        {{-- Loyalty Status --}}
        <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-lg p-8 border border-gray-200 dark:border-[#404854] mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-5xl font-bold text-[#06B6D4] mb-2">{{ $profile->loyalty_points ?? 0 }}</div>
                    <p class="text-gray-600 dark:text-[#cbd5e1]">Total Poin</p>
                </div>
                <div class="text-center">
                    <div class="text-3xl mb-2"><x-icon name="star" class="inline-block w-10 h-10 align-text-bottom" /></div>
                    <p class="text-xl font-bold text-gray-900 dark:text-[#f1f5f9] capitalize">{{ $profile->membership_tier ?? 'bronze' }}</p>
                    <p class="text-gray-600 dark:text-[#cbd5e1] text-sm">Member</p>
                </div>
                <div class="text-center">
                    <div class="text-3xl mb-2"><x-icon name="gift" class="inline-block w-10 h-10 align-text-bottom" /></div>
                    <p class="text-gray-900 dark:text-[#f1f5f9]">Tukarkan Poin</p>
                    <p class="text-sm text-gray-600 dark:text-[#cbd5e1]">Dapatkan Hadiah</p>
                </div>
            </div>
        </div>

        {{-- Membership Tiers --}}
        <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-lg p-8 border border-gray-200 dark:border-[#404854]">
            <h2 class="text-xl font-bold text-gray-900 dark:text-[#f1f5f9] mb-6">Tingkatan Member</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="p-4 border-2 border-gray-300 dark:border-[#404854] rounded-lg text-center">
                    <div class="text-3xl mb-2"><x-icon name="star" class="inline-block w-10 h-10 align-text-bottom" /></div>
                    <p class="font-bold text-gray-900 dark:text-[#f1f5f9]">Bronze</p>
                    <p class="text-sm text-gray-600 dark:text-[#cbd5e1]">0 - 999 Poin</p>
                </div>
                <div class="p-4 border-2 border-gray-300 dark:border-[#404854] rounded-lg text-center">
                    <div class="text-3xl mb-2"><x-icon name="star" class="inline-block w-10 h-10 align-text-bottom" /></div>
                    <p class="font-bold text-gray-900 dark:text-[#f1f5f9]">Silver</p>
                    <p class="text-sm text-gray-600 dark:text-[#cbd5e1]">1000 - 1999 Poin</p>
                </div>
                <div class="p-4 border-2 border-gray-300 dark:border-[#404854] rounded-lg text-center">
                    <div class="text-3xl mb-2"><x-icon name="star" class="inline-block w-10 h-10 align-text-bottom" /></div>
                    <p class="font-bold text-gray-900 dark:text-[#f1f5f9]">Gold</p>
                    <p class="text-sm text-gray-600 dark:text-[#cbd5e1]">2000 - 4999 Poin</p>
                </div>
                <div class="p-4 border-2 border-[#06B6D4] dark:border-[#06B6D4] rounded-lg text-center">
                    <div class="text-3xl mb-2"><x-icon name="star" class="inline-block w-10 h-10 align-text-bottom" /></div>
                    <p class="font-bold text-gray-900 dark:text-[#f1f5f9]">Platinum</p>
                    <p class="text-sm text-gray-600 dark:text-[#cbd5e1]">5000+ Poin</p>
                </div>
            </div>
        </div>

        {{-- Benefits --}}
        <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-lg p-8 border border-gray-200 dark:border-[#404854] mt-8">
            <h2 class="text-xl font-bold text-gray-900 dark:text-[#f1f5f9] mb-6">Keuntungan Member</h2>
            <ul class="space-y-3">
                <li class="flex items-start gap-3">
                    <span class="text-[#06B6D4]"><x-icon name="check" class="inline-block w-5 h-5 align-text-bottom" /></span>
                    <p class="text-gray-700 dark:text-[#cbd5e1]">Dapatkan 1 poin untuk setiap Rp 100 yang dibelanjakan</p>
                </li>
                <li class="flex items-start gap-3">
                    <span class="text-[#06B6D4]"><x-icon name="check" class="inline-block w-5 h-5 align-text-bottom" /></span>
                    <p class="text-gray-700 dark:text-[#cbd5e1]">Tukarkan poin dengan diskon dan hadiah eksklusif</p>
                </li>
                <li class="flex items-start gap-3">
                    <span class="text-[#06B6D4]"><x-icon name="check" class="inline-block w-5 h-5 align-text-bottom" /></span>
                    <p class="text-gray-700 dark:text-[#cbd5e1]">Dapatkan akses early-bird ke penawaran khusus</p>
                </li>
                <li class="flex items-start gap-3">
                    <span class="text-[#06B6D4]"><x-icon name="check" class="inline-block w-5 h-5 align-text-bottom" /></span>
                    <p class="text-gray-700 dark:text-[#cbd5e1]">Bonus poin di hari ulang tahun Anda</p>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
