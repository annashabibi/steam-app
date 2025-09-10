<x-app-layout>
    <x-page-title>Payment GoPay</x-page-title>

    <div class="container mt-5 text-center">
        <h3 class="mb-4">Pembayaran Transaksi</h3>

        @unless($isPaid)
            <p><strong>Nama Karyawan:</strong> {{ $transaction->karyawan->nama_karyawan }}</p>
            <p><strong>Nama Motor:</strong> {{ $transaction->motor->nama_motor }}</p>
            <hr class="mt-4">
            <p><strong>Total Pembayaran:</strong> Rp{{ number_format($transaction->total, 0, ',', '.') }}</p>

            <div id="qrcode" class="my-4 d-flex justify-content-center"></div>
            <p>Silakan scan QR Code ini dengan aplikasi <strong>GoPay</strong> Anda.</p>
        @else
            <div class="alert alert-success mt-2">
                <p><strong>Pembayaran sudah lunas.</strong></p>
                <p><strong>Metode Pembayaran:</strong> {{ ucfirst($transaction->midtrans_payment_type ?? 'N/A') }} </p>
                <p><strong>ID Transaksi:</strong> {{ $transaction->midtrans_transaction_id ?? '-' }} </p>
                <p><strong>Status:</strong> {{ ucfirst($transaction->payment_status) }} </p>
            </div>
        @endunless

        <div class="mt-3">
            <a href="{{ route('transactions.index') }}" class="btn btn-secondary ms-2">Kembali</a>
        </div>
    </div>

    {{-- Library QRCode --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        @if(!$isPaid && $qrUrl)
            new QRCode(document.getElementById("qrcode"), "{{ $qrUrl }}");
        @endif
    </script>
</x-app-layout>
