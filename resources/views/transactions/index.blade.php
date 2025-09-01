<x-app-layout>
    {{-- Page Title --}}
    <x-page-title>Transaksi ({{ \Carbon\Carbon::parse($today)->translatedFormat('l, d F Y') }})</x-page-title>

    <div class="bg-white rounded-2 shadow-sm p-4 mb-4">
        <div class="row">
            <div class="d-grid d-lg-block col-lg-5 col-xl-6 mb-4 mb-lg-0">
                {{-- Add Transaction --}}
                <a href="{{ route('transactions.create') }}" class="btn btn-primary py-2 px-3">
                    <i class="ti ti-plus me-2"></i> Add Transaction
                </a>
                {{-- Delete All --}}
                {{-- <button type="button" class="btn btn-danger py-2 px-3" data-bs-toggle="modal" data-bs-target="#modalDeleteAll">
                    <i class="ti ti-trash me-2"></i> Delete All Transaction
                </button> --}}

                {{-- Modal Delete All --}}
            </div>
            <div class="col-lg-7 col-xl-6">
                {{-- Search --}}
                <form action="{{ route('transactions.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control py-2" value="{{ request('search') }}" placeholder="Search transaction ..." autocomplete="off">
                        <button class="btn btn-primary py-2" type="submit">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2 shadow-sm pt-4 px-4 pb-3 mb-5">
        <div class="table-responsive mb-3">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th class="text-center">No.</th>
                        <th class="text-center">Date</th>
                        <th class="text-center">Karyawan</th>
                        <th class="text-center">Motor</th>
                        <th class="text-center">Harga</th>
                        <th class="text-center">Tip</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Pembayaran</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $transaction)
                        <tr>
                            <td class="text-center">{{ ++$i }}</td>
                            <td>{{ date('F j, Y', strtotime($transaction->date)) }}</td>
                            <td>{{ $transaction->karyawan->nama_karyawan }}</td>
                            <td>{{ $transaction->motor->nama_motor }}</td>
                            <td class="text-end">{{ 'Rp ' . number_format($transaction->motor->harga, 0, '', '.') }}</td>
                            <td class="text-center">{{ number_format($transaction->tip, 0, '', '.') }}</td>
                            <td class="text-end">{{ 'Rp ' . number_format($transaction->motor->harga + $transaction->tip, 0, '', '.') }}</td>
                            <td class="text-center">
                                @if ($transaction->payment_method === 'midtrans')
                                    {{ ucfirst($transaction->midtrans_payment_type ?? 'Midtrans') }}
                                @elseif ($transaction->payment_method === 'cash')
                                    Cash
                                @else
                                    {{ ucfirst($transaction->payment_method ?? '-') }}
                                @endif
                            </td>
                            {{-- <td class="text-center">{{ ucfirst($transaction->payment_status ?? 'Paid') }}</td> --}}
                            @php
                            $status = $transaction->payment_status ?? 'paid';
                            $class = match($status) {
                                    'paid'    => 'bg-success',
                                    'pending' => 'bg-warning',
                                    'expired' => 'bg-danger',
                                     default  => 'bg-secondary',
                                };
                            @endphp
                            <td class="text-center"> 
                                <span class="badge {{ $class }}">{{ ucfirst($status) }}</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('transactions.edit', $transaction->id) }}" class="btn btn-primary btn-sm m-1" title="Edit Transaksi">
                                    <i class="ti ti-edit"></i>
                                </a>
                                <button type="button" class="btn btn-danger btn-sm m-1" data-bs-toggle="modal" title="Hapus" data-bs-target="#modalDelete{{ $transaction->id }}">
                                    <i class="ti ti-trash"></i>
                                </button>
                                <a href="{{ route('pay', $transaction->id) }}" class="btn btn-sm btn-warning" title="Lihat Detail">
                                    <i class="ti ti-list"></i>
                                </a>
                            </td>
                        </tr>

                        {{-- Modal Delete One --}}
                        <div class="modal fade" id="modalDelete{{ $transaction->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5"><i class="ti ti-trash me-2"></i> Delete Transaction</h1>
                                    </div>
                                    <div class="modal-body">
                                        <p class="mb-2">Are you sure to delete this transaction?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary py-2 px-3" data-bs-dismiss="modal">Cancel</button>
                                        <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger py-2 px-3">Yes, delete it!</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="10">
                                <div class="d-flex justify-content-center align-items-center">
                                    <i class="ti ti-info-circle fs-5 me-2"></i>
                                    <span>No data available.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    @if ($transactions->currentPage() == $transactions->lastPage())
                        <tr>
                            <td colspan="8" class="text-end"><strong>Total Keseluruhan</strong></td>
                            <td class="text-end"><strong>{{ 'Rp ' . number_format($totalKeseluruhan, 0, '', '.') }}</strong></td>
                            <td colspan="3"></td>
                        </tr>
                    @endif
                </tfoot>
            </table>
        </div>
        <div class="pagination-links">{{ $transactions->links() }}</div>
    </div>
</x-app-layout>