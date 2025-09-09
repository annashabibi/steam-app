<x-app-layout>
    <x-page-title>Payment</x-page-title>

    <div class="container mt-5">
        <h3 class="mb-4">Pembayaran Transaksi</h3>

        <div class="card">
            <div class="card-body">
                @unless($isPaid)
                    <p><strong>Nama Karyawan:</strong> {{ $transaction->karyawan->nama_karyawan }}</p>
                    <p><strong>Nama Motor:</strong> {{ $transaction->motor->nama_motor }}</p>
                    <hr class="mt-4">
                    <p><strong>Total Pembayaran:</strong> Rp{{ number_format($transaction->total, 0, ',', '.') }}</p>

                    {{-- Tampilkan QR Code GoPay --}}
                    @if(!empty($gopayQrUrl))
                        <div class="text-center mt-4">
                            <p><strong>Scan QR untuk bayar dengan GoPay:</strong></p>
                            <img src="{{ $gopayQrUrl }}" alt="GoPay QR Code" style="max-width:300px;">
                        </div>
                    @else
                        <div class="alert alert-warning">QR Code belum tersedia, silakan coba lagi.</div>
                    @endif
                @endunless

                {{-- Status Pembayaran --}}
                @if($isPaid)
                    <div class="alert alert-success mt-2">
                        <p><strong>Pembayaran sudah lunas.</strong></p>
                        <p><strong>Metode Pembayaran:</strong> {{ ucfirst($transaction->midtrans_payment_type ?? 'N/A') }}</p>
                        <p><strong>ID Transaksi:</strong> {{ $transaction->midtrans_transaction_id ?? '-' }}</p>
                        <p><strong>Status:</strong> {{ ucfirst($transaction->payment_status) }}</p>
                    </div>
                @else
                    <div id="paymentStatus" class="mt-3">
                        <div class="alert alert-warning">Menunggu pembayaran melalui QR Code...</div>
                    </div>
                @endif

                <div class="mt-3">
                    <a href="{{ route('transactions.index') }}" class="btn btn-secondary ms-2">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
