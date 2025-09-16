<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Motor;
use App\Models\Transaction;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
{
    $pagination = 10;
    $today = Carbon::today();
    // test tanggal
    // $today =  Carbon::parse('2025-08-14');

    // Base query
    $query = Transaction::select('id', 'date', 'tip', 'total', 'payment_method', 'payment_status', 'midtrans_payment_type', 'karyawan_id', 'motor_id')
        ->with([
            'karyawan:id,nama_karyawan',
            'motor:id,nama_motor,harga'
        ])
        ->whereDate('date', $today);

        // Pencarian
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('date', 'like', "%{$search}%")
                    ->orWhere('tip', 'like', "%{$search}%")
                    ->orWhere('total', 'like', "%{$search}%")
                    ->orWhere('payment_method', 'like', "%{$search}%")
                    ->orWhere('payment_status', 'like', "%{$search}%")
                    ->orWhere('midtrans_payment_type', 'like', "%{$search}%")
                    ->orWhereHas('karyawan', fn($q) => $q->where('nama_karyawan', 'like', "%{$search}%"))
                    ->orWhereHas('motor', fn($q) => $q->where('nama_motor', 'like', "%{$search}%"));
            });
        }

        // Ambil data paginated untuk tampilan
        $transactions = $query->orderBy('date', 'asc')->paginate($pagination)->withQueryString();


        $totalKeseluruhan = Transaction::whereDate('date', $today)
            ->whereNotIn('payment_status', ['pending', 'expired'])
            ->with('motor')
            ->get()
            ->sum(fn($trx) => ($trx->motor->harga ?? 0) + $trx->tip);

            return view('transactions.index', compact('transactions', 'totalKeseluruhan', 'today'))->with('i', ($request->input('page', 1) - 1) * $pagination);
}

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // get data karyawan
        $karyawans = Karyawan::where('aktif', true)->get(['id', 'nama_karyawan']);
        // get data motor
        $motors = Motor::get(['id', 'nama_motor', 'harga']);

        // tampilkan form add data
        return view('transactions.create', compact('karyawans', 'motors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
{
    // Validasi form
    $request->validate([
        'date'           => 'required|date',
        'karyawan'       => ['required',
            Rule::exists('karyawans', 'id')->where(function ($query):void {
                $query->where('aktif', true);
            }),
        ],
        'motor'          => 'required|exists:motors,id',
        'tip'            => 'nullable|numeric|min:0',
        'total'          => 'required|numeric|min:0', // Pastikan total adalah angka
        'payment_method' => 'required|in:cash,midtrans',
    ]);

    // Bersihkan angka untuk tip dan total (hilangkan titik sebagai pemisah ribuan)
    $tip = str_replace('.', '', $request->tip ?? 0);
    $total = str_replace('.', '', $request->total);

    // dd($request->all());

    // Buat data transaksi
    $transaction = Transaction::create([
        'date'            => $request->date,
        'karyawan_id'     => $request->karyawan,
        'motor_id'        => $request->motor,
        'tip'             => $tip,
        'total'           => $total,
        'payment_method'  => $request->payment_method,
        'payment_status'  => $request->payment_method === 'cash' ? 'paid' : 'pending',
        // 'midtrans_payment_type' => null,
        'midtrans_order_id' => $request->payment_method === 'midtrans' ? 'ORDER-' . Str::uuid() : null,
    ]);

    // Menangani payment_method 'cash' atau 'online'
    if ($request->payment_method === 'cash') {
        // Jika cash, langsung selesai
        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil disimpan.');
    } else {
        // Jika online, lanjutkan ke Midtrans
        return redirect()->route('midtrans.pay', $transaction->id);
    }
}
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        // get data transaction by ID
        $transaction = Transaction::findOrFail($id);
        // get data karyawan
        $karyawans = Karyawan::get(['id', 'nama_karyawan']);
        // get data motor
        $motors = Motor::get(['id', 'nama_motor', 'harga']);

        // tampilkan form edit data
        return view('transactions.edit', compact('transaction', 'karyawans', 'motors'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        // validasi form
        $request->validate([
            'date'     => 'required',
            'karyawan' => 'required|exists:karyawans,id',
            'motor'    => 'required|exists:motors,id',
            'tip'      => 'nullable|numeric|min:0',
            'total'    => 'required'
        ]);

        // get data by ID
        $transaction = Transaction::findOrFail($id);

        // update data
        $transaction->update([
            'date'        => $request->date,
            'karyawan_id' => $request->karyawan,
            'motor_id'    => $request->motor,
            'tip'         => str_replace('.', '', $request->tip ?? 0),
            'total'       => str_replace('.', '', $request->total)
        ]);

        // redirect ke halaman index dan tampilkan pesan berhasil ubah data
        return redirect()->route('transactions.index')->with(['success' => 'The transaction has been updated.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id): RedirectResponse
    {
        // get data by ID
        $transaction = Transaction::findOrFail($id);

        // delete data
        $transaction->delete();

        // redirect ke halaman index dan tampilkan pesan berhasil hapus data
        return redirect()->route('transactions.index')->with(['success' => 'The transaction has been deleted!']);
    }

    // public function deleteAll()
    // {
    //     // Menghapus semua transaksi
    //     Transaction::truncate();

    //     return redirect()->route('transactions.index')->with('success', 'All transactions have been deleted.');
    // }
    
    public function show($id)
{
    $transaction = Transaction::with('karyawan', 'motor')->findOrFail($id);

    return view('transactions.show', compact('transaction'));
}


    public function pay($id)
{
    $transaction = Transaction::findOrFail($id);

    // kalau metode pembayaran Midtrans → lempar ke PaymentController
    if ($transaction->payment_method === 'midtrans') {
        return redirect()->route('payments.pay', $transaction->id);
    }

    // kalau bukan Midtrans, tampilkan show biasa
    return redirect()->route('transactions.show', $transaction->id);
}


    public function success()
{
    return redirect()->route('transactions.index')
                     ->with('success', 'Pembayaran berhasil!');
}

}
