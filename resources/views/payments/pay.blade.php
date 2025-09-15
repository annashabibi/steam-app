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

                    <div class="text-center mt-4">
                        @if(!empty($qrUrl))
                            <p>Scan QR Code ini dengan aplikasi GoPay:</p>
                            <img src="{{ $qrUrl }}" alt="GoPay QR" class="img-fluid" style="max-width:250px;">
                        @else
                            <p>QR Code belum tersedia, silakan coba lagi nanti.</p>
                        @endif

                        @if(!empty($deeplinkUrl))
                            <p class="mt-3">Atau langsung buka aplikasi GoPay:</p>
                            <a href="{{ $deeplinkUrl }}" class="btn btn-success">Bayar dengan GoPay</a>
                        @endif

                        @if(!empty($errorMessage))
                            <div class="alert alert-danger mt-3">{{ $errorMessage }}</div>
                        @endif
                    </div>
                @endunless

                @if($isPaid)
                    <div class="alert alert-success mt-2">
                        <p><strong>Pembayaran sudah lunas ✅</strong></p>
                        <p><strong>Metode Pembayaran:</strong> {{ ucfirst($transaction->midtrans_payment_type ?? 'N/A') }}</p>
                        <p><strong>ID Transaksi:</strong> {{ $transaction->midtrans_transaction_id ?? '-' }}</p>
                        <p><strong>Status:</strong> {{ ucfirst($transaction->payment_status) }}</p>
                    </div>
                @endif

                <div class="mt-3">
                    <a href="{{ route('transactions.index') }}" class="btn btn-secondary ms-2">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
