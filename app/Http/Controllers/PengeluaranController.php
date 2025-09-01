<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use App\Models\Karyawan;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class PengeluaranController extends Controller
{
    public function index(Request $request)
{
    // Jumlah data per halaman
    $pagination = 10;

    // Query dasar dengan relasi ke tabel karyawan
    // test tanggal
    // $query = Pengeluaran::with('karyawan')->whereDate('date', Carbon::parse('2025-08-12'))->orderBy('date', 'desc');
    $query = Pengeluaran::with('karyawan')->whereDate('date', Carbon::today())->orderBy('date', 'desc');

    // Jika ada pencarian
    if ($request->search) {
        $query->whereAny(['jenis_pengeluaran', 'jumlah'], 'LIKE', '%' . $request->search . '%')
              ->orWhereHas('karyawan', function ($q) use ($request) {
                  $q->where('nama_karyawan', 'LIKE', '%' . $request->search . '%');
              });
    }

    // Paginate hasil pencarian atau semua data
    $pengeluaran = $query->paginate($pagination)->withQueryString();

    return view('pengeluaran.index', compact('pengeluaran'))->with('i', ($request->input('page', 1) - 1) * $pagination);
}


    public function create()
{
    $karyawans = Karyawan::where('aktif', true)->get();
    return view('pengeluaran.create', compact('karyawans'));
}

public function store(Request $request)
{
    $request->validate([
        'date' => 'required|date',
        'jenis_pengeluaran' => 'required|string',
        'jumlah' => 'required|string',
        'karyawan_id' => 'nullable|exists:karyawans,id',
    ]);

    // Konversi jumlah dari format Indonesia (52.000) ke format yang bisa dibaca database (52000)
    $jumlah = str_replace('.', '', $request->jumlah); // Hapus titik pemisah ribuan
    $jumlah = str_replace(',', '.', $jumlah); // Ganti koma desimal (jika ada)

    Pengeluaran::create([
        'date' => $request->date,
        'jenis_pengeluaran' => $request->jenis_pengeluaran,
        'jumlah' => (float) $jumlah, // Pastikan dalam bentuk angka
        'karyawan_id' => $request->jenis_pengeluaran === 'Kasbon' ? $request->karyawan_id : null,
    ]);

    return redirect()->route('pengeluaran.index')->with('success', 'Data pengeluaran berhasil ditambahkan!');
}
    public function edit(string $id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);
        $karyawans = Karyawan::where('aktif', true)->get(['id', 'nama_karyawan']);

        return view('pengeluaran.edit', compact('pengeluaran', 'karyawans'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'date'              => 'required|date',
            'jenis_pengeluaran' => 'required|string|max:255',
            'jumlah'            => 'required',
            'karyawan_id'       => 'nullable|exists:karyawans,id',
        ]);

        $pengeluaran = Pengeluaran::findOrFail($id);

        // update data satu per satu
        $pengeluaran->update([
            'date'              => $request->date,
            'jenis_pengeluaran' => $request->jenis_pengeluaran,
            'jumlah'            => str_replace('.', '', $request->jumlah), // format ke angka asli
            'karyawan_id'       => $request->karyawan_id,
        ]);

        return redirect()->route('pengeluaran.index')->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    public function destroy(Pengeluaran $pengeluaran)
    {
        $pengeluaran->delete();
        return redirect()->route('pengeluaran.index')->with('success', 'Pengeluaran berhasil dihapus.');
    }

    public function deleteAll()
    {
        // Menghapus semua transaksi
        Pengeluaran::truncate();

        return redirect()->route('pengeluaran.index')->with('success', 'Data Pengeluaran berhasil dihapus.');
    }
}
