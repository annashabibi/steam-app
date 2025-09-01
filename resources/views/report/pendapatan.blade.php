     <div class="bg-white rounded-2 shadow-sm p-4 mb-5">
            <div class="d-flex flex-column flex-lg-row mb-4">
                <div class="flex-grow-1 d-flex align-items-center">
                    {{-- judul laporan --}}
                    <h6 class="mb-0">
                        <i class="ti ti-file-text fs-5 align-text-top me-1"></i> 
                        Laporan Pendapatan - Hari {{ \Carbon\Carbon::parse($date)->translatedFormat('l, j F Y') }}
                    </h6>
                </div>
                <div class="d-grid gap-3 d-sm-flex mt-3 mt-lg-0">
                    {{-- button cetak laporan (export PDF) --}}
                    <a href="{{ route('report.print', ['type' => 'pendapatan', 'date' => request('date')]) }}" target="_blank" class="btn btn-warning py-2 px-3">
                        <i class="ti ti-printer me-2"></i> Print
                    </a>
                </div>
            </div>

        <div class="alert alert-info d-flex justify-content-between">
            <div>
                <strong>Total Motor Masuk:</strong> {{ $totalCucian }}
            </div>
            <div class="text-end">
                <strong>Total Cuci Helm:</strong> {{ $pendapatanKaryawan->sum('total_helm') }}
            </div>
        </div>


        <h4 class="mb-3">Pendapatan Karyawan</h4>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-warning">
                    <tr class="text-center">
                        <th>Nama Karyawan</th>
                        <th>Motor Kecil</th>
                        <th>Motor Sedang</th>
                        <th>Motor Besar</th>
                        <th>Motor Sport</th>
                        <th>Total Cuci</th>
                        <th>Bonus</th>
                        <th>Tip</th>
                        <th>Half Face</th>
                        <th>Full Face</th>
                        <th>Total Helm</th>
                        <th>Pendapatan</th>
                        <th>Pengeluaran</th>
                        <th>Pendapatan Bersih</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pendapatanKaryawan as $data)
                        <tr class="text-center">
                            <td>{{ $data['nama_karyawan'] }}</td>
                            <td>{{ $data['motor_kecil'] }}</td>
                            <td>{{ $data['motor_sedang'] }}</td>
                            <td>{{ $data['motor_besar'] }}</td>
                            <td>{{ $data['motor_sport'] }}</td>
                            <td>{{ $data['total_cucian'] }}</td>
                            <td>Rp{{ number_format($data['bonus'], 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($data['total_tip'], 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($data['helm_half_face'] * 7000, 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($data['helm_full_face'] * 8000, 0, ',', '.') }}</td>
                            <td>{{ $data['total_helm'] }}</td>
                            <td>Rp{{ number_format($data['total_pendapatan'], 0, ',', '.') }}</td>
                            <td class="text-danger fw-bold">Rp{{ number_format($data['total_pengeluaran'], 0, ',', '.') }}</td>
                            <td class="text-success fw-bold">Rp{{ number_format($data['saldo_akhir'], 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        {{-- jika data tidak ada, tampilkan pesan data tidak tersedia --}}
                        <tr>
                            <td colspan="11">
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
    </div>
    
    <div class="bg-white rounded-2 shadow-sm pt-4 px-4 pb-3 mb-5">
        <h4 class="mb-3">Pendapatan Pemilik</h4>
        <div class="table-responsive mb-3">
            <table class="table table-bordered table-striped table-hover text-center" style="width:100%">
                <thead class="table-secondary">
                    <tr>
                        <th>Total Uang Steam</th>
                        <th>Uang Makan</th>
                        <th>Token</th>
                        <th>Uang Sampah</th>
                        <th>Galon</th>
                        <th>Sabun</th>
                        <th>Total Pengeluaran</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Rp{{ number_format($totalUangSteamHariItu, 0, ',', '.') }}</td>
                        <td>Rp{{ number_format($pengeluaranMakan, 0, ',', '.') }}</td>
                        <td>Rp{{ number_format($pengeluaranToken, 0, ',', '.') }}</td>
                        <td>Rp{{ number_format($pengeluaranSampah, 0, ',', '.') }}</td>
                        <td>Rp{{ number_format($pengeluaranAir, 0, ',', '.') }}</td>
                        <td>Rp{{ number_format($pengeluaranSabun, 0, ',', '.') }}</td>
                        <td class="text-danger fw-bold">Rp{{ number_format($totalPengeluaranHariItu, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="6" class="text-end fw-bold text-dark">Jumlah</td>
                        <td class="fw-bold text-dark">Rp{{ number_format($jumlahBersihHariItu, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="6" class="text-end fw-bold">Total Pendapatan Bersih</td>
                        <td class="text-success fw-bold">Rp{{ number_format($totalPendapatanPemilik - ($pengeluaranToken + $pengeluaranSampah + $pengeluaranAir + $pengeluaranSabun + $pengeluaranMakan), 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>