<x-app-layout>
    {{-- Page Title --}}
    <x-page-title>Helm</x-page-title>

    <div class="bg-white rounded-2 shadow-sm p-4 mb-4">
        <div class="row">
            <div class="d-grid d-lg-block col-lg-5 col-xl-6 mb-4 mb-lg-0">
                {{-- button form add data --}}
                <a href="{{ route('helms.create') }}" class="btn btn-primary py-2 px-3">
                    <i class="ti ti-plus me-2"></i> Add Helm Transaction
                </a>

                {{-- Modal Delete All --}}
                <div class="modal fade" id="modalDeleteAll" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalDeleteAllLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="modalDeleteAllLabel">
                                    <i class="ti ti-trash me-2"></i> Delete All Transaction Helm
                                </h1>
                            </div>
                            <div class="modal-body">
                                <p class="mb-2">Are you sure delete all transaction today? Already printed?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary py-2 px-3" data-bs-dismiss="modal">Cancel</button>
                                <form action="{{ route('helms.deleteAll') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger py-2 px-3">Yes, delete it!</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 col-xl-6">
                {{-- form pencarian --}}
                <form action="{{ route('helms.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control form-search py-2" value="{{ request('search') }}" placeholder="Search helm transaction ..." autocomplete="off">
                        <button class="btn btn-primary py-2" type="submit">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2 shadow-sm pt-4 px-4 pb-3 mb-5">
        {{-- tabel tampil data --}}
        <div class="table-responsive mb-3">
            <table class="table table-bordered table-striped table-hover" style="width:100%">
                <thead>
                    <th class="text-center">No.</th>
                    <th class="text-center">Customer</th>
                    <th class="text-center">Jumlah Helm</th>
                    <th class="text-center">Nama Helm</th>
                    <th class="text-center">Total Harga</th>
                    <th class="text-center">Status Bayar</th>
                    <th class="text-center">Metode</th>
                    <th class="text-center">Tanggal Cuci</th>
                    <th class="text-center">Selesai</th>
                    <th class="text-center">Action</th>
                </thead>
                <tbody>
                @forelse ($helm_transaction as $helm)
                    {{-- jika data ada, tampilkan data --}}
                    <tr>
                        <td width="30" class="text-center">{{ ++$i }}</td>
                        <td width="120">{{ $helm->nama_customer ?? '-' }}</td>
                        <td width="50" class="text-center">{{ $helm->helmitems->count() }}</td>
                        <td width="200">
                            <ul class="mb-0 list-unstyled">
                                @foreach ($helm->helmitems as $item)
                                    <li class="mb-1">
                                        <span class="fw-bold">{{ $item->nama_helm }}</span><br>
                                        <small class="text-muted">({{ ucfirst(str_replace('_', ' ', $item->type_helm)) }})</small>
                                        @if($item->karyawan)
                                            <br><small class="text-info">by {{ $item->karyawan->nama_karyawan }}</small>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                        <td width="100" class="text-end">{{ 'Rp ' . number_format($helm->helmitems->sum('harga'), 0, '', '.') }}</td>
                        <td width="80" class="text-center">
                            @php
                                $status = $helm->payment_status ?? 'paid';
                                $class = match($status) {
                                    'paid'    => 'bg-success',
                                    'pending' => 'bg-warning',
                                    'expired' => 'bg-danger',
                                    'failed'  => 'bg-dark',
                                    default   => 'bg-secondary',
                                };
                            @endphp
                            <span class="badge {{ $class }}">{{ ucfirst($status) }}</span>
                        </td>
                        <td width="80" class="text-center">
                            @if ($helm->payment_method === 'online')
                                {{ ucfirst($helm->midtrans_payment_type ?? 'Online') }}
                            @elseif ($helm->payment_method === 'offline')
                                Cash
                            @else
                                {{ ($helm->payment_method ?? '-') }}
                            @endif
                        </td>                        
                        <td width="100" class="text-center">{{ \Carbon\Carbon::parse($helm->tanggal_cuci)->format('d/m/Y') }}</td>
                        <td width="100" class="text-center">{{ $helm->tanggal_selesai ? \Carbon\Carbon::parse($helm->tanggal_selesai)->format('d/m/Y') : '-' }}</td>
                        <td width="120" class="text-center">
                            {{-- button form detail data --}}
                            {{-- <a href="{{ route('helms.show', $helm->id) }}" class="btn btn-warning btn-sm m-1" data-bs-tooltip="tooltip" data-bs-title="Detail">
                                <i class="ti ti-list"></i>
                            </a> --}}
                            {{-- button form edit data --}}
                            <a href="{{ route('helms.edit', $helm->id) }}" class="btn btn-primary btn-sm m-1" data-bs-tooltip="tooltip" data-bs-title="Edit">
                                <i class="ti ti-edit"></i>
                            </a>
                            {{-- button modal hapus data --}}
                            <button type="button" class="btn btn-danger btn-sm m-1" data-bs-toggle="modal" data-bs-target="#modalDelete{{ $helm->id }}" data-bs-tooltip="tooltip" data-bs-title="Delete"> 
                                <i class="ti ti-trash"></i>
                            </button>
                        </td>
                    </tr>

                    {{-- Modal hapus data --}}
                    <div class="modal fade" id="modalDelete{{ $helm->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalDeleteLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">
                                        <i class="ti ti-trash me-2"></i> Delete Helm Transaction
                                    </h1>
                                </div>
                                <div class="modal-body">
                                    {{-- informasi data yang akan dihapus --}}
                                    <p class="mb-2">
                                        Are you sure to delete helm transaction for <span class="fw-bold mb-2">{{ $helm->nama_customer ?? 'Customer' }}</span>?
                                    </p>
                                    <div class="mb-2">
                                        <strong>Items:</strong>
                                        <ul class="mb-0">
                                            @foreach ($helm->helmitems as $item)
                                                <li>{{ $item->nama_helm }} ({{ ucfirst(str_replace('_', ' ', $item->type_helm)) }})</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary py-2 px-3" data-bs-dismiss="modal">Cancel</button>
                                    {{-- button hapus data --}}
                                    <form action="{{ route('helms.destroy', $helm->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger py-2 px-3"> Yes, delete it! </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    {{-- jika data tidak ada, tampilkan pesan data tidak tersedia --}}
                    <tr>
                        <td colspan="10">
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
        <div class="pagination-links">{{ $helm_transaction->links() }}</div>
    </div>
</x-app-layout>