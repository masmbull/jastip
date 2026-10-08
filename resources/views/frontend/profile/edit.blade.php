@extends('layouts.app')

@section('title', 'Edit Profil | NITIP DI END')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#ecfeff] to-[#e0f2fe] dark:from-[#1a1a1a] dark:to-[#23252b] py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-6">
            <a href="{{ route('profile.show') }}" class="text-[#06B6D4] hover:underline"><x-icon name="arrow-left" class="inline-block w-5 h-5 align-text-bottom" /> Kembali</a>
        </div>

        <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-lg p-8 border border-gray-200 dark:border-[#404854]">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-[#f1f5f9] mb-6">Edit Profil</h1>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Profile Picture --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-[#cbd5e1] mb-3">Foto Profil</label>
                    <div class="flex items-center gap-6">
                        @if($profile->profile_picture)
                            <img src="{{ asset('storage/' . $profile->profile_picture) }}" alt="{{ $user->name }}"
                                 class="w-24 h-24 rounded-full object-cover border-4 border-[#06B6D4]">
                        @else
                            <div class="w-24 h-24 rounded-full bg-[#06B6D4] flex items-center justify-center text-white text-3xl font-bold">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <input type="file" name="profile_picture" accept="image/*"
                                   class="block w-full text-sm text-gray-500 dark:text-[#9ca3af]
                                   file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                                   file:text-sm file:font-semibold file:bg-[#06B6D4] file:text-white
                                   hover:file:bg-[#0E7490]">
                            <p class="text-xs text-gray-500 dark:text-[#9ca3af] mt-2">Max 2MB. Format: JPG, PNG, GIF</p>
                        </div>
                    </div>
                    @error('profile_picture')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-[#cbd5e1] mb-2">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-4 py-2 bg-gray-100 dark:bg-[#404854] border border-gray-300 dark:border-[#404854] rounded-lg dark:text-[#f1f5f9] focus:outline-none focus:ring-2 focus:ring-[#06B6D4]">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-[#cbd5e1] mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-4 py-2 bg-gray-100 dark:bg-[#404854] border border-gray-300 dark:border-[#404854] rounded-lg dark:text-[#f1f5f9] focus:outline-none focus:ring-2 focus:ring-[#06B6D4]">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-[#cbd5e1] mb-2">No. Telepon</label>
                    <input type="tel" name="phone" value="{{ old('phone', $profile->phone) }}"
                           class="w-full px-4 py-2 bg-gray-100 dark:bg-[#404854] border border-gray-300 dark:border-[#404854] rounded-lg dark:text-[#f1f5f9] focus:outline-none focus:ring-2 focus:ring-[#06B6D4]"
                           placeholder="08xxxxxxxxxx">
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Date of Birth --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-[#cbd5e1] mb-2">Tanggal Lahir</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $profile->date_of_birth?->format('Y-m-d')) }}"
                           class="w-full px-4 py-2 bg-gray-100 dark:bg-[#404854] border border-gray-300 dark:border-[#404854] rounded-lg dark:text-[#f1f5f9] focus:outline-none focus:ring-2 focus:ring-[#06B6D4]">
                    @error('date_of_birth')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Gender --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-[#cbd5e1] mb-2">Jenis Kelamin</label>
                    <select name="gender" class="w-full px-4 py-2 bg-gray-100 dark:bg-[#404854] border border-gray-300 dark:border-[#404854] rounded-lg dark:text-[#f1f5f9] focus:outline-none focus:ring-2 focus:ring-[#06B6D4]">
                        <option value="">-- Pilih --</option>
                        <option value="male" {{ old('gender', $profile->gender) === 'male' ? 'selected' : '' }}>Pria</option>
                        <option value="female" {{ old('gender', $profile->gender) === 'female' ? 'selected' : '' }}>Wanita</option>
                        <option value="other" {{ old('gender', $profile->gender) === 'other' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('gender')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Bio --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-[#cbd5e1] mb-2">Biodata</label>
                    <textarea name="bio" rows="4" maxlength="500"
                              class="w-full px-4 py-2 bg-gray-100 dark:bg-[#404854] border border-gray-300 dark:border-[#404854] rounded-lg dark:text-[#f1f5f9] focus:outline-none focus:ring-2 focus:ring-[#06B6D4]"
                              placeholder="Ceritakan tentang Anda...">{{ old('bio', $profile->bio) }}</textarea>
                    <p class="text-xs text-gray-500 dark:text-[#9ca3af] mt-1">Max 500 karakter</p>
                    @error('bio')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <div class="flex gap-3 pt-6 border-t border-gray-200 dark:border-[#404854]">
                    <a href="{{ route('profile.show') }}" class="flex-1 px-4 py-2 border border-gray-300 dark:border-[#404854] text-gray-700 dark:text-[#cbd5e1] rounded-lg hover:bg-gray-100 dark:hover:bg-[#404854] transition text-center">
                        Batal
                    </a>
                    <button type="submit" class="flex-1 px-4 py-2 bg-[#06B6D4] text-white rounded-lg hover:bg-[#0E7490] transition font-medium">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
