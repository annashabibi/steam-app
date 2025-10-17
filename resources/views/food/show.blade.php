<x-app-layout>
    {{-- Page Title --}}
    <x-page-title>Detail Product</x-page-title>

    <div class="bg-white rounded-2 shadow-sm p-4 mb-5">
        {{-- tampilkan detail data --}}
        <div class="row flex-lg-row align-items-center g-5">
            <div class="col-lg-3">
                @if ($food->image)
                    <img src="{{ $food->image }}" class="d-block mx-lg-auto img-thumbnail rounded-4 shadow-sm" alt="{{ $food->nama_produk }}" loading="lazy">
                @else
                    <img src="https://via.placeholder.com/160" class="d-block mx-lg-auto img-thumbnail rounded-4 shadow-sm" alt="No Image">
                @endif
            </div>
            <div class="col-lg-9">
                <h4>{{ $food->nama_produk }}</h4>
                <p class="text-muted">
                    <i class="ti ti-cup me-1"></i> {{ ucfirst($food->category) }}
                </p>
                <p class="text-success fw-bold">
                    {{ 'Rp ' . number_format($food->harga, 0, '', '.') }}
                </p>
                <p class="mb-0">
                    <i class="ti ti-package me-1"></i> Stok: {{ $food->qty }}
                </p>
                <p class="mt-1">
                    <i class="ti ti-info-circle me-1"></i>
                    Status:
                    @if ($food->qty > 0)
                        <span class="text-success fw-semibold">Tersedia</span>
                    @else
                        <span class="text-danger fw-semibold">Stok Habis</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="pt-4 pb-2 mt-5 border-top">
            <div class="d-grid gap-3 d-sm-flex justify-content-md-start pt-1">
                {{-- button kembali ke halaman index --}}
                <a href="{{ route('food.index') }}" class="btn btn-secondary py-2 px-4">Close</a>
            </div>
        </div>
    </div>
</x-app-layout>