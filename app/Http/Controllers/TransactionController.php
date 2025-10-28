<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Motor;
use App\Models\Transaction;
use App\Models\Food;
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
    
    $query = Transaction::select('id', 'date', 'tip', 'food_items', 'total', 'payment_method', 'payment_status', 'qr_url', 'qr_string', 'expiry_time', 'midtrans_payment_type', 'karyawan_id', 'motor_id')
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
        ->sum(function ($trx) {
            $hargaMotor = $trx->motor->harga ?? 0;
            $tip = $trx->tip ?? 0;
            $foodTotal = 0;

            if (!empty($trx->food_items) && is_array($trx->food_items)) {
                foreach ($trx->food_items as $food) {
                    $foodTotal += ($food['harga'] ?? 0) * ($food['qty'] ?? 1);
                }
            }

            return $hargaMotor + $tip + $foodTotal;
        });

        $totalTransaksi = Transaction::whereDate('date', $today)
        ->whereNotIn('payment_status', ['pending', 'expired'])
        ->with('motor')
        ->get()
        ->sum(function($trx) {
            $hargaMotor = $trx->motor->harga ?? 0;
            $tip = $trx->tip ?? 0;
            $foodTotal = 0;
            $foodItems = is_string($trx->food_items) ? json_decode($trx->food_items, true) : $trx->food_items;
            if (!empty($foodItems) && is_array($foodItems)) {
                foreach ($foodItems as $item) {
                    $foodTotal += ($item['harga'] ?? 0) * ($item['qty'] ?? 1);
                }
            }
            return $hargaMotor + $tip + $foodTotal;
        });

    return view('transactions.index', compact('transactions', 'totalKeseluruhan', 'totalTransaksi', 'today'))
        ->with('i', ($request->input('page', 1) - 1) * $pagination);
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
        // get data food
        $foods = Food::all();

        // tampilkan form add data
        return view('transactions.create', compact('karyawans', 'motors', 'foods'));
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
                Rule::exists('karyawans', 'id')->where(function ($query): void {
                    $query->where('aktif', true);
                }),
            ],
            'motor'          => 'required|exists:motors,id',
            'tip'            => 'nullable|numeric|min:0',
            'total'          => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,midtrans',

            // validasi opsional untuk makanan/minuman
            'food_items'     => ['nullable', 'string'], // string JSON
        ]);

        // Bersihkan angka untuk tip
        $tip = str_replace('.', '', $request->tip ?? 0);

        // Ambil harga motor
        $motor = Motor::findOrFail($request->motor);
        $motorHarga = $motor->harga;

        // Ambil data makanan jika ada
        $foodItems = null;
        $foodTotal = 0;

        if ($request->filled('food_items')) {
            $foodArray = json_decode($request->food_items, true); // decode JSON menjadi array

            if (!empty($foodArray)) {
                $foods = Food::whereIn('id', collect($foodArray)->pluck('id'))->get();

                $foodItems = $foods->map(function ($food) use ($foodArray, &$foodTotal) {
                    $qty = collect($foodArray)
                        ->firstWhere('id', $food->id)['qty'] ?? 1;

                    // Hitung total makanan
                    $foodTotal += $food->harga * $qty;

                    return [
                        'id' => $food->id,
                        'nama_produk' => $food->nama_produk,
                        'harga' => $food->harga,
                        'qty' => $qty,
                    ];
                })->toArray();
            }
        }

        // Hitung total keseluruhan (motor + tip + makanan)
        $total = $motorHarga + $tip + $foodTotal;

        // Buat data transaksi
        $transaction = Transaction::create([
            'date'            => $request->date,
            'karyawan_id'     => $request->karyawan,
            'motor_id'        => $request->motor,
            'tip'             => $tip,
            'total'           => $total,
            'payment_method'  => $request->payment_method,
            'payment_status'  => $request->payment_method === 'cash' ? 'paid' : 'pending',
            'midtrans_order_id' => $request->payment_method === 'midtrans' ? 'ORDER-' . Str::uuid() : null,
            'food_items'      => $foodItems ? json_encode($foodItems) : null,
        ]);

        // Kurangi stok makanan jika ada
        if (!empty($foodItems)) {
            foreach ($foodItems as $item) {
                Food::where('id', $item['id'])->decrement('qty', $item['qty']);
            }
        }

        // Redirect sesuai payment method
        if ($request->payment_method === 'cash') {
            return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil disimpan.');
        } else {
            return redirect()->route('midtrans.pay', $transaction->id);
        }
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


   public function transaction($id)
{
    $transaction = Transaction::with('karyawan', 'motor')->findOrFail($id);

    if ($transaction->payment_method !== 'midtrans') {
        return redirect()->route('transactions.show', $transaction->id);
    }

    $isPaid = in_array($transaction->payment_status, ['paid', 'settlement', 'capture']);

    return view('payments.pay', [
        'transaction' => $transaction,
        'isPaid'      => $isPaid,
    ]);
}

}
