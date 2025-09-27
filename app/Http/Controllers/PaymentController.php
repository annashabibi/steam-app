<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\HelmTransaction;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Midtrans\Snap;
use Midtrans\CoreApi;
use Midtrans\Config;
use Midtrans\Notification;
use Illuminate\Support\Facades\Log;


class PaymentController extends Controller
{
    // Halaman pembayaran Midtrans
    public function pay(Transaction $transaction): View
{
    // Konfigurasi Midtrans
    Config::$serverKey = config('midtrans.server_key');
    Config::$isProduction = config('midtrans.is_production');
    Config::$isSanitized = true;
    Config::$is3ds = true;

    // Qris
    if ($transaction->midtrans_transaction_id && $transaction->qr_string) {
        $deeplink = $transaction->qr_string;
        $time_qr  = $transaction->expiry_time;

    } else {
        if (empty($transaction->midtrans_order_id)) {
            abort(400, 'Order ID tidak valid');
        }

        try {
            $params = [
                'payment_type' => 'qris',
                'transaction_details' => [
                    'order_id'     => $transaction->midtrans_order_id,
                    'gross_amount' => (int) $transaction->total,
                ],
                'item_details' => [[
                    'id'       => $transaction->id,
                    'price'    => (int) $transaction->total,
                    'quantity' => 1,
                    'name'     => 'Cuci Motor - ' . ($transaction->motor->nama_motor ?? 'Tanpa Nama'),
                ]],
                'customer_details' => [
                    'first_name' => $transaction->karyawan->nama_karyawan ?? 'Customer',
                ],
            ];

            $charge = CoreApi::charge($params);

            $qrString = $charge->qr_string ?? null;
            $expiry   = $charge->expiry_time ?? null;

            $transaction->update([
                'midtrans_payment_type'   => 'qris',
                'midtrans_transaction_id' => $charge->transaction_id ?? null,
                'payment_status'          => $charge->transaction_status ?? 'pending',
                'qr_string'               => $qrString,
                'qr_url'                  => $charge->actions[0]->url ?? null,
                'expiry_time'             => $expiry,
            ]);

            $deeplink = $qrString;
            $time_qr  = $expiry;

        } catch (\Exception $e) {
            Log::error('Midtrans QRIS Error: ' . $e->getMessage());
            abort(500, 'Midtrans Error: ' . $e->getMessage());
        }
    }

    return view('payments.pay', compact('transaction', 'deeplink', 'time_qr'));
}

    public function checkStatus(Transaction $transaction)
{
    // Ambil status terbaru dari database (webhook sudah update)
    return response()->json([
        'status' => $transaction->payment_status,
        'total' => $transaction->total,
        'message' => 'Status terbaru berhasil diambil'
    ]);
}

    public function webhook(Request $request)
{
    $payload = $request->all();

    Log::info('Midtrans webhook payload:', $payload);

    if (!isset($payload['order_id'], $payload['status_code'], $payload['gross_amount'], $payload['signature_key'])) {
        return response()->json(['message' => 'Invalid payload'], 400);
    }

    // Validasi signature
    $expectedSignature = hash('sha512',
        $payload['order_id'] .
        $payload['status_code'] .
        $payload['gross_amount'] .
        config('midtrans.server_key')
    );

    if ($payload['signature_key'] !== $expectedSignature) {
        Log::warning('Midtrans signature mismatch', [
            'expected' => $expectedSignature,
            'received' => $payload['signature_key'],
        ]);
        return response()->json(['message' => 'Invalid signature'], 403);
    }

    // Cek transaksi motor lebih dulu
    $transaction = Transaction::where('midtrans_order_id', $payload['order_id'])->first();

    if ($transaction) {
        $statusMap = [
            'settlement' => 'paid',
            'capture'    => 'paid',
            'pending'    => 'pending',
            'deny'       => 'failed',
            'cancel'     => 'failed',
            'expire'     => 'expired',
            'failure'    => 'failed',
        ];

        $transactionStatus = $payload['transaction_status'] ?? 'pending';

        $transaction->update([
            'payment_status'          => $statusMap[$transactionStatus] ?? $transactionStatus,
            'payment_method'          => 'midtrans',
            'midtrans_transaction_id' => $payload['transaction_id'] ?? null,
            'midtrans_payment_type'   => $payload['payment_type'] ?? null,
        ]);

        Log::info('Motor transaction updated via webhook', [
            'transaction_id' => $transaction->id,
            'status'         => $transaction->payment_status
        ]);

        return response()->json(['message' => 'Transaction updated']);
    }

    // Jika bukan motor, coba cari transaksi helm
    $helm = HelmTransaction::where('midtrans_order_id', $payload['order_id'])->first();

    if ($helm) {
        $statusMap = [
            'settlement' => 'paid',
            'capture'    => 'paid',
            'pending'    => 'pending',
            'deny'       => 'failed',
            'cancel'     => 'failed',
            'expire'     => 'expired',
            'failure'    => 'failed',
        ];

        $transactionStatus = $payload['transaction_status'] ?? 'pending';

        $helm->update([
            'payment_status'          => $statusMap[$transactionStatus] ?? $transactionStatus,
            'payment_method'          => 'online',
            'midtrans_transaction_id' => $payload['transaction_id'] ?? null,
            'midtrans_payment_type'   => $payload['payment_type'] ?? null,
        ]);

        Log::info('Helm transaction updated via webhook', [
            'helm_transaction_id' => $helm->id,
            'status'              => $helm->payment_status
        ]);

        return response()->json(['message' => 'Helm transaction updated']);
    }
}

public function payHelm(HelmTransaction $helm_transaction): View
{
    Config::$serverKey = config('midtrans.server_key');
    Config::$isProduction = config('midtrans.is_production');
    Config::$isSanitized = true;
    Config::$is3ds = config('midtrans.is_3ds');

    // Hitung total harga dari semua helm item
    $total = $helm_transaction->helmitems->sum('harga');

    // Gunakan QR yang sudah ada jika ada
    if ($helm_transaction->midtrans_transaction_id && $helm_transaction->qr_string) {
        $deeplink = $helm_transaction->qr_string;
        $time_qr  = $helm_transaction->expiry_time;
    } else {
        // Pastikan order_id valid
        if (empty($helm_transaction->midtrans_order_id)) {
            abort(400, 'Order ID tidak valid');
        }

        try {
            $params = [
                'payment_type' => 'qris',
                'transaction_details' => [
                    'order_id'     => $helm_transaction->midtrans_order_id,
                    'gross_amount' => (int) $total,
                ],
                'item_details' => $helm_transaction->helmitems->map(function ($item) {
                    return [
                        'id'       => $item->id,
                        'price'    => (int) $item->harga,
                        'quantity' => 1,
                        'name'     => 'Cuci Helm - ' . $item->type_helm,
                    ];
                })->toArray(),
                'customer_details' => [
                    'first_name' => $helm_transaction->nama_customer ?? 'Customer',
                ],
            ];

            // Charge ke Midtrans
            $charge = CoreApi::charge($params);

            // Ambil qr_string & expiry
            $qrString = $charge->qr_string ?? null;
            $expiry   = $charge->expiry_time ?? null;

            // Simpan ke DB
            $helm_transaction->update([
                'midtrans_payment_type'   => 'qris',
                'midtrans_transaction_id' => $charge->transaction_id ?? null,
                'payment_status'          => $charge->transaction_status ?? 'pending',
                'qr_url'                  => $charge->actions[0]->url ?? null,
                'qr_string'               => $qrString,
                'expiry_time'             => $expiry,
            ]);

            $deeplink = $qrString;
            $time_qr  = $expiry;

        } catch (\Exception $e) {
            Log::error('Midtrans QRIS Error (Helm): ' . $e->getMessage());
            abort(500, 'Midtrans Error: ' . $e->getMessage());
        }
    }

    // Load relasi helmitems
    $helm_transaction->load('helmitems');

    $isPaid = strtolower($helm_transaction->payment_status) === 'paid';

    return view('payments.pay_helm', [
        'helm_transaction' => $helm_transaction,
        'deeplink'         => $deeplink,
        'time_qr'          => $time_qr,
        'isPaid'           => $isPaid
    ]);
}

}