<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\HelmItem;
use App\Models\Motor;
use App\Models\Karyawan;
use App\Models\Pengeluaran;
use App\Models\Transaction;
use App\Models\Food;
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
    public function index(Request $request): View
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
            ->whereDate('date', Carbon::today())
            ->groupBy('motor_id')
            ->orderByDesc('total_washes')
            ->with('motor:id,nama_motor,harga,image')
            ->take(5)
            ->get();

        // ambil filter range dari request (default 7 hari)
        $range = $request->get('range', '7');

        $dates = collect();
        $incomes = collect();
        $totals = collect();


        $fnbIncomes = collect();
        $fnbTotals = collect();
        $fnbProducts = collect();

        if ($range == '7' || $range == '30') {
            $days = (int) $range - 1;

            for ($i = $days; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i)->toDateString();
                $dailyTransactions = Transaction::with('motor')
                    ->where('payment_status', 'paid')
                    ->whereDate('date', $date)
                    ->get();

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

                $pengeluaranPemilik = Pengeluaran::whereDate('date', $date)
                    ->whereIn('jenis_pengeluaran', ['Sabun', 'Air', 'Token', 'Uang Makan', 'Uang Sampah'])
                    ->sum('jumlah');

                $pendapatanBersih = $pendapatanPemilik - $pengeluaranPemilik;

                $dates->push(Carbon::parse($date)->translatedFormat('d M'));
                $incomes->push($pendapatanBersih);
                $totals->push($dailyTransactions->count());

                $fnbTransactions = Transaction::where('payment_status', 'paid')
                    ->whereDate('date', $date)
                    ->whereNotNull('food_items')
                    ->get();

                $dailyFnbRevenue = 0;
                $dailyFnbCount = 0;

                foreach ($fnbTransactions as $trx) {
                    $foodItems = $trx->food_items;
                    if (is_string($foodItems)) {
                        $foodItems = json_decode($foodItems, true);
                    }

                    if (!empty($foodItems) && is_array($foodItems)) {
                        foreach ($foodItems as $item) {
                            $quantity = $item['qty'] ?? 0;
                            $harga = $item['harga'] ?? 0;
                            $subtotal = $harga * $quantity;

                            $dailyFnbRevenue += $subtotal;
                            $dailyFnbCount += $quantity;
                        }
                    }
                }

                $fnbIncomes->push($dailyFnbRevenue);
                $fnbTotals->push($dailyFnbCount);
            }

        } elseif ($range == 'monthly') {
            $months = Transaction::where('payment_status', 'paid')
                ->selectRaw('DISTINCT DATE_FORMAT(date, "%Y-%m") as bulan')
                ->pluck('bulan');

            foreach ($months as $bulan) {
                $monthlyTransactions = Transaction::with('motor')
                    ->where('payment_status', 'paid')
                    ->whereRaw('DATE_FORMAT(date, "%Y-%m") = ?', [$bulan])
                    ->get();

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

                $pengeluaranPemilik = Pengeluaran::whereRaw('DATE_FORMAT(date, "%Y-%m") = ?', [$bulan])
                    ->whereIn('jenis_pengeluaran', ['Sabun', 'Air', 'Token', 'Uang Makan', 'Uang Sampah'])
                    ->sum('jumlah');

                $pendapatanBersih = $pendapatanPemilik - $pengeluaranPemilik;

                $dates->push(Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('M Y'));
                $incomes->push($pendapatanBersih);
                $totals->push($monthlyTransactions->count());

                $fnbTransactions = Transaction::where('payment_status', 'paid')
                    ->whereRaw('DATE_FORMAT(date, "%Y-%m") = ?', [$bulan])
                    ->whereNotNull('food_items')
                    ->get();

                $monthlyFnbRevenue = 0;
                $monthlyFnbCount = 0;

                foreach ($fnbTransactions as $trx) {
                    $foodItems = $trx->food_items;
                    if (is_string($foodItems)) {
                        $foodItems = json_decode($foodItems, true);
                    }

                    if (!empty($foodItems) && is_array($foodItems)) {
                        foreach ($foodItems as $item) {
                            $quantity = $item['qty'] ?? 0;
                            $harga = $item['harga'] ?? 0;
                            $subtotal = $harga * $quantity;

                            $monthlyFnbRevenue += $subtotal;
                            $monthlyFnbCount += $quantity;
                        }
                    }
                }

                $fnbIncomes->push($monthlyFnbRevenue);
                $fnbTotals->push($monthlyFnbCount);
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

        $fnbDataset = [
            'label' => 'Pendapatan F&B',
            'data' => $fnbIncomes,
            'borderColor' => '#4BC0C0',
            'backgroundColor' => 'rgba(75, 192, 192, 0.5)',
            'fill' => true,
            'tension' => 0.4,
        ];

        // Top 5 Products F&B
        $todayFnbTransactions = Transaction::where('payment_status', 'paid')
            ->whereDate('date', Carbon::today())
            ->whereNotNull('food_items')
            ->get();

        $todayFnbProducts = collect();

        foreach ($todayFnbTransactions as $trx) {
            $foodItems = $trx->food_items;
            if (is_string($foodItems)) {
                $foodItems = json_decode($foodItems, true);
            }

            if (!empty($foodItems) && is_array($foodItems)) {
                foreach ($foodItems as $item) {
                    $quantity = $item['qty'] ?? 0;
                    $harga = $item['harga'] ?? 0;
                    $subtotal = $harga * $quantity;
                    $productName = $item['nama_produk'] ?? 'Unknown';

                    $existingProduct = $todayFnbProducts->firstWhere('nama_produk', $productName);

                    if ($existingProduct) {
                        $todayFnbProducts = $todayFnbProducts->map(function ($p) use ($productName, $quantity, $subtotal) {
                            if ($p['nama_produk'] === $productName) {
                                $p['qty'] += $quantity;
                                $p['revenue'] += $subtotal;
                            }
                            return $p;
                        });
                    } else {
                        $todayFnbProducts->push([
                            'nama_produk' => $productName,
                            'qty' => $quantity,
                            'revenue' => $subtotal
                        ]);
                    }
                }
            }
        }

        $topProducts = $todayFnbProducts
        ->sortByDesc('revenue')
        ->take(5)
        ->values()
        ->map(function($product) {
            $food = Food::where('nama_produk', $product['nama_produk'])->first();
            $product['image'] = $food->image ?? 'default.png';
            return $product;
        });

        // Summary F&B
        $totalFnbRevenue = $fnbIncomes->sum();
        $totalFnbItems = $fnbTotals->sum();
        $totalFnbTransactions = Transaction::where('payment_status', 'paid')
            ->whereNotNull('food_items')
            ->count();
            

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
            'range',
            'fnbDataset',
            'fnbIncomes',
            'fnbTotals',
            'topProducts',
            'totalFnbRevenue',
            'totalFnbItems',
            'totalFnbTransactions'
        ));
    }
}