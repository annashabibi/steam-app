<x-app-layout>
    {{-- Page Title --}}
    <x-page-title>Karyawan</x-page-title>

    <div class="bg-white rounded-2 shadow-sm p-4 mb-4">
        <div class="row">
            <div class="d-grid d-lg-block col-lg-5 col-xl-6 mb-4 mb-lg-0">
                {{-- button form add data --}}
                <a href="{{ route('karyawans.create') }}" class="btn btn-primary py-2 px-3">
                    <i class="ti ti-plus me-2"></i> Add Karyawan
                </a>
            </div>
            <div class="col-lg-7 col-xl-6">
                {{-- form pencarian --}}
                <form action="{{ route('karyawans.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control form-search py-2" value="{{ request('search') }}" placeholder="Search karyawan ..." autocomplete="off">
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
                    <th class="text-center">Nama Karyawan</th>
                    <th class="text-center">Phone</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                </thead>
                <tbody>
                @forelse ($karyawans as $karyawan)
                    {{-- jika data ada, tampilkan data --}}
                    <tr>
                        <td width="30" class="text-center">{{ ++$i }}</td>
                        <td width="150">{{ $karyawan->nama_karyawan }}</td>
                        <td width="70" class="text-center">{{ $karyawan->no_telepon }}</td>
                        @php
                        $status = $karyawan->aktif ? 'aktif' : 'nonaktif';
                        $class = match($status) {
                                'aktif'     => 'bg-success',
                                'nonaktif'  => 'bg-danger',
                                default     => 'bg-secondary',
                            };
                        @endphp
                        <td width="70" class="text-center">
                            <span class="badge {{ $class }}">{{ ucfirst($status) }}</span>
                        </td>
                        <td width="70" class="text-center">
                            {{-- button form edit data --}}
                            <a href="{{ route('karyawans.edit', $karyawan->id) }}" class="btn btn-primary btn-sm m-1" data-bs-tooltip="tooltip" data-bs-title="Edit">
                                <i class="ti ti-edit"></i>
                            </a>
                            {{-- button modal hapus data --}}
                            <button type="button" class="btn {{ $karyawan->aktif ? 'btn-danger' : 'btn-success' }} btn-sm m-1" data-bs-toggle="modal" data-bs-target="#modalToggle{{ $karyawan->id }}" data-bs-tooltip="tooltip" data-bs-title="{{ $karyawan->aktif ? 'Nonaktifkan' : 'Aktifkan' }}">
                                <i class="ti {{ $karyawan->aktif ? 'ti-lock' : 'ti-lock-open' }}"></i>
                            </button>
                        </td>
                    </tr>

                    {{-- Modal hapus data --}}
                    <div class="modal fade" id="modalToggle{{ $karyawan->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalToggleLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="modalToggleLabel">
                                        <i class="ti {{ $karyawan->aktif ? 'ti-lock' : 'ti-lock-open' }} me-2"></i> 
                                        {{ $karyawan->aktif ? 'Nonaktifkan' : 'Aktifkan' }} Karyawan
                                    </h1>
                                </div>
                                <div class="modal-body">
                                    <p class="mb-2">
                                        Anda yakin ingin {{ $karyawan->aktif ? 'menonaktifkan' : 'mengaktifkan kembali' }} 
                                        <span class="fw-bold">{{ $karyawan->nama_karyawan }}</span>?
                                    </p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary py-2 px-3" data-bs-dismiss="modal">Batal</button>
                                    <form action="{{ route('karyawans.status', $karyawan->id) }}" method="POST">
                                        @csrf
                                        @method('POST')
                                        <button type="submit" class="btn {{ $karyawan->aktif ? 'btn-danger' : 'btn-success' }} py-2 px-3">
                                            Ya, {{ $karyawan->aktif ? 'Nonaktifkan' : 'Aktifkan' }}!
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    {{-- jika data tidak ada, tampilkan pesan data tidak tersedia --}}
                    <tr>
                        <td colspan="5">
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
        <div class="pagination-links">{{ $karyawans->links() }}</div>
    </div>
</x-app-layout>