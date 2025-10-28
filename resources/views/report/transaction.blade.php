<div class="bg-white rounded-2 shadow-sm p-4 mb-5">
    <div class="d-flex flex-column flex-lg-row mb-4">
        <div class="flex-grow-1 d-flex align-items-center">
            {{-- judul laporan --}}
            <h6 class="mb-0">
                <i class="ti ti-file-text fs-5 align-text-top me-1"></i> 
                Transactions Report
            </h6>
        </div>
        <div class="d-grid gap-3 d-sm-flex mt-3 mt-lg-0">
            {{-- button cetak laporan (export PDF) --}}
            <a href="{{ route('report.print', ['type' => 'transaction', 'date' => $date]) }}" target="_blank" class="btn btn-warning py-2 px-3">
                <i class="ti ti-printer me-2"></i> Print
            </a>
        </div>
    </div>

    <hr class="mb-4">

    {{-- tabel tampil data --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover" style="width:100%">
            <thead class="text-center">
                <th>No.</th>
                <th>Date</th>
                <th>Karyawan</th>
                <th>Motor</th>
                <th>Harga</th>
                <th>F&B</th>
                <th>Pembayaran</th>
                <th>Tip</th>
                <th>Total</th>
            </thead>
            <tbody>
            @php
                $no = 1;
            @endphp
            @forelse ($transactions as $transaction)
                {{-- jika data ada, tampilkan data --}}
                <tr class="text-center">
                    <td width="30">{{ $no++ }}</td>
                    <td width="100">{{ date('F   j, Y', strtotime($transaction->date)) }}</td>
                    <td width="130">{{ $transaction->karyawan->nama_karyawan }}</td>
                    <td width="100">{{ $transaction->motor->nama_motor }}</td>
                    <td width="70">{{ 'Rp' . number_format($transaction->motor->harga, 0, '', '.') }}</td>
                    <td width="120" class="text-start">
                        @if (!empty($transaction->food_items))
                            @php
                                $foodItems = json_decode($transaction->food_items, true);
                            @endphp

                            @if (!empty($foodItems))
                                <ul class="list-unstyled mb-0">
                                    @foreach ($foodItems as $item)
                                        <li>
                                            {{ $item['nama_produk'] }} (x{{ $item['qty'] }})<br>
                                            Rp{{ number_format($item['qty'] * $item['harga'], 0, ',', '.') }}
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                -
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td width="50">
                         @if ($transaction->payment_method === 'midtrans')
                            {{ ucfirst($transaction->midtrans_payment_type ?? 'Midtrans') }}
                        @elseif ($transaction->payment_method === 'cash')
                                Cash
                        @else
                            {{ ucfirst($transaction->payment_method ?? '-') }}
                        @endif
                    </td>
                    <td width="50" class="text-center">{{'Rp'. number_format($transaction->tip, 0, '', '.') }}</td>
                    <td width="80" class="text-center">{{ 'Rp'. number_format($transaction->total, 0, '', '.') }}</td>
                </tr>
            @empty
                {{-- jika data tidak ada, tampilkan pesan data tidak tersedia --}}
                <tr>
                    <td colspan="9">
                        <div class="d-flex justify-content-center align-items-center">
                            <i class="ti ti-info-circle fs-5 me-2"></i>
                            <div>No data available.</div>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="8" class="text-end fw-bold">Total Keseluruhan</td>
                    <td class="text-end fw-bold">
                        {{ isset($totalKeseluruhan) && isset($totalFnbTransaksi) ? 'Rp' . number_format($totalKeseluruhan + $totalFnbTransaksi, 0, '', '.') : 'Rp 0' }}
                    </td>
                </tr>
            </tfoot>
            
        </table>
    </div>
</div>