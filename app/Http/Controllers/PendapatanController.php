<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Pengeluaran;
use App\Models\Transaction;
use App\Models\HelmItem;
use Illuminate\Http\Request;

class PendapatanController extends Controller
{
    public function index()
{
    $karyawanList = Karyawan::with([
        'transactions' => function ($q) {
            $q->where('payment_status', 'paid')->with('motor');
        },
        'pengeluaran',
        'helmitems'
    ])->get();

    $totalCucian = $karyawanList->sum(fn($k) => $k->transactions->count());

    $pendapatanKaryawan = $karyawanList->map(function ($karyawan) {
        $totalCucian = $karyawan->transactions->count();
        $totalTip = $karyawan->transactions->sum('tip');
        $totalPengeluaran = $karyawan->pengeluaran->sum('jumlah');
        $bonus = max(0, $totalCucian - 10) * 1000;

        $totalPendapatan = $this->hitungPendapatan($karyawan);
        $totalDenganBonus = $totalPendapatan + $totalTip + $bonus;

        return [
            'nama_karyawan'      => $karyawan->nama_karyawan,
            'motor_kecil'        => $this->hitungCucian($karyawan, 1),
            'motor_sedang'       => $this->hitungCucian($karyawan, 2),
            'motor_besar'        => $this->hitungCucian($karyawan, 3),
            'motor_sport'        => $this->hitungCucian($karyawan, 4),
            'helm_half_face'     => $this->hitungCucianHelm($karyawan, 'half_face'),
            'helm_full_face'     => $this->hitungCucianHelm($karyawan, 'full_face'),
            'total_helm'         => $karyawan->helmitems->count(),
            'total_cucian'       => $totalCucian,
            'total_tip'          => $totalTip,
            'bonus'              => $bonus,
            'total_pendapatan'   => $totalDenganBonus,
            'total_pengeluaran'  => $totalPengeluaran,
            'saldo_akhir'        => $totalDenganBonus - $totalPengeluaran,
        ];
    });

    // Pendapatan Pemilik
    $totalPendapatanPemilik = $karyawanList->sum(fn($karyawan) => $this->hitungPendapatanPemilik($karyawan));

    // Pengeluaran
    $pengeluaranToken = Pengeluaran::where('jenis_pengeluaran', 'Token')->sum('jumlah');
    $pengeluaranSampah = Pengeluaran::where('jenis_pengeluaran', 'Uang Sampah')->sum('jumlah');
    $pengeluaranSabun = Pengeluaran::where('jenis_pengeluaran', 'Sabun')->sum('jumlah');
    $pengeluaranAir = Pengeluaran::where('jenis_pengeluaran', 'Air')->sum('jumlah');
    $pengeluaranMakan = Pengeluaran::where('jenis_pengeluaran', 'Uang Makan')->sum('jumlah');
    $pengeluaranLainnya = Pengeluaran::whereNotIn('jenis_pengeluaran', [
        'Token', 'Uang Sampah', 'Kasbon', 'Sabun', 'Air', 'Uang Makan'
    ])->sum('jumlah');

    $totalPengeluaranPemilik = $pengeluaranToken + $pengeluaranSampah + $pengeluaranSabun + $pengeluaranAir + $pengeluaranMakan + $pengeluaranLainnya;
    $pendapatanBersihPemilik = $totalPendapatanPemilik - $totalPengeluaranPemilik;

    return view('pendapatan.index', compact(
        'pendapatanKaryawan',
        'totalPendapatanPemilik',
        'pengeluaranToken',
        'pengeluaranSampah',
        'pengeluaranSabun',
        'pengeluaranAir',
        'pengeluaranMakan',
        'pengeluaranLainnya',
        'totalCucian',
        'totalPengeluaranPemilik',
        'pendapatanBersihPemilik'
    ));
}


    /**
     * Hitung jumlah cucian berdasarkan kategori motor
     */
    private function hitungCucian($karyawan, $categoryId)
{
    return $karyawan->transactions->where('motor.category_id', $categoryId)->count();
}

private function hitungCucianHelm($karyawan, $type)
{
    return $karyawan->helmitems->where('type_helm', $type)->count();
}

private function hitungPendapatan($karyawan)
{
    $pendapatanMotor = $karyawan->transactions->sum(function ($transaction) {
        return match ($transaction->motor->category_id ?? 0) {
            1 => 5000, 2 => 6000, 3 => 7000, 4 => 8000, default => 0,
        };
    });

    $pendapatanHelm = $karyawan->helmitems->sum(function ($helm) {
        return match ($helm->type_helm ?? '') {
            'half_face' => 7000,
            'full_face' => 8000,
            default => 0,
        };
    });

    return $pendapatanMotor + $pendapatanHelm;
}

private function hitungPendapatanPemilik($karyawan)
{
    $pendapatanMotor = $karyawan->transactions->sum(function ($transaction) {
        return match ($transaction->motor->category_id ?? 0) {
            1 => 10000, 2 => 12000, 3 => 13000, 4 => 14000, default => 0,
        };
    });

    $pendapatanHelm = $karyawan->helmitems->sum(function ($helm) {
        return match ($helm->type_helm ?? '') {
            'half_face' => 10000,
            'full_face' => 12000,
            default => 0,
        };
    });

    return $pendapatanMotor + $pendapatanHelm;
}

}