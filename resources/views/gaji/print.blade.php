<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Gaji Steam Guegbyuur - {{ $karyawan->nama_karyawan }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .text-danger { color: #dc3545; font-weight: bold; }
        .text-success { color: #198754; font-weight: bold; }
    </style>
</head>
<body>
    <h2 style="margin-bottom: 5px;">Laporan Gaji Steam Guegbyuur - {{ $karyawan->nama_karyawan }}</h2>
    <p>Periode: {{ \Carbon\Carbon::now()->startOfMonth()->translatedFormat('j F Y') }} - {{ \Carbon\Carbon::now()->endOfMonth()->translatedFormat('j F Y') }} </p>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Pendapatan</th>
                <th>Pengeluaran</th>
                <th>Pendapatan Bersih</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataPerHari as $data)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($data['date'])->translatedFormat('j F Y') }}</td>
                    <td>Rp{{ number_format($data['pendapatan'], 0, ',', '.') }}</td>
                    <td class="text-danger fw-bold">
                        @if($data['pengeluaran'] > 0)
                            Rp-{{ number_format($data['pengeluaran'],  0, ',', '.') }}
                        @else
                            Rp0
                        @endif
                    </td>
                    <td class="text-success fw-bold">Rp{{ number_format($data['pendapatan_bersih'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            @php
            $totalKeseluruhan = collect($dataPerHari)->sum('pendapatan_bersih');
            $class = $totalKeseluruhan < 0 ? 'text-danger' : 'text-success';
            @endphp
            <tr class="text-bold">
                <td colspan="3" class="text-end">Total Keseluruhan:</td>
                    <td class="text-center {{ $class }}">
                            Rp{{ $totalKeseluruhan < 0 ? '-' : '' }}{{ number_format(abs($totalKeseluruhan), 0, ',', '.') }}
                    </td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
