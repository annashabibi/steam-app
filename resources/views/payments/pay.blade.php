<x-app-layout>
    <x-page-title>Payment</x-page-title>

    <div class="container mt-5">
        <h3 class="mb-4">Pembayaran Transaksi</h3>

        <div class="card">
            <div class="card-body">
                @unless($isPaid)
                    <p><strong>Nama Karyawan:</strong> {{ $transaction->karyawan->nama_karyawan }}</p>
                    <p><strong>Nama Motor:</strong> {{ $transaction->motor->nama_motor }}</p>
                    <hr>
                    <p><strong>Total Pembayaran:</strong> Rp{{ number_format($transaction->total, 0, ',', '.') }}</p>

                    @if(!empty($errorMessage))
                        <div class="alert alert-danger">{{ $errorMessage }}</div>
                    @endif

                    <div class="text-center mt-4">
                        @if(!empty($qrCodeBase64))
                            {{-- SOLUSI UTAMA: Base64 QR Code --}}
                            <p>Scan QR Code dengan aplikasi GoPay:</p>
                            <img src="{{ $qrCodeBase64 }}" alt="GoPay QR Code" class="img-fluid" style="max-width:250px;">
                            
                        @elseif(!empty($qrUrl))
                            {{-- FALLBACK: Direct link --}}
                            <div class="alert alert-warning">
                                <p>QR Code tidak dapat dimuat otomatis.</p>
                                <a href="{{ $qrUrl }}" target="_blank" class="btn btn-primary">
                                    Buka QR Code GoPay
                                </a>
                            </div>
                            
                        @else
                            <div class="alert alert-danger">
                                QR Code tidak tersedia. Silakan refresh atau hubungi admin.
                            </div>
                        @endif

                        @if(!empty($deeplinkUrl))
                            <div class="mt-3">
                                <p>Atau buka langsung aplikasi GoPay:</p>
                                <a href="{{ $deeplinkUrl }}" class="btn btn-success">
                                    Bayar dengan GoPay
                                </a>
                            </div>
                        @endif
                        
                        <div class="mt-3">
                            <button onclick="location.reload()" class="btn btn-outline-secondary btn-sm">
                                Refresh
                            </button>
                        </div>
                    </div>
                @endunless

                @if($isPaid)
                    <div class="alert alert-success">
                        <h5>✅ Pembayaran Berhasil!</h5>
                        <p><strong>Metode:</strong> {{ ucfirst($transaction->midtrans_payment_type ?? 'GoPay') }}</p>
                        <p><strong>Status:</strong> {{ ucfirst($transaction->payment_status) }}</p>
                    </div>
                @endif

                <div class="mt-4">
                    <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    @unless($isPaid)
        <script>
            // Auto check status setiap 30 detik
            setInterval(function() {
                fetch("{{ route('payments.status', $transaction->id) }}")
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'paid') {
                            location.reload();
                        }
                    })
                    .catch(error => console.log('Status check failed'));
            }, 30000);
        </script>
    @endunless
</x-app-layout>