@extends('layouts.admin')
@section('title', 'Edit Invoice ' . $order->order_number . ' | ' . setting('brand_name'))

@section('content')
<div class="space-y-6" x-data="manualInvoice()">
    <x-page-header title="Edit Invoice" class="animate-fade-up">
        <x-btn as="a" :href="route('admin.orders.show', $order)" variant="ghost" icon="arrow-left">Kembali ke Pesanan</x-btn>
    </x-page-header>

    @if($errors->any())
        <div class="bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 text-red-700 dark:text-red-400 text-sm rounded-lg p-4 animate-fade-up">
            <p class="font-medium mb-1">Periksa kembali isian berikut:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @include('admin.orders._manual-form', [
        'action' => route('admin.orders.update', $order),
        'method' => 'PUT',
        'isEdit' => true,
        'order' => $order,
        'submitLabel' => 'Simpan Perubahan',
    ])
</div>
@endsection

@include('admin.orders._manual-form-script')
