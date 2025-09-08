<?php

namespace App\Http\Controllers;

use App\Models\HelmTransaction;
use App\Models\HelmItem;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HelmTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // Tampilkan daftar transaksi helm
    public function index(Request $request): View
{
    // jumlah data yang ditampilkan per paginasi halaman
    $pagination = 10;

    if ($request->search) {
        // menampilkan pencarian data
        $helm_transaction = HelmTransaction::select('id', 'nama_customer', 'tanggal_cuci', 'tanggal_selesai', 'payment_method', 'payment_status', 'midtrans_payment_type')
            ->with(['helmitems:helm_transaction_id,nama_helm,type_helm,karyawan_id,harga', 'helmitems.karyawan:id,nama_karyawan'])
            ->where(function($query) use ($request) {
                $query->whereAny(['nama_customer', 'payment_method', 'payment_status', 'midtrans_payment_type'], 'LIKE', '%' . $request->search . '%')
                    ->orWhereHas('helmitems', function($subQuery) use ($request) {
                        $subQuery->where('nama_helm', 'LIKE', '%' . $request->search . '%')
                            ->orWhere('type_helm', 'LIKE', '%' . $request->search . '%');
                    })
                    ->orWhereHas('helmitems.karyawan', function($subQuery) use ($request) {
                        $subQuery->where('nama_karyawan', 'LIKE', '%' . $request->search . '%');
                    });
            })
            ->paginate($pagination)
            ->withQueryString();
    } else {
        // menampilkan semua data
        $helm_transaction = HelmTransaction::select('id', 'nama_customer', 'tanggal_cuci', 'tanggal_selesai', 'payment_method', 'payment_status', 'midtrans_payment_type')
            ->with(['helmitems:helm_transaction_id,nama_helm,type_helm,karyawan_id,harga', 'helmitems.karyawan:id,nama_karyawan'])
            ->latest()
            ->paginate($pagination);
    }

    // tampilkan data ke view
    return view('helms.index', compact('helm_transaction'))->with('i', ($request->input('page', 1) - 1) * $pagination);
}

    // Tampilkan form tambah transaksi helm
    public function create()
    {
        $karyawans = Karyawan::where('aktif', true)->get();
        return view('helms.create', compact('karyawans'));
    }

    // Simpan transaksi helm ke database
    public function store(Request $request)
{
    $request->validate([
        'nama_customer' => 'required|string|max:255',
        'tanggal_cuci' => 'required|date',
        'tanggal_selesai' => 'required|date|after_or_equal:tanggal_cuci',
        'nama_helm.*' => 'required|string|max:255',
        'type_helm.*' => 'required|in:half_face,full_face',
        'karyawan_id.*' => 'required|exists:karyawans,id',
        'payment_method' => 'required|in:offline,online',
    ]);

    // Hitung total harga
    $totalHarga = 0;
    foreach ($request->input('type_helm') as $type) {
        $totalHarga += $type === 'half_face' ? 18000 : 20000;
    }

    // Buat transaksi
    $helm = HelmTransaction::create([
        'nama_customer' => $request->nama_customer,
        'tanggal_cuci' => $request->tanggal_cuci,
        'tanggal_selesai' => $request->tanggal_selesai,
        'payment_status' => $request->payment_method === 'offline' ? 'paid' : 'pending',
        'payment_method' => $request->payment_method,
        // 'midtrans_payment_type' => null,
        'total_harga' => $totalHarga,
        'midtrans_order_id' => $request->payment_method === 'online' ? 'HELM-' . Str::uuid() : null,
    ]);

    // Simpan helm items
    foreach ($request->nama_helm as $i => $nama) {
        HelmItem::create([
            'helm_transaction_id' => $helm->id,
            'nama_helm' => $nama,
            'type_helm' => $request->type_helm[$i],
            'harga' => $request->type_helm[$i] === 'half_face' ? 18000 : 20000,
            'karyawan_id' => $request->karyawan_id[$i],
        ]);
    }

    // Jika offlineredirect ke index
    if ($request->payment_method === 'offline') {
        return redirect()->route('helms.index')->with('success', 'Transaksi berhasil disimpan');
    }

    // Jika online → redirect ke Midtrans
    return redirect()->route('helms.pay', $helm->id);
}

    public function destroy($id): RedirectResponse
        {
            // get data by ID
            $helm_transaction = HelmTransaction::findOrFail($id);

            // delete data
            $helm_transaction->delete();

            // redirect ke halaman index dan tampilkan pesan berhasil hapus data
            return redirect()->route('helms.index')->with(['success' => 'The transaction has been deleted!']);
        }

public function deleteAll()
{
    try {
        foreach (HelmTransaction::all() as $helm) {
            $helm->helmItems()->delete();
            $helm->delete();
        }

        return redirect()->route('helms.index')->with('success', 'Semua data berhasil dihapus.');
    } catch (\Exception $e) {
        return redirect()->route('helms.index')->with('error', 'Gagal menghapus: ' . $e->getMessage());
    }
}
}