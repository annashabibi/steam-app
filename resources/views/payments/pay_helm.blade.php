<x-app-layout>
    <x-page-title>Pembayaran Helm</x-page-title>

    <style>
        .countdown-timer {
            background: #fed7d7;
            color: #c53030;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-block;
            margin-top: 16px;
            animation: softPulse 2s ease-in-out infinite;
        }

        .countdown-expired {
            background: #e2e8f0;
            color: #64748b;
            animation: none;
        }

        @keyframes softPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); }
        }

        .status-check-btn {
            background: #4299e1;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-left: 10px;
        }

        .status-check-btn:hover {
            background: #3182ce;
            transform: translateY(-1px);
        }

        .status-check-btn:disabled {
            background: #a0aec0;
            cursor: not-allowed;
            transform: none;
        }

        .loading-spinner {
            border: 2px solid #f3f3f3;
            border-top: 2px solid #3498db;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            animation: spin 1s linear infinite;
            display: inline-block;
            margin-right: 8px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>

    <div class="container mt-5">
        <h3>Transaksi Helm</h3>

        <div class="card mt-3">
            <div class="card-body">
                @if(in_array(strtolower($helm_transaction->payment_status), ['settlement','capture','paid']))
                    <div class="alert alert-success mt-3">
                        Pembayaran berhasil
                        Metode: {{ ucfirst($helm_transaction->midtrans_payment_type ?? '-') }}<br>
                        ID Transaksi: {{ $helm_transaction->midtrans_transaction_id ?? '-' }}
                    </div>
                @else
                    <div class="text-center mt-4">
                        <h5>Scan QRIS</h5>
                        @if($deeplink)
                            <div class="text-center">
                                {!! QrCode::size(250)->generate($deeplink) !!}
                            </div>
                            {{-- Logo QRIS --}}
                            <div class="mt-1">
                                <img src="{{ asset('images/qris.png') }}" alt="QRIS Logo" style="max-width: 140px;">
                            </div>
                            <div>
                                <div id="countdown" class="countdown-timer"></div>
                            </div>
                            <div class="mt-3">
                                <button id="checkStatusBtn" class="status-check-btn" onclick="checkPaymentStatus()">
                                    Cek Status Pembayaran
                                </button>
                            </div>
                        @else
                            <div class="alert alert-danger mt-3">
                                Gagal memuat QR Code
                            </div>
                        @endif
                    </div>
                @endif

                <div class="mt-4">
                    <a href="{{ route('helms.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>

    {{-- <script>
        document.addEventListener('DOMContentLoaded', function () {
            let countdownEl = document.getElementById("countdown");
            @if($time_qr)
                let expiredTime = new Date("{{ $time_qr }}").getTime();
            @else
                let expiredTime = new Date().getTime() + (15*60*1000);
            @endif

            if(countdownEl){
                let timer = setInterval(function(){
                    let now = new Date().getTime();
                    let distance = expiredTime - now;
                    if(distance <= 0){
                        clearInterval(timer);
                        countdownEl.innerHTML = "⏰ Waktu pembayaran sudah habis";
                        countdownEl.classList.add("countdown-expired");
                        let checkBtn = document.getElementById("checkStatusBtn");
                        if(checkBtn){
                            checkBtn.disabled = true;
                            checkBtn.innerHTML = "Waktu Habis";
                        }
                        return;
                    }
                    let minutes = Math.floor((distance % (1000*60*60))/(1000*60));
                    let seconds = Math.floor((distance % (1000*60))/1000);
                    countdownEl.innerHTML = `⏱️ Sisa waktu: ${minutes}m ${seconds}s`;
                },1000);
            }
        });

        function checkPaymentStatus(){
            const checkBtn = document.getElementById("checkStatusBtn");
            checkBtn.disabled = true;
            checkBtn.innerHTML = `<span class="loading-spinner"></span> Memeriksa...`;

            fetch("{{ route('payment.helm.checkStatus', $helm_transaction->id) }}")
            .then(res=>res.json())
            .then(data=>{
                if(data.status==='settlement'||data.status==='capture'||data.status==='paid'){
                    Swal.fire({
                        icon:'success',
                        title:'Success',
                        text:'Transaksi berhasil disimpan.',
                        timer:2000,
                        showConfirmButton:false,
                        willClose:()=>window.location.href="{{ route('helms.index') }}"
                    });
                    clearInterval(autoCheck);
                } else if(data.status==='pending'){
                    Swal.fire({icon:'info',title:'Pembayaran Pending',text:'Silakan selesaikan pembayaran.',timer:2000,showConfirmButton:false});
                    checkBtn.disabled = false;
                    checkBtn.innerHTML="Cek Status Pembayaran";
                } else {
                    Swal.fire({icon:'error',title:'Gagal',text:`Status: ${data.status}`,timer:2500,showConfirmButton:true});
                    checkBtn.disabled = false;
                    checkBtn.innerHTML="Cek Status Pembayaran";
                    clearInterval(autoCheck);
                }
            }).catch(err=>{
                Swal.fire({icon:'warning',title:'Gagal Cek Status',text:'Terjadi kesalahan',showConfirmButton:true});
                checkBtn.disabled = false;
                checkBtn.innerHTML="Cek Status Pembayaran";
                console.error(err);
            });
        }

        let autoCheck = setInterval(checkPaymentStatus,20000);
    </script> --}}
</x-app-layout>
