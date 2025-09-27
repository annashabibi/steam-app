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
                <button type="button" class="btn btn-danger py-2 px-3" data-bs-toggle="modal" data-bs-target="#modalDeleteAll">
                    <i class="ti ti-trash me-2"></i> Delete All Transactions
                </button>

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
                        <td width="100" class="text-center">{{ \Carbon\Carbon::parse($helm->tanggal_cuci)->translatedFormat('l, d F Y') }}</td>
                        <td width="100" class="text-center">{{ $helm->tanggal_selesai ? \Carbon\Carbon::parse($helm->tanggal_selesai)->translatedFormat('l, d F Y') : '-' }}</td>
                        <td width="120" class="text-center">
                            {{-- button form edit data --}}
                            <a href="{{ route('helms.edit', $helm->id) }}" class="btn btn-primary btn-sm m-1" data-bs-tooltip="tooltip" data-bs-title="Edit">
                                <i class="ti ti-edit"></i>
                            </a>
                            {{-- button modal hapus data --}}
                            <button type="button" class="btn btn-danger btn-sm m-1" data-bs-toggle="modal" data-bs-target="#modalDelete{{ $helm->id }}" data-bs-tooltip="tooltip" data-bs-title="Delete"> 
                                <i class="ti ti-trash"></i>
                            </button>

                            {{-- Tombol Detail di tabel --}}
                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalDetailHelm{{ $helm->id }}" title="Lihat Detail">
                                <i class="ti ti-list"></i>
                            </button>

                            {{-- Modal Detail --}}
                            <div class="modal fade" id="modalDetailHelm{{ $helm->id }}" tabindex="-1" aria-labelledby="modalDetailLabelHelm{{ $helm->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg">
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>

                                        <div class="modal-body p-0">
                                            <div class="card border-0 rounded-0" id="transaksiHelmContent{{ $helm->id }}">
                                                <div class="card-body text-center p-4">
                                                    {{-- Header --}}
                                                    <h3 class="brand">Gue Gbyuur</h3>
                                                    <h6 class="text-muted mb-1">Steam Motor & Cuci Helm</h6>
                                                    <p class="text-muted small mb-3">Jl. Abdul Ghani 2 Perumahan Palkostrad, Kalibaru, Kec. Cilodong, Kota Depok</p>

                        {{-- Payment Status --}}
                        @php
                            $status = $helm->payment_status ?? 'paid';
                            $statusClass = match($status) {
                                'paid' => 'success',
                                'pending' => 'warning',
                                'expired' => 'danger',
                                default => 'secondary',
                            };
                            $statusText = match($status) {
                                'paid' => 'Pembayaran Lunas',
                                'pending' => 'Menunggu Pembayaran',
                                'expired' => 'Pembayaran Kadaluarsa',
                                default => 'Status Tidak Diketahui',
                            };
                        @endphp

                        <div class="alert alert-{{ $statusClass }} py-2 mb-4">
                            <i class="ti ti-check-circle me-1"></i>
                            <strong>{{ $statusText }}</strong>
                        </div>

                        {{-- QR Code --}}
                        @if ($helm->payment_method === 'online')
                            @if ($helm->payment_status === 'pending' 
                                && $helm->expiry_time 
                                && now()->lt(\Carbon\Carbon::parse($helm->expiry_time)))
                                <div class="text-center my-3">
                                    {!! QrCode::size(250)->generate($helm->qr_string) !!}
                                    <p class="small text-muted mt-2">
                                        <div>
                                            <div class="countdown" data-expired="{{ $helm->expiry_time }}" id="countdownHelm{{ $helm->id }}"></div>
                                        </div>
                                    </p>
                                </div>
                            @endif
                        @endif

                        {{-- Helm Items --}}
                        <div class="row g-0">
                            <div class="col-12">
                                <div class="border rounded-3 p-3 bg-light">
                                    <table class="table table-borderless mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="text-start fw-semibold text-muted"><i class="ti ti-user me-2"></i>Nama Pelanggan</td>
                                                <td class="text-end">{{ $helm->nama_customer ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-start fw-semibold text-muted"><i class="ti ti-helmet align-text-top me-2"></i>Helm</td>
                                                <td class="text-end">
                                                    @foreach ($helm->helmitems as $item)
                                                        {{ $item->nama_helm }} ({{ $item->type_helm }})<br>
                                                    @endforeach
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-start fw-semibold text-muted"><i class="ti ti-calendar me-2"></i>Tanggal Cuci</td>
                                                <td class="text-end">{{ $helm->tanggal_cuci->translatedFormat('l, d F Y') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-start fw-semibold text-muted"><i class="ti ti-calendar me-2"></i>Tanggal Selesai</td>
                                                <td class="text-end">{{ $helm->tanggal_selesai->translatedFormat('l, d F Y') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-start fw-semibold text-muted"><i class="ti ti-credit-card me-2"></i>Metode Pembayaran</td>
                                                <td class="text-end">
                                                    @if ($helm->payment_method === 'online')
                                                        {{ ucfirst($helm->midtrans_payment_type ?? 'Online') }}
                                                    @elseif ($helm->payment_method === 'cash')
                                                        Cash
                                                    @else
                                                        {{ ucfirst($helm->payment_method ?? '-') }}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr class="text-end">
                                                <td class="text-start fw-bold text-primary">
                                                    <i class="ti ti-calculator me-2"></i>Total
                                                </td>
                                                <td class="text-end fw-bold text-primary fs-5">
                                                    Rp {{ number_format($helm->helmitems->sum('harga'), 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="mt-1 pt-2">
                            <p class="text-muted small mb-2">
                                <i class="ti ti-clock me-1">
                                    Tanggal Cetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB
                                </i>
                            </p>
                            <p class="text-muted small mb-0">Terima kasih atas kepercayaan Anda!</p>
                        </div>

                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-warning" onclick="printTransaksiHelm({{ $helm->id }})"
                    @if(!in_array($helm->payment_status, ['paid','settlement','capture'])) disabled @endif>
                    <i class="ti ti-printer me-1"></i> Cetak
                </button>
            </div>
        </div>
    </div>
</div>
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