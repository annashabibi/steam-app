<x-app-layout>
    {{-- Page Title --}}
    <x-page-title>Makanan & Minuman</x-page-title>

    <div class="bg-white rounded-2 shadow-sm p-4 mb-4">
        <div class="row">
            <div class="d-grid d-lg-block col-lg-5 col-xl-6 mb-4 mb-lg-0">
                {{-- button form add data --}}
                <a href="{{ route('food.create') }}" class="btn btn-primary py-2 px-3">
                    <i class="ti ti-plus me-2"></i> Add Product
                </a>
            </div>
            <div class="col-lg-7 col-xl-6">
                {{-- form pencarian --}}
                <form action="{{ route('food.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control form-search py-2" value="{{ request('search') }}" placeholder="Search product..." autocomplete="off">
                        <button class="btn btn-primary py-2" type="submit">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2 shadow-sm pt-4 px-4 pb-3 mb-5">
        {{-- tabel tampil data --}}
        <div class="table-responsive mb-3">
            <table class="table table-bordered table-striped table-hover align-middle" style="width:100%">
                <thead class="text-center">
                    <tr>
                        <th>No.</th>
                        <th>Image</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($foods as $food)
                    <tr>
                        <td class="text-center">{{ ++$i }}</td>

                        {{-- Gambar --}}
                        <td class="text-center" width="90">
                            @if ($food->image)
                                <img src="{{ $food->image }}" alt="{{ $food->nama_produk }}" class="rounded-3" width="70" height="70" style="object-fit: cover;">
                            @else
                                <img src="https://via.placeholder.com/70" alt="No Image" class="rounded-3">
                            @endif
                        </td>

                        <td>{{ $food->nama_produk }}</td>
                        <td class="text-center">{{ ucfirst($food->category) }}</td>
                        <td class="text-center">Rp{{ number_format($food->harga, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $food->qty }}</td>

                        {{-- Status otomatis berdasarkan stok --}}
                        <td class="text-center">
                            @if ($food->qty > 0)
                                <span class="badge bg-success">Tersedia</span>
                            @else
                                <span class="badge bg-danger">Stok Habis</span>
                            @endif
                        </td>

                        {{-- Action --}}
                        <td class="text-center">
                            <a href="{{ route('food.edit', $food->id) }}" class="btn btn-primary btn-sm m-1" data-bs-tooltip="tooltip" data-bs-title="Edit">
                                <i class="ti ti-edit"></i>
                            </a>
                            {{-- button form detail data --}}
                            <a href="{{ route('food.show', $food->id) }}" class="btn btn-warning btn-sm m-1" data-bs-tooltip="tooltip" data-bs-title="Detail">
                                <i class="ti ti-list"></i>
                            </a>
                            <button type="button" class="btn btn-danger btn-sm m-1" data-bs-toggle="modal" data-bs-target="#modalDelete{{ $food->id }}" data-bs-tooltip="tooltip" data-bs-title="Delete">
                                <i class="ti ti-trash"></i>
                            </button>
                        </td>
                    </tr>

                    {{-- Modal hapus data --}}
                    <div class="modal fade" id="modalDelete{{ $food->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalDeleteLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5">
                                        <i class="ti ti-trash me-2"></i> Delete Product
                                    </h1>
                                </div>
                                <div class="modal-body">
                                    <p>Are you sure you want to delete <strong>{{ $food->nama_produk }}</strong>?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary py-2 px-3" data-bs-dismiss="modal">Cancel</button>
                                    <form action="{{ route('food.destroy', $food->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger py-2 px-3">Yes, delete it!</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    {{-- jika data tidak ada, tampilkan pesan data tidak tersedia --}}
                    <tr>
                        <td colspan="8">
                            <div class="d-flex justify-content-center align-items-center">
                                <i class="ti ti-info-circle fs-5 me-2"></i>
                                <div>No data available.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- pagination --}}
        <div class="pagination-links">{{ $foods->links() }}</div>
    </div>
</x-app-layout>
