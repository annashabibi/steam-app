<x-app-layout>
    <x-page-title>Detail Transaksi</x-page-title>

    <div class="container mt-5">
        <h3 class="mb-4">Detail Transaksi</h3>

        <div class="card">
            <div class="card-body">
                <div class="alert alert-success mt-2">
                <p><strong>Pembayaran sudah lunas.</strong></p>
                <p><strong>Metode Pembayaran:</strong> {{ ucfirst($transaction->payment_method) }}</p>
                <p><strong>Status:</strong> {{ ucfirst($transaction->payment_status) }}</p>
                </div>
            </div>
        </div>

        <a href="{{ route('transactions.index') }}" class="btn btn-secondary mt-4">Kembali</a>
    </div>
</x-app-layout>
