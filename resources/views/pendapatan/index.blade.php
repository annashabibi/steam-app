    <x-app-layout>
        {{-- Page Title --}}
        <x-page-title>Pendapatan</x-page-title>

        <div class="alert alert-info">
            <strong>Total Cuci Motor:</strong> {{ $totalCucian }}
            <strong>Total Cuci Helm:</strong> {{ $pendapatanKaryawan->sum('total_helm') }}
        </div>

        <div class="bg-white rounded-2 shadow-sm pt-4 px-4 pb-3 mb-5">
            {{-- Tabel Pendapatan Karyawan --}}
            <h4 class="mb-3">Pendapatan Karyawan</h4>
            <div class="table-responsive mb-3">
                <table class="table table-bordered table-striped table-hover" style="width:100%">
                    <thead class="table-warning">
                        <tr class="text-center">
                            <th>Nama Karyawan</th>
                            <th>Motor Kecil</th>
                            <th>Motor Sedang</th>
                            <th>Motor Besar</th>
                            <th>Motor Sport</th>
                            <th>Total Cuci</th>
                            <th>Bonus</th>
                            <th>Total Tip</th>
                            <th>Helm Half Face</th>
                            <th>Helm Full Face</th>
                            <th>Total Helm</th>
                            <th>Pendapatan</th>
                            <th>Pengeluaran</th>
                            <th>Pendapatan Bersih</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pendapatanKaryawan as $pendapatan)
                        <tr class="text-center">
                            <td>{{ $pendapatan['nama_karyawan'] }}</td>
                            <td>{{ $pendapatan['motor_kecil'] }}</td>
                            <td>{{ $pendapatan['motor_sedang'] }}</td>
                            <td>{{ $pendapatan['motor_besar'] }}</td>
                            <td>{{ $pendapatan['motor_sport'] }}</td>
                            <td>{{ $pendapatan['total_cucian'] }}</td>
                            <td>Rp {{ number_format($pendapatan['bonus'], 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($pendapatan['total_tip'], 0, ',', '.') }}</td>
                            <td>{{ $pendapatan['helm_half_face'] }}</td>
                            <td>{{ $pendapatan['helm_full_face'] }}</td>
                            <td>{{ $pendapatan['total_helm'] }}</td>
                            <td>Rp {{ number_format($pendapatan['total_pendapatan'], 0, ',', '.') }}</td>
                            <td class="text-danger fw-bold">Rp {{ number_format($pendapatan['total_pengeluaran'], 0, ',', '.') }}</td>
                            <td class="text-success fw-bold">Rp {{ number_format($pendapatan['saldo_akhir'], 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
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
                            <th>Pendapatan</th>
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
                            <td>Rp {{ number_format($totalPendapatanPemilik, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($pengeluaranMakan, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($pengeluaranToken, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($pengeluaranSampah, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($pengeluaranAir, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($pengeluaranSabun, 0, ',', '.') }}</td>
                            <td class="text-danger fw-bold">Rp {{ number_format($pengeluaranToken + $pengeluaranSampah + $pengeluaranAir + $pengeluaranSabun + $pengeluaranMakan, 0, ',', '.') }}</td>
                            {{-- <td>Rp {{ number_format($pengeluaranKasbon, 0, ',', '.') }}</td> --}}
                        </tr>
                        {{-- <tr>
                            <td colspan="6" class="text-end fw-bold">Total Pengeluaran</td>
                            <td class="text-danger fw-bold">Rp {{ number_format($pengeluaranToken + $pengeluaranSampah + $pengeluaranSabun + $pengeluaranMakan +$pengeluaranKasbon, 0, ',', '.') }}</td>
                        </tr> --}}
                        <tr>
                            <td colspan="6" class="text-end fw-bold">Total Pendapatan Bersih</td>
                            <td class="text-success fw-bold">Rp {{ number_format($totalPendapatanPemilik - ($pengeluaranToken + $pengeluaranSampah + $pengeluaranAir + $pengeluaranSabun + $pengeluaranMakan), 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
</x-app-layout>