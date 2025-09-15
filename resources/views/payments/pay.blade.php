<x-app-layout>
    {{-- Page Title --}}
    <x-page-title>Payment</x-page-title>

    <div class="container mt-5">
        <h3 class="mb-4">Pembayaran Transaksi</h3>

        <div class="card">
            <div class="card-body">
                {{-- Debug Information (hapus jika sudah OK) --}}
                @if(config('app.debug'))
                    <div class="alert alert-info">
                        <small>
                            <strong>Debug Info:</strong><br>
                            QR URL: {{ $qrUrl ?? 'NULL' }}<br>
                            Deeplink: {{ $deeplinkUrl ?? 'NULL' }}<br>
                            Error: {{ $errorMessage ?? 'None' }}
                        </small>
                    </div>
                @endif

                {{-- Jika belum bayar --}}
                @unless($isPaid)
                    <p><strong>Nama Karyawan:</strong> {{ $transaction->karyawan->nama_karyawan }}</p>
                    <p><strong>Nama Motor:</strong> {{ $transaction->motor->nama_motor }}</p>
                    <hr class="mt-4">
                    <p><strong>Total Pembayaran:</strong> Rp{{ number_format($transaction->total, 0, ',', '.') }}</p>

                    {{-- Error Message --}}
                    @if($errorMessage)
                        <div class="alert alert-danger">
                            {{ $errorMessage }}
                        </div>
                    @endif

                    {{-- Tampilkan QR & Link GoPay --}}
                    <div class="text-center mt-4">
                        @if(!empty($qrUrl))
                            <p>Scan QR Code ini dengan aplikasi GoPay:</p>
                            
                            {{-- Cek apakah QR URL adalah URL gambar atau string QR --}}
                            @if(filter_var($qrUrl, FILTER_VALIDATE_URL))
                                {{-- Jika URL gambar --}}
                                <img src="{{ $qrUrl }}" alt="GoPay QR Code" class="img-fluid" 
                                     style="max-width:250px;" 
                                     onerror="this.style.display='none'; document.getElementById('qr-fallback').style.display='block';">
                                
                                {{-- Fallback jika gambar gagal load --}}
                                <div id="qr-fallback" style="display:none;">
                                    <canvas id="qr-canvas" style="max-width:250px;"></canvas>
                                    <script>
                                        // Generate QR using JavaScript library
                                        if (typeof QRCode !== 'undefined') {
                                            const qr = new QRCode(document.getElementById('qr-canvas'), {
                                                text: "{{ $qrUrl }}",
                                                width: 250,
                                                height: 250
                                            });
                                        } else {
                                            document.getElementById('qr-fallback').innerHTML = 
                                                '<p class="text-muted">QR Code tidak dapat ditampilkan.<br>String QR: <code>{{ $qrUrl }}</code></p>';
                                        }
                                    </script>
                                </div>
                            @else
                                {{-- Jika string QR, generate menggunakan JavaScript --}}
                                <div id="qr-container">
                                    <canvas id="qr-canvas" style="max-width:250px;"></canvas>
                                </div>
                                <script>
                                    // Load QR Code generator jika belum ada
                                    if (typeof QRCode === 'undefined') {
                                        const script = document.createElement('script');
                                        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/qrcode/1.5.3/qrcode.min.js';
                                        script.onload = function() {
                                            generateQR();
                                        };
                                        document.head.appendChild(script);
                                    } else {
                                        generateQR();
                                    }

                                    function generateQR() {
                                        try {
                                            const qr = new QRCode(document.getElementById('qr-canvas'), {
                                                text: "{{ $qrUrl }}",
                                                width: 250,
                                                height: 250,
                                                colorDark: "#000000",
                                                colorLight: "#ffffff"
                                            });
                                        } catch (error) {
                                            document.getElementById('qr-container').innerHTML = 
                                                '<p class="text-muted">QR Code tidak dapat ditampilkan.<br>String: <small><code>{{ substr($qrUrl, 0, 50) }}...</code></small></p>';
                                        }
                                    }
                                </script>
                            @endif
                        @else
                            <div class="alert alert-warning">
                                <p>QR Code belum tersedia. Silakan refresh halaman atau coba lagi.</p>
                                <button onclick="window.location.reload();" class="btn btn-sm btn-warning">Refresh</button>
                            </div>
                        @endif

                        @if(!empty($deeplinkUrl))
                            <p class="mt-3">Atau langsung buka aplikasi GoPay:</p>
                            <a href="{{ $deeplinkUrl }}" class="btn btn-success">Bayar dengan GoPay</a>
                        @endif

                        {{-- Tombol refresh manual --}}
                        <div class="mt-3">
                            <small class="text-muted">QR Code tidak muncul?</small><br>
                            <button onclick="window.location.reload();" class="btn btn-sm btn-outline-primary">
                                Refresh Halaman
                            </button>
                        </div>
                    </div>
                @endunless

                {{-- Jika sudah dibayar --}}
                @if($isPaid)
                    <div class="alert alert-success mt-2">
                        <p><strong>Pembayaran sudah lunas.</strong></p>
                        <p><strong>Metode Pembayaran:</strong> {{ ucfirst($transaction->midtrans_payment_type ?? 'N/A') }}</p>
                        <p><strong>ID Transaksi:</strong> {{ $transaction->midtrans_transaction_id ?? '-' }}</p>
                        <p><strong>Status:</strong> {{ ucfirst($transaction->payment_status) }}</p>
                    </div>
                @endif

                {{-- Tombol kembali --}}
                <div class="mt-3">
                    <a href="{{ route('transactions.index') }}" class="btn btn-secondary ms-2">Kembali</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Load QR Code Library --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode/1.5.3/qrcode.min.js"></script>
</x-app-layout>