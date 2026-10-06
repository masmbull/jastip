@extends('layouts.app')
@section('title', 'Menunggu Pembayaran - ' . $order->order_number)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl md:text-4xl font-bold text-[#1E293B] mb-8 text-center">Selesaikan Pembayaran</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- QRIS Section -->
        <div class="bg-white rounded-xl shadow-lg p-6 space-y-4">
            <h2 class="text-2xl font-bold text-[#1E293B]">Scan QRIS</h2>
            <p class="text-[#64748B]">Scan dengan aplikasi perbankan atau e-wallet kamu</p>

            <!-- QRIS Image -->
            <div class="bg-[#F1F5F9] rounded-lg p-4 flex items-center justify-center">
                @if($qrisImage)
                    <img src="{{ $qrisImage }}" alt="QRIS" class="max-w-sm w-full" id="qrisImage">
                @else
                    <p class="text-[#94A3B8] text-center">QRIS tidak tersedia</p>
                @endif
            </div>

            <!-- Download Button -->
            <a href="{{ route('payment.download-qris', $order) }}"
               class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Download QRIS
            </a>

            <!-- Merchant Info -->
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                <p class="text-sm text-[#64748B] mb-1">Atas nama</p>
                <p class="font-semibold text-[#1E293B]">{{ $qrisMerchant }}</p>
            </div>

            <!-- Amount -->
            <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4">
                <p class="text-sm text-[#64748B] mb-1">Total yang harus dibayar</p>
                <p class="text-3xl font-bold text-emerald-600">
                    {{ format_price($order->total) }}
                </p>
            </div>
        </div>

        <!-- Order Summary & Upload Section -->
        <div class="space-y-6">
            <!-- Order Summary -->
            <div class="bg-white rounded-xl shadow-lg p-6 space-y-4">
                <h2 class="text-xl font-bold text-[#1E293B]">Ringkasan Pesanan</h2>

                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-[#64748B]">Nomor Pesanan</span>
                        <span class="font-semibold text-[#1E293B]">{{ $order->order_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#64748B]">Subtotal</span>
                        <span class="font-medium">{{ format_price($order->subtotal) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#64748B]">Ongkir</span>
                        <span class="font-medium">{{ format_price($order->shipping_cost) }}</span>
                    </div>
                    @if($order->fee > 0)
                        <div class="flex justify-between">
                            <span class="text-[#64748B]">Biaya Fee</span>
                            <span class="font-medium">{{ format_price($order->fee) }}</span>
                        </div>
                    @endif
                    <div class="border-t border-[#E2E8F0] pt-2 flex justify-between">
                        <span class="font-semibold text-[#1E293B]">Total</span>
                        <span class="text-lg font-bold text-orange-600">{{ format_price($order->total) }}</span>
                    </div>
                </div>

                <!-- Countdown Timer -->
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <p class="text-sm text-[#64748B] mb-2">⏱️ Waktu pembayaran</p>
                    <div class="flex items-baseline gap-2">
                        <p class="text-2xl font-bold text-amber-600" id="countdown">--:--:--</p>
                        <span class="text-xs text-[#94A3B8]">sisa waktu</span>
                    </div>
                    <p class="text-xs text-[#94A3B8] mt-2">Pembayaran harus selesai sebelum waktu habis</p>
                </div>
            </div>

            <!-- Upload Proof Section -->
            <div class="bg-white rounded-xl shadow-lg p-6 space-y-4">
                <h2 class="text-xl font-bold text-[#1E293B]">Unggah Bukti Pembayaran</h2>
                <p class="text-sm text-[#64748B]">Screenshot atau foto struk pembayaran QRIS kamu</p>

                <!-- Upload Area -->
                <div class="border-2 border-dashed border-[#E2E8F0] rounded-lg p-6 text-center hover:border-orange-300 transition-colors cursor-pointer"
                     onclick="document.getElementById('proofInput').click()">
                    <svg class="w-12 h-12 text-[#94A3B8] mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <p class="font-medium text-[#1E293B]">Klik untuk upload atau drag file di sini</p>
                    <p class="text-xs text-[#94A3B8] mt-1">JPG, PNG (Max 5MB)</p>
                </div>

                <input type="file" id="proofInput" accept="image/jpeg,image/png" class="hidden">

                <!-- Upload Status -->
                <div id="uploadStatus" class="hidden space-y-2">
                    <div class="flex items-center gap-2 p-3 bg-emerald-50 border border-emerald-200 rounded-lg">
                        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <p class="font-semibold text-emerald-700">Bukti pembayaran berhasil diunggah</p>
                            <p class="text-xs text-emerald-600">Tunggu konfirmasi admin (biasanya dalam 5-30 menit)</p>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button id="uploadBtn" onclick="uploadProof()"
                        class="w-full px-4 py-3 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    Upload Bukti Pembayaran
                </button>
            </div>
        </div>
    </div>

    <!-- Info Box -->
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="font-semibold text-blue-900 mb-3">📋 Petunjuk Pembayaran</h3>
        <ul class="text-sm text-blue-800 space-y-2">
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
            alert('Silakan pilih file bukti pembayaran');
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
                alert('Error: ' + data.message);
                document.getElementById('uploadBtn').disabled = false;
                document.getElementById('uploadBtn').textContent = 'Upload Bukti Pembayaran';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal mengunggah file');
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
