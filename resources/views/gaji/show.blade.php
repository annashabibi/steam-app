<x-app-layout>
    <x-page-title>Gaji - {{ $selected->nama_karyawan }}</x-page-title>

    <div class="bg-white rounded-2 shadow-sm p-4 mb-4">
        <div class="alert alert-success mb-5" role="alert">
            <i class="ti ti-user fs-5 me-2"></i> Menampilkan gaji untuk <strong>{{ $selected->nama_karyawan }}</strong>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" style="width:100%">
                <thead>
                    <tr class="text-center">
                        <th>Tanggal</th>
                        <th>Pendapatan</th>
                        <th>Pengeluaran</th>
                        <th>Pendapatan Bersih</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dataPerHari as $data)
                        <tr class="text-center">
                            <td>{{ $data['date'] }}</td>
                            <td>{{ number_format($data['pendapatan']) }}</td>
                            <td class="text-danger fw-bold"> - {{ number_format($data['pengeluaran']) }}</td>
                            <td class="text-success fw-bold">{{ number_format($data['pendapatan_bersih']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="fw-bold">
                        <td colspan="3" class="text-end">Total Keseluruhan:</td>
                        <td>
                            {{ number_format(collect($dataPerHari)->sum('pendapatan_bersih')) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</x-app-layout>
