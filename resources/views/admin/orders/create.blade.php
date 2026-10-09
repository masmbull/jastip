@extends('layouts.admin')
@section('title', 'Buat Invoice | ' . setting('brand_name'))

@section('content')
<div class="p-6 space-y-6" x-data="manualInvoice()">
    <div class="flex items-center justify-between animate-fade-up">
        <div>
            <h1 class="text-2xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">Buat Invoice</h1>
            <a href="{{ route('admin.invoices.index') }}" class="text-sm text-orange-600 hover:underline ml-2">Kembali</a>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg p-4">
            <p class="font-medium mb-1">Periksa kembali isian berikut:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @include('admin.orders._manual-form')
</div>
@endsection

@include('admin.orders._manual-form-script')