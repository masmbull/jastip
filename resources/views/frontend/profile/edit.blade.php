@extends('layouts.app')

@section('title', 'Edit Profil | NITIP DI END')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#ecfeff] to-[#e0f2fe] dark:from-[#1a1a1a] dark:to-[#23252b] py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-6">
            <a href="{{ route('profile.show') }}" class="inline-flex items-center gap-1 text-sm font-medium text-[#06B6D4] hover:underline focus:outline-none focus:ring-2 focus:ring-orange-300 rounded">
                <x-icon name="arrow-left" class="w-4 h-4" /> Kembali
            </a>
        </div>

        <x-card>
            <h1 class="text-2xl font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-6">Edit Profil</h1>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Profile Picture --}}
                <div>
                    <label for="profile_picture" class="block text-sm font-medium text-[#1E293B] dark:text-[#cbd5e1] mb-3">Foto Profil</label>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-6">
                        @if($profile->profile_picture)
                            <img src="{{ asset('storage/' . $profile->profile_picture) }}" alt="{{ $user->name }}"
                                 class="w-20 h-20 rounded-full object-cover border-4 border-[#06B6D4] shrink-0">
                        @else
                            <div class="w-20 h-20 rounded-full bg-[#06B6D4] flex items-center justify-center text-white text-3xl font-bold shrink-0">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="min-w-0">
                            <input type="file" id="profile_picture" name="profile_picture" accept="image/*"
                                   class="block w-full text-sm text-[#64748B] dark:text-[#cbd5e1]
                                   file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                                   file:text-sm file:font-semibold file:bg-[#06B6D4] file:text-white
                                   hover:file:bg-[#FFC800] focus:outline-none focus:ring-2 focus:ring-orange-300 rounded-lg">
                            <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-2">Max 2MB. Format: JPG, PNG, GIF</p>
                        </div>
                    </div>
                    @error('profile_picture')
                        <p class="text-red-600 dark:text-red-300 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-[#1E293B] dark:text-[#cbd5e1] mb-2">Nama Lengkap</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                           class="nb-input w-full">
                    @error('name')
                        <p class="text-red-600 dark:text-red-300 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-[#1E293B] dark:text-[#cbd5e1] mb-2">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="nb-input w-full">
                    @error('email')
                        <p class="text-red-600 dark:text-red-300 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label for="phone" class="block text-sm font-medium text-[#1E293B] dark:text-[#cbd5e1] mb-2">No. Telepon</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $profile->phone) }}"
                           class="nb-input w-full"
                           placeholder="08xxxxxxxxxx">
                    @error('phone')
                        <p class="text-red-600 dark:text-red-300 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Date of Birth --}}
                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-[#1E293B] dark:text-[#cbd5e1] mb-2">Tanggal Lahir</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $profile->date_of_birth?->format('Y-m-d')) }}"
                           class="nb-input w-full">
                    @error('date_of_birth')
                        <p class="text-red-600 dark:text-red-300 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Gender --}}
                <div>
                    <label for="gender" class="block text-sm font-medium text-[#1E293B] dark:text-[#cbd5e1] mb-2">Jenis Kelamin</label>
                    <select id="gender" name="gender" class="nb-input w-full">
                        <option value="">-- Pilih --</option>
                        <option value="male" {{ old('gender', $profile->gender) === 'male' ? 'selected' : '' }}>Pria</option>
                        <option value="female" {{ old('gender', $profile->gender) === 'female' ? 'selected' : '' }}>Wanita</option>
                        <option value="other" {{ old('gender', $profile->gender) === 'other' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('gender')
                        <p class="text-red-600 dark:text-red-300 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Bio --}}
                <div>
                    <label for="bio" class="block text-sm font-medium text-[#1E293B] dark:text-[#cbd5e1] mb-2">Biodata</label>
                    <textarea id="bio" name="bio" rows="4" maxlength="500"
                              class="nb-input w-full"
                              placeholder="Ceritakan tentang Anda...">{{ old('bio', $profile->bio) }}</textarea>
                    <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-1">Max 500 karakter</p>
                    @error('bio')
                        <p class="text-red-600 dark:text-red-300 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <div class="flex flex-col-reverse sm:flex-row gap-3 pt-6 border-t border-[#E2E8F0] dark:border-[#404854]">
                    <x-btn as="a" :href="route('profile.show')" variant="ghost" class="flex-1">Batal</x-btn>
                    <x-btn type="submit" variant="brand" class="flex-1">Simpan Perubahan</x-btn>
                </div>
            </form>
        </x-card>
    </div>
</div>
@endsection