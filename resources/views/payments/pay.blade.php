<x-app-layout>
    <x-page-title>Pembayaran GoPay</x-page-title>

    <div class="container mt-5 text-center">
        <h3 class="mb-3">Scan QR Code GoPay</h3>
        <p>Total: Rp{{ number_format($transaction->total, 0, ',', '.') }}</p>

        <div class="card p-4">
            <img src="{{ $qrUrl }}" alt="QR Code GoPay" class="mx-auto" style="max-width:300px;">
        </div>

        <div class="mt-4">
            <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
</x-app-layout>
