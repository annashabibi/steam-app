<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use App\Models\Karyawan;
use App\Models\Transaction;
use App\Models\Pengeluaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Tampilkan view laporan
        return view('report.index');
    }

    /**
     * Filter laporan berdasarkan tipe dan tanggal.
     */
    public function filter(Request $request)
{
    $reportType = $request->input('report_type');
    $date = $request->input('date') ?? Transaction::max('date');

    if ($reportType === 'transaction') {
        $transactions = Transaction::with('karyawan', 'motor')
            ->where('payment_status', 'paid')
            ->whereDate('date', $date)
            ->get();

        $totalKeseluruhan = $transactions->sum(fn($trx) =>
            ($trx->motor->harga ?? 0) + ($trx->tip ?? 0)
        );

        $totalFnbTransaksi = 0;
        foreach ($transactions as $trx) {
            $foodItems = is_string($trx->food_items) ? json_decode($trx->food_items, true) : $trx->food_items;
            if (!empty($foodItems) && is_array($foodItems)) {
                foreach ($foodItems as $item) {
                    $totalFnbTransaksi += ($item['harga'] ?? 0) * ($item['qty'] ?? 0);
                }
            }
        }

        return view('report.index', compact('reportType', 'transactions', 'totalKeseluruhan', 'totalFnbTransaksi', 'date'));
    }

    if ($reportType === 'pendapatan') {
        $karyawanList = Karyawan::where('aktif', true)->with([
            'transactions' => fn($q) => $q->where('payment_status', 'paid')->whereDate('date', $date),
            'transactions.motor',
            'pengeluaran' => fn($q) => $q->whereDate('date', $date),
            'helmitems' => fn($q) => $q->whereHas('helmTransaction', fn($query) =>
                $query->whereDate('tanggal_cuci', $date)
            ),
        ])->get();

        $pendapatanKaryawan = $karyawanList->map(function ($karyawan) {
            $totalCucian = $karyawan->transactions->count();
            $totalTip = $karyawan->transactions->sum('tip');
            $totalPengeluaran = $karyawan->pengeluaran->sum('jumlah');
            $bonus = max(0, $totalCucian - 10) * 1000;

            $totalPendapatanMotor = $karyawan->transactions->sum(function ($trx) {
                return match ($trx->motor->category_id ?? 0) {
                    1 => 5000, 2 => 6000, 3 => 7000, 4 => 8000, default => 0,
                };
            });

            $helmHalf = $karyawan->helmitems->where('type_helm', 'half_face');
            $helmFull = $karyawan->helmitems->where('type_helm', 'full_face');

            $totalPendapatanHelm = ($helmHalf->count() * 7000) + ($helmFull->count() * 8000);
            $totalDenganBonus = $totalPendapatanMotor + $totalPendapatanHelm + $totalTip + $bonus;

            return [
                'nama_karyawan'     => $karyawan->nama_karyawan,
                'motor_kecil'       => $karyawan->transactions->where('motor.category_id', 1)->count(),
                'motor_sedang'      => $karyawan->transactions->where('motor.category_id', 2)->count(),
                'motor_besar'       => $karyawan->transactions->where('motor.category_id', 3)->count(),
                'motor_sport'       => $karyawan->transactions->where('motor.category_id', 4)->count(),
                'helm_half_face'    => $helmHalf->count(),
                'helm_full_face'    => $helmFull->count(),
                'total_helm'        => $helmHalf->count() + $helmFull->count(),
                'total_cucian'      => $totalCucian,
                'total_tip'         => $totalTip,
                'bonus'             => $bonus,
                'total_pendapatan'  => $totalDenganBonus,
                'total_pengeluaran' => $totalPengeluaran,
                'saldo_akhir'       => $totalDenganBonus - $totalPengeluaran,
            ];
        });

        $totalCucian = $pendapatanKaryawan->sum('total_cucian');

        $totalPendapatanPemilik = $karyawanList->sum(function ($karyawan) {
            $pendMotor = $karyawan->transactions->sum(function ($trx) {
                return match ($trx->motor->category_id ?? 0) {
                    1 => 10000, 2 => 12000, 3 => 13000, 4 => 14000, default => 0,
                };
            });

            $pendHelm = $karyawan->helmitems->sum(function ($helm) {
                return match ($helm->type_helm ?? '') {
                    'half_face' => 11000,
                    'full_face' => 12000,
                    default => 0,
                };
            });

            return $pendMotor + $pendHelm;
        });

        // Pengeluaran berdasarkan jenis
        $pengeluaranMakan   = Pengeluaran::where('jenis_pengeluaran', 'Uang Makan')->whereDate('date', $date)->sum('jumlah');
        $pengeluaranToken   = Pengeluaran::where('jenis_pengeluaran', 'Token')->whereDate('date', $date)->sum('jumlah');
        $pengeluaranSampah  = Pengeluaran::where('jenis_pengeluaran', 'Uang Sampah')->whereDate('date', $date)->sum('jumlah');
        $pengeluaranSabun   = Pengeluaran::where('jenis_pengeluaran', 'Sabun')->whereDate('date', $date)->sum('jumlah');
        $pengeluaranAir     = Pengeluaran::where('jenis_pengeluaran', 'Air')->whereDate('date', $date)->sum('jumlah');

        // Jumlah total uang steam (langsung dari transaksi)
        $totalUangSteamHariItu = Transaction::whereDate('date', $date)
            ->where('payment_status', 'paid')
            ->sum('total');

        $totalFnbHariItu = 0;
        $fnbTransactions = Transaction::whereDate('date', $date)
            ->where('payment_status', 'paid')
            ->whereNotNull('food_items')
            ->get();

        foreach ($fnbTransactions as $trx) {
            $foodItems = is_string($trx->food_items) ? json_decode($trx->food_items, true) : $trx->food_items;
            if (!empty($foodItems) && is_array($foodItems)) {
                foreach ($foodItems as $item) {
                    $totalFnbHariItu += ($item['harga'] ?? 0) * ($item['qty'] ?? 0);
                }
            }
        }

        $totalUangSteamSaja = $totalUangSteamHariItu - $totalFnbHariItu;

        // Jumlah semua pengeluaran pada hari itu (kasbon & lainnya)
        $totalPengeluaranHariItu = Pengeluaran::whereDate('date', $date)->sum('jumlah');

        // Jumlah bersih hari itu = uang steam - pengeluaran
        $jumlahBersihHariItu = $totalUangSteamSaja + $totalFnbHariItu - $totalPengeluaranHariItu;

        return view('report.index', compact(
            'reportType',
            'pendapatanKaryawan',
            'totalCucian',
            'totalPendapatanPemilik',
            'pengeluaranMakan',
            'pengeluaranToken',
            'pengeluaranSampah',
            'pengeluaranSabun',
            'pengeluaranAir',
            'totalUangSteamHariItu',
            'totalUangSteamSaja',
            'totalFnbHariItu',
            'totalPengeluaranHariItu',
            'jumlahBersihHariItu',
            'date'
        ));
    }

    return redirect()->back()->with('error', 'Tipe laporan tidak dikenali.');
}

    /**
     * Cetak laporan berdasarkan tipe.
     */
    public function print($type)
{
    $date = Carbon::parse(request('date') ?? Transaction::max('date'));

    $data = [
        'type' => $type,
        'date' => $date
    ];

    if ($type === 'transaction') {
        $transactions = Transaction::with('karyawan', 'motor')
            ->where('payment_status', 'paid')
            ->whereDate('date', $date)
            ->get();

        $totalKeseluruhan = $transactions->sum(fn($trx) =>
            ($trx->motor->harga ?? 0) + ($trx->tip ?? 0)
        );

        $totalFnbTransaksi = 0;
        foreach ($transactions as $trx) {
            $foodItems = is_string($trx->food_items) ? json_decode($trx->food_items, true) : $trx->food_items;
            if (!empty($foodItems) && is_array($foodItems)) {
                foreach ($foodItems as $item) {
                    $totalFnbTransaksi += ($item['harga'] ?? 0) * ($item['qty'] ?? 0);
                }
            }
        }

        $data['transactions'] = $transactions;
        $data['totalKeseluruhan'] = $totalKeseluruhan;
        $data['totalFnbTransaksi'] = $totalFnbTransaksi;
    }

    if ($type === 'pendapatan') {
        $karyawanList = Karyawan::where('aktif', true)->with([
            'transactions' => fn($q) => $q->where('payment_status', 'paid')->whereDate('date', $date),
            'transactions.motor',
            'pengeluaran' => fn($q) => $q->whereDate('date', $date),
            'helmitems' => fn($q) => $q->whereHas('helmTransaction', fn($query) =>
                $query->whereDate('tanggal_cuci', $date)
            ),
        ])->get();

        $pendapatanKaryawan = $karyawanList->map(function ($karyawan) {
            $totalCucian = $karyawan->transactions->count();
            $totalTip = $karyawan->transactions->sum('tip');
            $totalPengeluaran = $karyawan->pengeluaran->sum('jumlah');
            $bonus = max(0, $totalCucian - 10) * 1000;

            $totalPendapatanMotor = $karyawan->transactions->sum(function ($trx) {
                return match ($trx->motor->category_id ?? 0) {
                    1 => 5000, 2 => 6000, 3 => 7000, 4 => 8000, default => 0,
                };
            });

            $helmHalf = $karyawan->helmitems->where('type_helm', 'half_face');
            $helmFull = $karyawan->helmitems->where('type_helm', 'full_face');

            $totalPendapatanHelm = ($helmHalf->count() * 7000) + ($helmFull->count() * 8000);
            $totalDenganBonus = $totalPendapatanMotor + $totalPendapatanHelm + $totalTip + $bonus;

            return [
                'nama_karyawan'     => $karyawan->nama_karyawan,
                'motor_kecil'       => $karyawan->transactions->where('motor.category_id', 1)->count(),
                'motor_sedang'      => $karyawan->transactions->where('motor.category_id', 2)->count(),
                'motor_besar'       => $karyawan->transactions->where('motor.category_id', 3)->count(),
                'motor_sport'       => $karyawan->transactions->where('motor.category_id', 4)->count(),
                'helm_half_face'    => $helmHalf->count(),
                'helm_full_face'    => $helmFull->count(),
                'total_helm'        => $helmHalf->count() + $helmFull->count(),
                'total_cucian'      => $totalCucian,
                'total_tip'         => $totalTip,
                'bonus'             => $bonus,
                'total_pendapatan'  => $totalDenganBonus,
                'total_pengeluaran' => $totalPengeluaran,
                'saldo_akhir'       => $totalDenganBonus - $totalPengeluaran,
            ];
        });

        $totalCucian = $pendapatanKaryawan->sum('total_cucian');

        $totalPendapatanPemilik = $karyawanList->sum(function ($karyawan) {
            $pendMotor = $karyawan->transactions->sum(function ($trx) {
                return match ($trx->motor->category_id ?? 0) {
                    1 => 10000, 2 => 12000, 3 => 13000, 4 => 14000, default => 0,
                };
            });

            $pendHelm = $karyawan->helmitems->sum(function ($helm) {
                return match ($helm->type_helm ?? '') {
                    'half_face' => 11000,
                    'full_face' => 12000,
                    default => 0,
                };
            });

            return $pendMotor + $pendHelm;
        });

        $pengeluaranMakan   = Pengeluaran::where('jenis_pengeluaran', 'Uang Makan')->whereDate('date', $date)->sum('jumlah');
        $pengeluaranToken   = Pengeluaran::where('jenis_pengeluaran', 'Token')->whereDate('date', $date)->sum('jumlah');
        $pengeluaranSampah  = Pengeluaran::where('jenis_pengeluaran', 'Uang Sampah')->whereDate('date', $date)->sum('jumlah');
        $pengeluaranSabun   = Pengeluaran::where('jenis_pengeluaran', 'Sabun')->whereDate('date', $date)->sum('jumlah');
        $pengeluaranAir     = Pengeluaran::where('jenis_pengeluaran', 'Air')->whereDate('date', $date)->sum('jumlah');

        $totalUangSteamHariItu = Transaction::whereDate('date', $date)
            ->where('payment_status', 'paid')
            ->sum('total');

        $totalFnbHariItu = 0;
        $fnbTransactions = Transaction::whereDate('date', $date)
            ->where('payment_status', 'paid')
            ->whereNotNull('food_items')
            ->get();

        foreach ($fnbTransactions as $trx) {
            $foodItems = is_string($trx->food_items) ? json_decode($trx->food_items, true) : $trx->food_items;
            if (!empty($foodItems) && is_array($foodItems)) {
                foreach ($foodItems as $item) {
                    $totalFnbHariItu += ($item['harga'] ?? 0) * ($item['qty'] ?? 0);
                }
            }
        }

        $totalUangSteamSaja = $totalUangSteamHariItu - $totalFnbHariItu;

        $totalPengeluaranHariItu = Pengeluaran::whereDate('date', $date)->sum('jumlah');
        $jumlahBersihHariItu = $totalUangSteamSaja + $totalFnbHariItu - $totalPengeluaranHariItu;

        $data = array_merge($data, compact(
            'pendapatanKaryawan',
            'totalCucian',
            'totalPendapatanPemilik',
            'pengeluaranMakan',
            'pengeluaranToken',
            'pengeluaranSampah',
            'pengeluaranSabun',
            'pengeluaranAir',
            'totalUangSteamHariItu',
            'totalUangSteamSaja',
            'totalFnbHariItu',
            'totalPengeluaranHariItu',
            'jumlahBersihHariItu'
        ));
    }

    $pdf = Pdf::loadView('report.print', $data)->setPaper('a4', 'landscape');
    return $pdf->download("report_{$type}_{$date}.pdf");
}

}
