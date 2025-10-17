<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\HelmItem;
use App\Models\Motor;
use App\Models\Karyawan;
use App\Models\Pengeluaran;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request):View
    {
         // menampilkan jumlah data Motor
         $totalMotor = Motor::count();
         // menampilkan jumlah data Helm
         $totalHelm = HelmItem::count();
         // menampilkan jumlah data Karyawan
         $totalKaryawan = Karyawan::count();
         // menampilkan jumlah data Transaksi
         $totalTransaction = Transaction::count();

        // menampilkan data 5 cuci motor terlaris
         $motorTransactions = Transaction::select('motor_id', DB::raw('count(*) as total_washes'))
                                        ->whereDate('date', Carbon::today()) // filter hanya hari ini
                                        ->groupBy('motor_id')
                                        ->orderByDesc('total_washes')
                                        ->with('motor:id,nama_motor,harga,image')
                                        ->take(5)
                                        ->get();

        // ambil filter range dari request (default 7 hari)
        $range = $request->get('range', '7');

        $dates   = collect();
        $incomes = collect();
        $totals  = collect();

        if ($range == '7' || $range == '30') {
            // Seminggu
            $days = (int) $range - 1;

            for ($i = $days; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i)->toDateString();

                // transaksi hari itu
                $dailyTransactions = Transaction::with('motor')
                    ->where('payment_status', 'paid')
                    ->whereDate('date', $date)
                    ->get();

                // pendapatan pemilik
                $pendapatanPemilik = $dailyTransactions->sum(function ($trx) {
                    $harga = $trx->motor->harga ?? 0;
                    return match ($harga) {
                        15000 => 10000,
                        18000 => 12000,
                        20000 => 13000,
                        22000 => 14000,
                        default => 0,
                    };
                });

                // pengeluaran pemilik
                $pengeluaranPemilik = Pengeluaran::whereDate('date', $date)
                    ->whereIn('jenis_pengeluaran', ['Sabun', 'Air', 'Token', 'Uang Makan', 'Uang Sampah'])
                    ->sum('jumlah');

                // pendapatan bersih
                $pendapatanBersih = $pendapatanPemilik - $pengeluaranPemilik;

                $dates->push(Carbon::parse($date)->translatedFormat('d M'));
                $incomes->push($pendapatanBersih);
                $totals->push($dailyTransactions->count());
            }

        // bulanan
        } elseif ($range == 'monthly') {
            // Ambil bulan-bulan yang ada transaksi
            $months = Transaction::where('payment_status', 'paid')
                ->selectRaw('DISTINCT DATE_FORMAT(date, "%Y-%m") as bulan')
                ->pluck('bulan');

            foreach ($months as $bulan) {
                // Semua transaksi motor di bulan ini
                $monthlyTransactions = Transaction::with('motor')
                    ->where('payment_status', 'paid')
                    ->whereRaw('DATE_FORMAT(date, "%Y-%m") = ?', [$bulan])
                    ->get();

                // Hitung pendapatan pemilik
                $pendapatanPemilik = $monthlyTransactions->sum(function ($trx) {
                    $harga = $trx->motor->harga ?? 0;
                    return match ($harga) {
                        15000 => 10000,
                        18000 => 12000,
                        20000 => 13000,
                        22000 => 14000,
                        default => 0,
                    };
                });

                // Hitung pengeluaran pemilik di bulan ini
                $pengeluaranPemilik = Pengeluaran::whereRaw('DATE_FORMAT(date, "%Y-%m") = ?', [$bulan])
                    ->whereIn('jenis_pengeluaran', ['Sabun', 'Air', 'Token', 'Uang Makan', 'Uang Sampah'])
                    ->sum('jumlah');

                $pendapatanBersih = $pendapatanPemilik - $pengeluaranPemilik;

                // Push ke koleksi untuk grafik
                $dates->push(Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('M Y'));
                $incomes->push($pendapatanBersih);
                $totals->push($monthlyTransactions->count());
            }
        }


        $labels = $dates;
        $datasets = [[
            'label' => 'Pendapatan Bersih Pemilik',
            'data' => $incomes,
            'borderColor' => '#36A2EB',
            'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
            'fill' => true,
            'tension' => 0.3,
        ]];

        return view('dashboard.index', compact(
            'totalMotor',
            'totalHelm',
            'totalKaryawan',
            'totalTransaction',
            'motorTransactions',
            'dates',
            'incomes',
            'totals',
            'labels',
            'datasets',
            'range'
        ));

    }
}