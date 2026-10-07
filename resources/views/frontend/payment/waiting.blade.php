@extends('layouts.app')
@section('title', 'Menunggu Pembayaran - ' . $order->order_number)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-6 md:mb-8 text-center">Selesaikan Pembayaran</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
        <!-- QRIS Section -->
        <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-lg p-4 md:p-6 space-y-4 transition-colors">
            <h2 class="text-xl md:text-2xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">Scan QRIS</h2>
            <p class="text-sm md:text-base text-[#64748B] dark:text-[#cbd5e1]">Scan dengan aplikasi perbankan atau e-wallet kamu</p>

            <!-- QRIS Image -->
            <div class="bg-[#F1F5F9] dark:bg-[#2e323b] rounded-lg p-4 flex items-center justify-center transition-colors">
                @if($qrisImage)
                    <img src="{{ $qrisImage }}" alt="QRIS" class="max-w-sm w-full rounded" id="qrisImage">
                @else
                    <p class="text-[#94A3B8] dark:text-[#cbd5e1] text-center text-sm md:text-base">QRIS tidak tersedia</p>
                @endif
            </div>

            <!-- Download Button -->
            <a href="{{ route('payment.download-qris', $order) }}"
               class="w-full flex items-center justify-center gap-2 px-3 md:px-4 py-2 md:py-3 bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-white rounded-lg font-medium transition-colors text-sm md:text-base">
                <svg class="w-4 md:w-5 h-4 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
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
                <p class="text-2xl md:text-3xl font-bold text-emerald-600 dark:text-emerald-400">
                    {{ format_price($order->total) }}
                </p>
            </div>
        </div>

        <!-- Order Summary & Upload Section -->
        <div class="space-y-4 md:space-y-6">
            <!-- Order Summary -->
            <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-lg p-4 md:p-6 space-y-4 transition-colors">
                <h2 class="text-lg md:text-xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">Ringkasan Pesanan</h2>

                <div class="space-y-2 text-xs md:text-sm">
                    <div class="flex justify-between text-[#64748B] dark:text-[#cbd5e1]">
                        <span>Nomor Pesanan</span>
                        <span class="font-semibold text-[#1E293B] dark:text-[#f1f5f9]">{{ $order->order_number }}</span>
                    </div>
                    <div class="flex justify-between text-[#64748B] dark:text-[#cbd5e1]">
                        <span>Subtotal</span>
                        <span class="font-medium text-[#1E293B] dark:text-[#f1f5f9]">{{ format_price($order->subtotal) }}</span>
                    </div>
                    <div class="flex justify-between text-[#64748B] dark:text-[#cbd5e1]">
                        <span>Ongkir</span>
                        <span class="font-medium text-[#1E293B] dark:text-[#f1f5f9]">{{ format_price($order->shipping_cost) }}</span>
                    </div>
                    @if($order->fee > 0)
                        <div class="flex justify-between text-[#64748B] dark:text-[#cbd5e1]">
                            <span>Biaya Fee</span>
                            <span class="font-medium text-[#1E293B] dark:text-[#f1f5f9]">{{ format_price($order->fee) }}</span>
                        </div>
                    @endif
                    <div class="border-t border-[#E2E8F0] dark:border-[#404854] pt-2 flex justify-between">
                        <span class="font-semibold text-[#1E293B] dark:text-[#f1f5f9]">Total</span>
                        <span class="text-base md:text-lg font-bold text-orange-600 dark:text-orange-400">{{ format_price($order->total) }}</span>
                    </div>
                </div>

                <!-- Countdown Timer -->
                <div class="bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 rounded-lg p-3 md:p-4 transition-colors">
                    <p class="text-xs md:text-sm text-[#64748B] dark:text-[#cbd5e1] mb-2">⏱️ Waktu pembayaran</p>
                    <div class="flex items-baseline gap-2">
                        <p class="text-2xl font-bold text-amber-600" id="countdown">--:--:--</p>
                        <span class="text-xs text-[#94A3B8]">sisa waktu</span>
                    </div>
                    <p class="text-xs text-[#94A3B8] mt-2">Pembayaran harus selesai sebelum waktu habis</p>
                </div>
            </div>

            <!-- Upload Proof Section -->
            <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-lg p-4 md:p-6 space-y-4 transition-colors">
                <h2 class="text-lg md:text-xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">Unggah Bukti Pembayaran</h2>
                <p class="text-xs md:text-sm text-[#64748B] dark:text-[#cbd5e1]">Screenshot atau foto struk pembayaran QRIS kamu</p>

                <!-- Upload Area -->
                <div class="border-2 border-dashed border-[#E2E8F0] dark:border-[#404854] rounded-lg p-4 md:p-6 text-center hover:border-orange-300 dark:hover:border-orange-500/50 transition-colors cursor-pointer bg-[#F8FAFC] dark:bg-[#1a1c22]"
                     onclick="document.getElementById('proofInput').click()">
                    <svg class="w-10 md:w-12 h-10 md:h-12 text-[#94A3B8] dark:text-[#64748B] mx-auto mb-2 md:mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <p class="font-medium text-[#1E293B] dark:text-[#f1f5f9] text-sm md:text-base">Klik untuk upload atau drag file di sini</p>
                    <p class="text-xs text-[#94A3B8] dark:text-[#64748B] mt-1">JPG, PNG (Max 5MB)</p>
                </div>

                <input type="file" id="proofInput" accept="image/jpeg,image/png" class="hidden">

                <!-- Upload Status -->
                <div id="uploadStatus" class="hidden space-y-2">
                    <div class="flex items-center gap-2 p-3 md:p-4 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 rounded-lg transition-colors">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <p class="font-semibold text-emerald-700 dark:text-emerald-300 text-sm md:text-base">Bukti pembayaran berhasil diunggah</p>
                            <p class="text-xs text-emerald-600 dark:text-emerald-400">Tunggu konfirmasi admin (biasanya dalam 5-30 menit)</p>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button id="uploadBtn" onclick="uploadProof()"
                        class="w-full px-3 md:px-4 py-2 md:py-3 bg-orange-500 hover:bg-orange-600 dark:bg-orange-600 dark:hover:bg-orange-700 text-white font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed text-sm md:text-base">
                    Upload Bukti Pembayaran
                </button>
            </div>
        </div>
    </div>

    <!-- Info Box -->
    <div class="mt-6 md:mt-8 bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/30 rounded-lg p-4 md:p-6 transition-colors">
        <h3 class="font-semibold text-blue-900 dark:text-blue-300 mb-3 text-base md:text-lg">📋 Petunjuk Pembayaran</h3>
        <ul class="text-xs md:text-sm text-blue-800 dark:text-blue-200 space-y-2">
            <li>✓ Buka aplikasi bank atau e-wallet kamu (GCash, Maya, BDO, etc)</li>
            <li>✓ Pilih fitur "Scan QR" atau "QRIS"</li>
            <li>✓ Scan kode QRIS di atas</li>
            <li>✓ Masukkan jumlah: <span class="font-semibold">{{ format_price($order->total) }}</span></li>
            <li>✓ Selesaikan transaksi & ambil screenshot</li>
            <li>✓ Upload screenshot bukti pembayaran di bawah</li>
            <li>✓ Admin akan konfirmasi dalam beberapa menit</li>
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
