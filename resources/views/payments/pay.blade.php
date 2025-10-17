<x-app-layout>
    <x-page-title>Payment</x-page-title>
    <div class="container mt-5">
        <h3>Pembayaran Transaksi</h3>

        <div class="card mt-3">
            <div class="card-body">
                {{-- Status pembayaran --}}
                @if(in_array($transaction->payment_status, ['settlement', 'capture', 'paid']))
                    <div class="alert alert-success mt-3">
                        Pembayaran berhasil
                    </div>
                @else
                    <div class="mt-4 text-center">
                        <h5>Scan QRIS</h5>
                        @if($deeplink)
                            {{-- QR Code dari deeplink --}}
                            {!! QrCode::size(250)->generate($deeplink) !!}
                            {{-- Countdown Timer --}}
                            <div class="mt-2">
                                <div id="countdown" class="countdown"></div>
                            </div>
                            
                            <div class="mt-3">
                                
                            {{-- Check Status  --}}
                            <button class="refresh-button checkStatusBtn" id="checkStatusBtn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" class="svg-icon">
                                    <path stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 12a9 9 0 1 0 3-6.708M3 3v4h4"/>
                                </svg>
                                <span class="label">Cek Status</span>
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
    const countdownEl = document.getElementById("countdown");

    @if($transaction->expiry_time)
        const expiredTime = new Date("{{ $transaction->expiry_time }}").getTime();
    @else
        const expiredTime = new Date().getTime() + (15 * 60 * 1000);
    @endif

    if (countdownEl) {
        countdownEl.classList.add("countdown-red");

        const timer = setInterval(() => {
            const now = new Date().getTime();
            const distance = expiredTime - now;

            if (distance <= 0) {
                clearInterval(timer);
                countdownEl.classList.remove("countdown-red");
                countdownEl.classList.add("countdown-expired");
                countdownEl.innerHTML = "⏰ Waktu pembayaran sudah habis";

                const checkBtn = document.getElementById("checkStatusBtn");
                if (checkBtn) {
                    checkBtn.disabled = true;
                    checkBtn.querySelector(".lable").textContent = "Waktu Habis";
                }
                return;
            }

            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            countdownEl.innerHTML = `Sisa waktu: ${minutes}m ${seconds}s`;
        }, 1000);
    }
});
    // Tambahkan variabel manual atau auto
    let isManualCheck = false;

    function checkPaymentStatus() {
    const checkBtn = document.getElementById("checkStatusBtn");
    const label = checkBtn.querySelector(".label");
    const icon = checkBtn.querySelector(".svg-icon");

    // loading state
    checkBtn.disabled = true;
    checkBtn.classList.add("loading");
    label.textContent = "Memeriksa...";

    fetch("{{ route('payment.checkStatus', $transaction->id) }}")
        .then(response => response.json())
        .then(data => {
            if (data.status === 'settlement' || data.status === 'capture' || data.status === 'paid') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Transaksi berhasil disimpan.',
                    timer: 2000,
                    showConfirmButton: false,
                    willClose: () => {
                        window.location.href = "{{ route('transactions.index') }}";
                    }
                });
                clearInterval(autoCheck);
            } else if (data.status === 'pending') {
                if (isManualCheck) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Pembayaran Pending',
                        text: 'Silakan selesaikan pembayaran.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
                checkBtn.disabled = false;
                label.textContent = "Cek Status";
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Pembayaran Gagal',
                    text: `Status: ${data.status}`,
                    timer: 2500,
                    showConfirmButton: true
                });
                clearInterval(autoCheck);
                checkBtn.disabled = false;
                label.textContent = "Cek Status";
            }
        })
        .catch(err => {
            if (isManualCheck) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Gagal Cek Status',
                    text: 'Terjadi kesalahan. Silakan coba lagi.',
                    showConfirmButton: true
                });
            }
            checkBtn.disabled = false;
            label.textContent = "Cek Status";
            console.error(err);
        })
        .finally(() => {
            checkBtn.classList.remove("loading");
            isManualCheck = false;
        });
}

document.getElementById("checkStatusBtn").addEventListener('click', function() {
    isManualCheck = true;
    checkPaymentStatus();
});

let autoCheck = setInterval(function() {
    isManualCheck = false;
    checkPaymentStatus();
}, 20000);
    </script>
</x-app-layout>