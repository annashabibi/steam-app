<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\Gaji;
use App\Models\Karyawan;
use App\Models\Transaction;
use Illuminate\Validation\Rule;
use App\Models\Pengeluaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\View\View;

class GajiController extends Controller
{
    public function index():View
{   
    $karyawans = Karyawan::where('aktif', true)->get();
    return view('gaji.index', compact('karyawans'));
}

public function filter(Request $request):View
{
    $request->validate([
        'karyawan' => 'required|exists:karyawans,id',
    ]);

    $karyawan = Karyawan::where('aktif', true)->with(['transactions.motor', 'helmitems', 'pengeluaran'])
        ->findOrFail($request->karyawan);

    $startDate = Carbon::now()->startOfMonth();
    $endDate = Carbon::now()->endOfMonth();

    // Test untuk bulan sebelumnya
    // $startDate = Carbon::create(2025, 5, 1)->startOfMonth();
    // $endDate = Carbon::create(2025, 5, 1)->endOfMonth();

    $dataPerHari = [];

    $dataPerHari = [];

    for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
        $tanggal = $date->toDateString();

        // transaction
        $transactions = Transaction::where('karyawan_id', $karyawan->id)
            ->whereDate('date', $tanggal)
            ->where('payment_status', 'paid')
            ->with('motor')
            ->get();

        // helm items
        $helmItems = $karyawan->helmitems->filter(function ($item) use ($tanggal) {
            return optional($item->helmTransaction?->tanggal_cuci)
                ? Carbon::parse($item->helmTransaction->tanggal_cuci)->toDateString() === $tanggal
                : false;
        });

        // Pengeluaran
        $pengeluaran = $karyawan->pengeluaran
            ->filter(function ($item) use ($tanggal) {
                return \Carbon\Carbon::parse($item->date)->toDateString() === $tanggal;
            })
            ->sum('jumlah');

        $jumlahMotor = $transactions->count();
        $bonus = max(0, $jumlahMotor - 10) * 1000;
        $pendapatan = 0;

        foreach ($transactions as $trx) {
            $pendapatan += match ($trx->motor->category_id ?? 0) {
                1 => 5000,
                2 => 6000,
                3 => 7000,
                4 => 8000,
                default => 0,
            };
            $pendapatan += (int) $trx->tip;
        }

        foreach ($helmItems as $helm) {
            $pendapatan += match ($helm->type_helm ?? '') {
                'half_face' => 7000,
                'full_face' => 8000,
                default => 0,
            };
        }

        $pendapatan += $bonus;

        $pendapatanBersih = $pendapatan - $pengeluaran;

        $dataPerHari[] = [
            'date' => $tanggal,
            'pendapatan' => $pendapatan,
            'pengeluaran' => $pengeluaran,
            'pendapatan_bersih' => $pendapatanBersih,
        ];
    }

    $karyawans = Karyawan::where('aktif', true)->get();

    return view('gaji.index', [
        'karyawans' => $karyawans,
        'dataPerHari' => $dataPerHari,
        'selected' => $karyawan,
    ]);
}
    public function store(Request $request)
    {
        $validated = $request->validate([
            'karyawan_id' => ['required', Rule::exists('karyawans', 'id')->where('aktif', true),],
            'date' => 'required|date',
        ]);

        $karyawanId = $validated['karyawan_id'];
        $date = $validated['date'];

        $pendapatan = Transaction::where('karyawan_id', $karyawanId)
            ->whereDate('date', $date)
            ->sum('total');

        $pengeluaran = Pengeluaran::where('karyawan_id', $karyawanId)
            ->whereDate('date', $date)
            ->sum('jumlah');

        $pendapatan_bersih = $pendapatan - $pengeluaran;

        // Hindari duplikat
        $exists = Gaji::where('karyawan_id', $karyawanId)
            ->whereDate('date', $date)
            ->exists();

        if (!$exists) {
            Gaji::create([
                'karyawan_id' => $karyawanId,
                'date' => $date,
                'pendapatan' => $pendapatan,
                'pengeluaran' => $pengeluaran,
                'total_pendapatan_bersih' => $pendapatan_bersih,
            ]);
        }

        return redirect()->route('gaji.index')->with('success', 'Data gaji berhasil disimpan.');
    }

    public function printGaji($id)
        {
            $karyawan = Karyawan::where('aktif', true)->with(['transactions.motor', 'helmitems', 'pengeluaran'])
                ->findOrFail($id);

            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();

            $dataPerHari = [];

            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $tanggal = $date->toDateString();

            // transaction
            $transactions = Transaction::where('karyawan_id', $karyawan->id)
                ->whereDate('date', $tanggal)
                ->where('payment_status', 'paid')
                ->with('motor')
                ->get();

            // helm items
            $helmItems = $karyawan->helmitems->filter(function ($item) use ($tanggal) {
                return optional($item->helmTransaction?->tanggal_cuci)
                    ? Carbon::parse($item->helmTransaction->tanggal_cuci)->toDateString() === $tanggal
                    : false;
            });

            // Pengeluaran
            $pengeluaran = $karyawan->pengeluaran
                ->filter(function ($item) use ($tanggal) {
                    return \Carbon\Carbon::parse($item->date)->toDateString() === $tanggal;
                })
                ->sum('jumlah');

            $jumlahMotor = $transactions->count();
            $bonus = max(0, $jumlahMotor - 10) * 1000;
            $pendapatan = 0;

            foreach ($transactions as $trx) {
                $pendapatan += match ($trx->motor->category_id ?? 0) {
                    1 => 5000,
                    2 => 6000,
                    3 => 7000,
                    4 => 8000,
                    default => 0,
                };
                $pendapatan += (int) $trx->tip;
            }

            foreach ($helmItems as $helm) {
                $pendapatan += match ($helm->type_helm ?? '') {
                    'half_face' => 7000,
                    'full_face' => 8000,
                    default => 0,
                };
            }


            $pendapatan += $bonus;

            $pendapatanBersih = $pendapatan - $pengeluaran;

            $dataPerHari[] = [
                'date' => $tanggal,
                'pendapatan' => $pendapatan,
                'pengeluaran' => $pengeluaran,
                'pendapatan_bersih' => $pendapatanBersih,
            ];
        }

        $pdf = Pdf::loadView('gaji.print', [
            'karyawan' => $karyawan,
            'dataPerHari' => $dataPerHari,
        ])->setPaper('A4', 'portrait');

        return $pdf->stream('laporan-gaji-' . $karyawan->nama_karyawan . '.pdf');
    }
}
