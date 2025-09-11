<x-app-layout>
    {{-- Page Title --}}
    <x-page-title>Payment</x-page-title>

    <div class="container mt-5">
        <h3 class="mb-4">Pembayaran Transaksi</h3>

        <div class="card">
            <div class="card-body">
                {{-- Jika belum bayar, tampilkan detail --}}
                @unless($isPaid)
                    <p><strong>Nama Karyawan:</strong> {{ $transaction->karyawan->nama_karyawan }}</p>
                    <p><strong>Nama Motor:</strong> {{ $transaction->motor->nama_motor }}</p>
                    <hr class="mt-4">
                    <p><strong>Total Pembayaran:</strong> Rp{{ number_format($transaction->total, 0, ',', '.') }}</p>
                    {{-- Status --}}
                    <div id="paymentStatus" class="mt-3"></div>
                @endunless

                {{-- Status Pembayaran --}}
                @if($isPaid)
                    <div class="alert alert-success mt-2">
                        <p><strong>Pembayaran sudah lunas.</strong></p>
                        <p><strong>Metode Pembayaran:</strong> {{ ucfirst($transaction->midtrans_payment_type ?? 'N/A') }} </p>
                        <p><strong>ID Transaksi:</strong> {{ $transaction->midtrans_transaction_id ?? '-' }} </p>
                        <p><strong>Status:</strong> {{ ucfirst($transaction->payment_status) }} </p>
                    </div>
                @else
                    <div id="snap-container" class="mt-4">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary mb-3" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p>Sedang memuat metode pembayaran...</p>
                            <button id="btnOnline" class="btn btn-primary">Bayar Online</button>
                        </div>
                    </div>
                @endif

                {{-- Tombol kembali --}}
                <div class="mt-3">
                    <a href="{{ route('transactions.index') }}" class="btn btn-secondary ms-2">Kembali</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Midtrans Snap.js --}}
    <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const payBtn = document.getElementById('btnOnline');
        if(!payBtn) return;

        let currentSnapToken = '{{ $snapToken }}';

        function showStatus(message, type) {
            const statusDiv = document.getElementById('paymentStatus');
            statusDiv.innerHTML = `<div class="alert alert-${type} mb-0">${message}</div>`;
        }

        payBtn.addEventListener('click', function () {
            if (!currentSnapToken) {
                alert('Token pembayaran belum tersedia.');
                return;
            }

            showStatus('Membuka halaman pembayaran...', 'info');

            snap.pay(currentSnapToken, {
                onSuccess: function(result) {
                    showStatus('Pembayaran berhasil!', 'success');
                    setTimeout(() => {
                        window.location.href = "{{ route('transactions.index') }}";
                    }, 2000);
                },
                onPending: function(result) {
                    showStatus('Menunggu pembayaran Anda...', 'warning');
                },
                onError: function(result) {
                    showStatus('Pembayaran gagal!', 'danger');
                    console.error(result);
                },
                onClose: function() {
                    showStatus('Transaksi dibatalkan.', 'danger');
                }
            });
        });
    });
    </script>
</x-app-layout>
