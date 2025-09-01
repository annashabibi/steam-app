<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Steam Guegbyuur - {{ ucfirst($type) }} {{ $date->translatedFormat('l, j F Y') }}</title>
    <style type="text/css">
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border: 1px solid #dee2e6;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th, td {
            border: 1px solid #dee2e6;
            padding: 8px 6px;
            text-align: center;
        }

        th {
            background-color: #f8f9fa;
        }

        .bg-blue { background-color: #1E90FF; color: #ffffff; }
        .bg-warning { background-color: #fff3cd; }
        .bg-secondary { background-color: #e2e3e5; }
        .text-danger { color: #dc3545; font-weight: bold; }
        .text-success { color: #198754; font-weight: bold; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
    </style>
</head>
<body>
    <div style="text-align: center">
        <h3>Laporan {{ ucfirst($type) }} Steam Guegbyuur {{ $date->isoFormat('dddd, D MMMM Y') }}</h3>
    </div>

    <hr style="margin-bottom:20px">
    

    @if($type === 'transaction' && isset($transactions) && $transactions->count())
    <table style="width: 100%; border-collapse: collapse;" border="1">
        <thead style="background-color: #cce5ff; text-align: center;">
            <tr>
                <th>No.</th>
                <th>Tanggal</th>
                <th>Karyawan</th>
                <th>Motor</th>
                <th>Harga</th>
                <th>Tip</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $transaction)
                <tr style="text-align: center;">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ date('d-m-Y', strtotime($transaction->date)) }}</td>
                    <td>{{ $transaction->karyawan->nama_karyawan }}</td>
                    <td>{{ $transaction->motor->nama_motor }}</td>
                    <td>Rp {{ number_format($transaction->motor->harga, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($transaction->tip, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($transaction->motor->harga + $transaction->tip, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" style="text-align: right; font-weight: bold;">Total Keseluruhan</td>
                <td style="font-weight: bold;">Rp{{ number_format($totalKeseluruhan ?? 0, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>    

    @elseif($type === 'pendapatan' && isset($pendapatanKaryawan) && count($pendapatanKaryawan))

        <h4 style="float: left;">Pendapatan Karyawan</h4>
        <h4 style="float: right;">Total Motor Masuk: {{ $totalCucian }}</h4>
        <div style="clear: both;"></div>
            <table>
                <thead class="bg-warning">
                    <tr>
                        <th>Nama Karyawan</th>
                        <th>Motor Kecil</th>
                        <th>Motor Sedang</th>
                        <th>Motor Besar</th>
                        <th>Motor Sport</th>
                        <th>Total Cuci</th>
                        <th>Bonus</th>
                        <th>Total Tip</th>
                        <th>Half Face</th>
                        <th>Full Face</th>
                        <th>Total Helm</th>
                        <th>Pendapatan</th>
                        <th>Pengeluaran</th>
                        <th>Pendapatan Bersih</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pendapatanKaryawan as $pendapatan)
                    <tr>
                        <td>{{ $pendapatan['nama_karyawan'] }}</td>
                        <td>{{ $pendapatan['motor_kecil'] }}</td>
                        <td>{{ $pendapatan['motor_sedang'] }}</td>
                        <td>{{ $pendapatan['motor_besar'] }}</td>
                        <td>{{ $pendapatan['motor_sport'] }}</td>
                        <td>{{ $pendapatan['total_cucian'] }}</td>
                        <td>Rp{{ number_format($pendapatan['bonus'], 0, ',', '.') }}</td>
                        <td>Rp{{ number_format($pendapatan['total_tip'], 0, ',', '.') }}</td>
                        <td>Rp{{ number_format($pendapatan['helm_half_face'] * 7000, 0, ',', '.') }}</td>
                        <td>Rp{{ number_format($pendapatan['helm_full_face'] * 8000, 0, ',', '.') }}</td>
                        <td>{{ $pendapatan['total_helm'] }}</td>
                        <td>Rp{{ number_format($pendapatan['total_pendapatan'], 0, ',', '.') }}</td>
                        <td class="text-danger">Rp{{ number_format($pendapatan['total_pengeluaran'], 0, ',', '.') }}</td>
                        <td class="text-success">Rp{{ number_format($pendapatan['saldo_akhir'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <h4>Pendapatan Pemilik</h4>
            <table>
                <thead class="bg-secondary">
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
                        <td class="text-success">Rp{{ number_format($totalPendapatanPemilik - ($pengeluaranToken + $pengeluaranSampah + $pengeluaranAir + $pengeluaranSabun + $pengeluaranMakan), 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        @endif

    <div style="margin-top: 25px; text-align: right">Depok, {{ $date->translatedFormat('F j, Y', strtotime($date)) }}</div>
</body>
</html>
