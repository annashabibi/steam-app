<x-app-layout>
    <x-page-title>Payment</x-page-title>

    <div class="container mt-5">
        <h3 class="mb-4">Pembayaran Transaksi</h3>

        <div class="card">
            <div class="card-body">
                <p><strong>Karyawan:</strong> {{ $transaction->karyawan->nama_karyawan }}</p>
                <p><strong>Motor:</strong> {{ $transaction->motor->nama_motor }}</p>
                <p><strong>Total:</strong> Rp{{ number_format($transaction->total, 0, ',', '.') }}</p>

                @if($gopayQrUrl)
                    <div class="text-center mt-4">
                        <p>Scan QR untuk bayar via GoPay:</p>
                        <img src="{{ $gopayQrUrl }}" alt="QR GoPay" style="max-width: 300px;">
                    </div>
                @else
                    <div class="alert alert-warning">QR Code belum tersedia.</div>
                @endif

                <div class="mt-3">
                    <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>