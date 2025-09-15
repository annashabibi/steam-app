<x-app-layout>
    <x-page-title>Payment</x-page-title>

    <div class="container mt-5">
        <h3 class="mb-4">Pembayaran Transaksi</h3>

        <div class="card">
            <div class="card-body">
                {{-- Jika belum bayar --}}
                @unless($isPaid)
                    <p><strong>Nama Karyawan:</strong> {{ $transaction->karyawan->nama_karyawan }}</p>
                    <p><strong>Nama Motor:</strong> {{ $transaction->motor->nama_motor }}</p>
                    <hr class="mt-4">
                    <p><strong>Total Pembayaran:</strong> Rp{{ number_format($transaction->total, 0, ',', '.') }}</p>

                    <div class="text-center mt-4">
                        @if(!empty($qrUrl))
                            <p>Scan QR Code ini dengan aplikasi GoPay:</p>
                            <img id="gopay-qr" alt="GoPay QR Code" class="img-fluid" style="max-width:250px;">
                        @endif

                        @if(!empty($deeplinkUrl))
                            <p class="mt-3">Atau langsung buka aplikasi GoPay:</p>
                            <a href="{{ $deeplinkUrl }}" class="btn btn-success">Bayar dengan GoPay</a>
                        @endif
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

                <div class="mt-3">
                    <a href="{{ route('transactions.index') }}" class="btn btn-secondary ms-2">Kembali</a>
                </div>
            </div>
        </div>
    </div>

    {{-- JS load QR --}}
    @if(!empty($qrUrl))
        <script>
            document.addEventListener("DOMContentLoaded", async () => {
                try {
                    const res = await fetch("{{ $qrUrl }}");
                    const blob = await res.blob();
                    const imgUrl = URL.createObjectURL(blob);
                    document.getElementById("gopay-qr").src = imgUrl;
                } catch (err) {
                    console.error("Gagal load QR:", err);
                }
            });
        </script>
    @endif
</x-app-layout>
