<x-app-layout>
    <x-page-title>Payment</x-page-title>

    <div class="container mt-5">
        <h3>Pembayaran Transaksi</h3>

        <div class="card mt-3">
            <div class="card-body">
                {{-- <p><strong>Nama Karyawan:</strong> {{ $transaction->karyawan->nama_karyawan }}</p>
                <p><strong>Motor:</strong> {{ $transaction->motor->nama_motor }}</p>
                <p><strong>Total:</strong> Rp{{ number_format($transaction->total, 0, ',', '.') }}</p> --}}

                {{-- Status pembayaran --}}
                @if(in_array($transaction->payment_status, ['settlement', 'capture']))
                    <div class="alert alert-success mt-3">
                        Pembayaran berhasil ✅
                    </div>
                @else
                    <div class="mt-4 text-center">
                        <h5>Scan QR Code</h5>
                        @if($deeplink)
                            {{-- QR Code dari deeplink --}}
                            {!! QrCode::size(250)->generate($deeplink) !!}
                            
                            {{-- Countdown Timer --}}
                            <div>
                                <div id="countdown" class="countdown-timer"></div>
                            </div>
                            
                            <div class="mt-3">
                                
                                {{-- Check Status Button --}}
                                <button id="checkStatusBtn" class="status-check-btn" onclick="checkPaymentStatus()">
                                    🔄 Cek Status Pembayaran
                                </button>
                            </div>
                            
                        @else
                            <div class="alert alert-danger">
                                Gagal memuat QR Code.
                            </div>
                        @endif
                    </div>
                @endif

                <div class="mt-4">
                    <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let countdownEl = document.getElementById("countdown");
            
            // expiry_time dilempar dari controller, format timestamp (UTC)
            @if($transaction->expiry_time)
                let expiredTime = new Date("{{ $transaction->expiry_time }}").getTime();
            @else
                // fallback manual 15 menit kalau expiry_time belum ada
                let expiredTime = new Date().getTime() + (15 * 60 * 1000);
            @endif

            if (countdownEl) {
                let timer = setInterval(function () {
                    let now = new Date().getTime();
                    let distance = expiredTime - now;

                    if (distance <= 0) {
                        clearInterval(timer);
                        countdownEl.innerHTML = "⏰ Waktu pembayaran sudah habis";
                        countdownEl.classList.add("countdown-expired");
                        
                        let checkBtn = document.getElementById("checkStatusBtn");
                        if (checkBtn) {
                            checkBtn.disabled = true;
                            checkBtn.innerHTML = "Waktu Habis";
                        }
                        return;
                    }

                    let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    let seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    countdownEl.innerHTML = `⏱️ Sisa waktu: ${minutes}m ${seconds}s`;
                }, 1000);
            }
        });

        function checkPaymentStatus() {
            // Bisa bikin fetch/ajax ke endpoint untuk cek status transaksi
            alert("🔍 Fitur cek status pembayaran bisa ditambahkan ke sini.");
        }
    </script>
</x-app-layout>
