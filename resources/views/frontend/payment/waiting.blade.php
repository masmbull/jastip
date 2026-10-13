@extends('layouts.app')
@section('title', 'Menunggu Pembayaran - ' . $order->order_number)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-6 md:mb-8 text-center">Selesaikan Pembayaran</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
        <!-- QRIS Section -->
        <x-card class="space-y-4">
            <h2 class="text-xl md:text-2xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">Scan QRIS</h2>
            <p class="text-sm md:text-base text-[#64748B] dark:text-[#cbd5e1]">Scan dengan aplikasi perbankan atau e-wallet kamu</p>

            <!-- QRIS Image -->
            <div class="bg-[#F1F5F9] dark:bg-[#2e323b] rounded-lg p-4 flex items-center justify-center transition-colors">
                @if($qrisImage)
                    <img src="{{ $qrisImage }}" alt="Kode QRIS pembayaran pesanan {{ $order->order_number }}" class="max-w-sm w-full rounded" id="qrisImage">
                @else
                    <p class="text-[#64748B] dark:text-[#cbd5e1] text-center text-sm md:text-base">QRIS tidak tersedia</p>
                @endif
            </div>

            <!-- Download Button -->
            <a href="{{ route('payment.download-qris', $order) }}"
               class="w-full inline-flex items-center justify-center gap-2 min-h-[44px] px-3 md:px-4 py-2 md:py-3 bg-[#0891B2] hover:bg-[#0E7490] text-white rounded-lg font-medium transition-colors text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-orange-300">
                <x-icon name="download" class="w-4 md:w-5 h-4 md:h-5" />
                Download QRIS
            </a>

            <!-- Merchant Info -->
            <div class="bg-orange-50 dark:bg-orange-500/10 border border-orange-200 dark:border-orange-500/30 rounded-lg p-3 md:p-4 transition-colors">
                <p class="text-xs md:text-sm text-[#64748B] dark:text-[#cbd5e1] mb-1">Atas nama</p>
                <p class="font-semibold text-[#1E293B] dark:text-[#f1f5f9]">{{ $qrisMerchant }}</p>
            </div>

            <!-- Amount -->
            <div class="bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 rounded-lg p-3 md:p-4 transition-colors">
                <p class="text-xs md:text-sm text-[#64748B] dark:text-[#cbd5e1] mb-1">Total yang harus dibayar</p>
                <p class="text-2xl md:text-3xl font-bold text-emerald-600 dark:text-emerald-400 num">
                    {{ format_price($order->total) }}
                </p>
            </div>
        </x-card>

        <!-- Order Summary & Upload Section -->
        <div class="space-y-4 md:space-y-6">
            <!-- Order Summary -->
            <x-card class="space-y-4">
                <h2 class="text-lg md:text-xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">Ringkasan Pesanan</h2>

                <div class="space-y-2 text-xs md:text-sm">
                    <div class="flex justify-between gap-4 text-[#64748B] dark:text-[#cbd5e1]">
                        <span>Nomor Pesanan</span>
                        <span class="font-semibold text-[#1E293B] dark:text-[#f1f5f9] num">{{ $order->order_number }}</span>
                    </div>
                    <div class="flex justify-between gap-4 text-[#64748B] dark:text-[#cbd5e1]">
                        <span>Subtotal</span>
                        <span class="font-medium text-[#1E293B] dark:text-[#f1f5f9] num">{{ format_price($order->subtotal) }}</span>
                    </div>
                    <div class="flex justify-between gap-4 text-[#64748B] dark:text-[#cbd5e1]">
                        <span>Ongkir</span>
                        <span class="font-medium text-[#1E293B] dark:text-[#f1f5f9] num">{{ format_price($order->shipping_cost) }}</span>
                    </div>
                    @if($order->fee > 0)
                        <div class="flex justify-between gap-4 text-[#64748B] dark:text-[#cbd5e1]">
                            <span>Biaya Fee</span>
                            <span class="font-medium text-[#1E293B] dark:text-[#f1f5f9] num">{{ format_price($order->fee) }}</span>
                        </div>
                    @endif
                    <div class="border-t border-[#E2E8F0] dark:border-[#404854] pt-2 flex justify-between gap-4">
                        <span class="font-semibold text-[#1E293B] dark:text-[#f1f5f9]">Total</span>
                        <span class="text-base md:text-lg font-bold text-[#0E7490] dark:text-[#67e8f9] num">{{ format_price($order->total) }}</span>
                    </div>
                </div>

                <!-- Countdown Timer -->
                <div class="bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 rounded-lg p-3 md:p-4 transition-colors">
                    <p class="text-xs md:text-sm text-[#64748B] dark:text-[#cbd5e1] mb-2 flex items-center gap-1"><x-icon name="clock" class="w-4 h-4" /> Waktu pembayaran</p>
                    <div class="flex items-baseline gap-2">
                        <p class="text-2xl font-bold text-[#0E7490] dark:text-[#67e8f9] num" id="countdown">--:--:--</p>
                        <span class="text-xs text-[#64748B] dark:text-[#cbd5e1]">sisa waktu</span>
                    </div>
                    <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-2">Pembayaran harus selesai sebelum waktu habis</p>
                </div>
            </x-card>

            <!-- Upload Proof Section -->
            <x-card class="space-y-4">
                <h2 class="text-lg md:text-xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">Unggah Bukti Pembayaran</h2>
                <p class="text-xs md:text-sm text-[#64748B] dark:text-[#cbd5e1]">Screenshot atau foto struk pembayaran QRIS kamu</p>

                <!-- Upload Area -->
                <button type="button"
                        class="w-full border-2 border-dashed border-[#E2E8F0] dark:border-[#404854] rounded-lg p-4 md:p-6 text-center hover:border-orange-300 dark:hover:border-orange-500/50 transition-colors cursor-pointer bg-[#F8FAFC] dark:bg-[#1a1c22] focus:outline-none focus:ring-2 focus:ring-orange-300"
                        onclick="document.getElementById('proofInput').click()">
                    <x-icon name="photo" class="w-10 md:w-12 h-10 md:h-12 text-[#64748B] dark:text-[#cbd5e1] mx-auto mb-2 md:mb-3" />
                    <span class="block font-medium text-[#1E293B] dark:text-[#f1f5f9] text-sm md:text-base">Klik untuk upload atau drag file di sini</span>
                    <span class="block text-xs text-[#64748B] dark:text-[#cbd5e1] mt-1">JPG, PNG (Max 5MB)</span>
                </button>

                <input type="file" id="proofInput" accept="image/jpeg,image/png" class="hidden">

                <!-- Upload Status -->
                <div id="uploadStatus" class="hidden space-y-2">
                    <div class="flex items-center gap-2 p-3 md:p-4 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 rounded-lg transition-colors">
                        <x-icon name="check-circle" class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" />
                        <div>
                            <p class="font-semibold text-emerald-700 dark:text-emerald-300 text-sm md:text-base">Bukti pembayaran berhasil diunggah</p>
                            <p class="text-xs text-emerald-600 dark:text-emerald-400">Tunggu konfirmasi admin (biasanya dalam 5-30 menit)</p>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button id="uploadBtn" onclick="uploadProof()"
                        class="w-full min-h-[44px] px-3 md:px-4 py-2 md:py-3 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-orange-300">
                    Upload Bukti Pembayaran
                </button>
            </x-card>
        </div>
    </div>

    <!-- Info Box -->
    <div class="mt-6 md:mt-8 bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/30 rounded-lg p-4 md:p-6 transition-colors">
        <h3 class="font-semibold text-blue-900 dark:text-blue-300 mb-3 text-base md:text-lg flex items-center gap-1"><x-icon name="clipboard" class="w-5 h-5" /> Petunjuk Pembayaran</h3>
        <ul class="text-xs md:text-sm text-blue-800 dark:text-blue-200 space-y-2">
            <li class="flex items-start gap-2"><x-icon name="check" class="w-4 h-4 mt-0.5 shrink-0" /> Buka aplikasi bank atau e-wallet kamu (GCash, Maya, BDO, etc)</li>
            <li class="flex items-start gap-2"><x-icon name="check" class="w-4 h-4 mt-0.5 shrink-0" /> Pilih fitur "Scan QR" atau "QRIS"</li>
            <li class="flex items-start gap-2"><x-icon name="check" class="w-4 h-4 mt-0.5 shrink-0" /> Scan kode QRIS di atas</li>
            <li class="flex items-start gap-2"><x-icon name="check" class="w-4 h-4 mt-0.5 shrink-0" /> Masukkan jumlah: <span class="font-semibold num">{{ format_price($order->total) }}</span></li>
            <li class="flex items-start gap-2"><x-icon name="check" class="w-4 h-4 mt-0.5 shrink-0" /> Selesaikan transaksi & ambil screenshot</li>
            <li class="flex items-start gap-2"><x-icon name="check" class="w-4 h-4 mt-0.5 shrink-0" /> Upload screenshot bukti pembayaran di bawah</li>
            <li class="flex items-start gap-2"><x-icon name="check" class="w-4 h-4 mt-0.5 shrink-0" /> Admin akan konfirmasi dalam beberapa menit</li>
        </ul>
    </div>
</div>

<script>
    const expiresAt = new Date('{{ $expiresAt->toIso8601String() }}');
    let selectedFile = null;

    // Countdown Timer
    function updateCountdown() {
        const now = new Date();
        const diff = expiresAt - now;

        if (diff <= 0) {
            document.getElementById('countdown').textContent = 'Waktu Habis';
            document.getElementById('countdown').classList.add('text-red-600');
            document.getElementById('uploadBtn').disabled = true;
            return;
        }

        const hours = Math.floor(diff / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);

        document.getElementById('countdown').textContent =
            String(hours).padStart(2, '0') + ':' +
            String(minutes).padStart(2, '0') + ':' +
            String(seconds).padStart(2, '0');

        if (hours === 0 && minutes < 15) {
            document.getElementById('countdown').parentElement.parentElement.classList.add('bg-red-50', 'border-red-200');
            document.getElementById('countdown').parentElement.parentElement.classList.remove('bg-amber-50', 'border-amber-200');
        }
    }

    setInterval(updateCountdown, 1000);
    updateCountdown();

    // File Upload Handler
    document.getElementById('proofInput').addEventListener('change', function(e) {
        selectedFile = e.target.files[0];
        if (selectedFile) {
            console.log('File selected: ' + selectedFile.name + ' (' + selectedFile.size + ' bytes)');
        }
    });

    function uploadProof() {
        if (!selectedFile) {
            window.toast('Silakan pilih file bukti pembayaran', 'info');
            return;
        }

        const formData = new FormData();
        formData.append('proof', selectedFile);

        document.getElementById('uploadBtn').disabled = true;
        document.getElementById('uploadBtn').textContent = 'Mengunggah...';

        fetch('{{ route("payment.upload-proof", $order) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token()}}',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('uploadStatus').classList.remove('hidden');
                selectedFile = null;
                document.getElementById('proofInput').value = '';
                document.getElementById('uploadBtn').textContent = 'Upload Bukti Pembayaran';
            } else {
                window.toast('Error: ' + data.message, 'error');
                document.getElementById('uploadBtn').disabled = false;
                document.getElementById('uploadBtn').textContent = 'Upload Bukti Pembayaran';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            window.toast('Gagal mengunggah file', 'error');
            document.getElementById('uploadBtn').disabled = false;
            document.getElementById('uploadBtn').textContent = 'Upload Bukti Pembayaran';
        });
    }

    // Poll payment status
    setInterval(function() {
        fetch('{{ route("payment.status", $order) }}')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'confirmed' || data.proof_status === 'verified') {
                    window.location.href = '{{ route("checkout.confirmation") }}';
                }
                if (data.is_expired) {
                    document.getElementById('uploadBtn').disabled = true;
                }
            });
    }, 5000); // Poll every 5 seconds
</script>
@endsection