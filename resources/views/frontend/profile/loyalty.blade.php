@extends('layouts.app')

@section('title', 'Program Loyalitas | NITIP DI END')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#ecfeff] to-[#e0f2fe] dark:from-[#1a1a1a] dark:to-[#23252b] py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-8">Program Loyalitas</h1>

        {{-- Loyalty Status --}}
        <x-card class="mb-8">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-5xl font-bold text-[#06B6D4] mb-2 num">{{ $profile->loyalty_points ?? 0 }}</div>
                    <p class="text-[#64748B] dark:text-[#cbd5e1]">Total Poin</p>
                </div>
                <div class="text-center">
                    <x-icon name="star" class="w-10 h-10 mx-auto mb-2 text-orange-500" />
                    <p class="text-xl font-bold text-[#1E293B] dark:text-[#f1f5f9] capitalize">{{ $profile->membership_tier ?? 'bronze' }}</p>
                    <p class="text-[#64748B] dark:text-[#cbd5e1] text-sm">Member</p>
                </div>
                <div class="text-center">
                    <x-icon name="gift" class="w-10 h-10 mx-auto mb-2 text-orange-500" />
                    <p class="text-[#1E293B] dark:text-[#f1f5f9]">Tukarkan Poin</p>
                    <p class="text-sm text-[#64748B] dark:text-[#cbd5e1]">Dapatkan Hadiah</p>
                </div>
            </div>
        </x-card>

        {{-- Membership Tiers --}}
        <x-card>
            <h2 class="text-xl font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-6">Tingkatan Member</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="p-4 border-2 border-[#E2E8F0] dark:border-[#404854] rounded-lg text-center">
                    <x-icon name="star" class="w-8 h-8 mx-auto mb-2 text-orange-500" />
                    <p class="font-bold text-[#1E293B] dark:text-[#f1f5f9]">Bronze</p>
                    <p class="text-sm text-[#64748B] dark:text-[#cbd5e1] num">0 - 999 Poin</p>
                </div>
                <div class="p-4 border-2 border-[#E2E8F0] dark:border-[#404854] rounded-lg text-center">
                    <x-icon name="star" class="w-8 h-8 mx-auto mb-2 text-orange-500" />
                    <p class="font-bold text-[#1E293B] dark:text-[#f1f5f9]">Silver</p>
                    <p class="text-sm text-[#64748B] dark:text-[#cbd5e1] num">1000 - 1999 Poin</p>
                </div>
                <div class="p-4 border-2 border-[#E2E8F0] dark:border-[#404854] rounded-lg text-center">
                    <x-icon name="star" class="w-8 h-8 mx-auto mb-2 text-orange-500" />
                    <p class="font-bold text-[#1E293B] dark:text-[#f1f5f9]">Gold</p>
                    <p class="text-sm text-[#64748B] dark:text-[#cbd5e1] num">2000 - 4999 Poin</p>
                </div>
                <div class="p-4 border-2 border-[#06B6D4] rounded-lg text-center">
                    <x-icon name="star" class="w-8 h-8 mx-auto mb-2 text-orange-500" />
                    <p class="font-bold text-[#1E293B] dark:text-[#f1f5f9]">Platinum</p>
                    <p class="text-sm text-[#64748B] dark:text-[#cbd5e1] num">5000+ Poin</p>
                </div>
            </div>
        </x-card>

        {{-- Benefits --}}
        <x-card class="mt-8">
            <h2 class="text-xl font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-6">Keuntungan Member</h2>
            <ul class="space-y-3">
                <li class="flex items-start gap-3">
                    <x-icon name="check" class="w-5 h-5 text-[#06B6D4] mt-0.5 shrink-0" />
                    <p class="text-[#64748B] dark:text-[#cbd5e1]">Dapatkan 1 poin untuk setiap Rp 100 yang dibelanjakan</p>
                </li>
                <li class="flex items-start gap-3">
                    <x-icon name="check" class="w-5 h-5 text-[#06B6D4] mt-0.5 shrink-0" />
                    <p class="text-[#64748B] dark:text-[#cbd5e1]">Tukarkan poin dengan diskon dan hadiah eksklusif</p>
                </li>
                <li class="flex items-start gap-3">
                    <x-icon name="check" class="w-5 h-5 text-[#06B6D4] mt-0.5 shrink-0" />
                    <p class="text-[#64748B] dark:text-[#cbd5e1]">Dapatkan akses early-bird ke penawaran khusus</p>
                </li>
                <li class="flex items-start gap-3">
                    <x-icon name="check" class="w-5 h-5 text-[#06B6D4] mt-0.5 shrink-0" />
                    <p class="text-[#64748B] dark:text-[#cbd5e1]">Bonus poin di hari ulang tahun Anda</p>
                </li>
            </ul>
        </x-card>
    </div>
</div>
@endsection