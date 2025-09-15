<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\HelmTransaction;
use Illuminate\Http\Request;
use Illuminate\View\View;
// use Midtrans\Snap;
use Midtrans\CoreApi;
use Midtrans\Config;
// use Midtrans\Notification;
use Illuminate\Support\Facades\Log;


class PaymentController extends Controller
{
    // Halaman pembayaran Midtrans
    public function pay($id): View
{
    // Ambil transaction berdasarkan ID
    $transaction = Transaction::findOrFail($id);
    
    // Konfigurasi Midtrans
    Config::$serverKey = config('midtrans.server_key');
    Config::$isProduction = config('midtrans.is_production');
    Config::$isSanitized = config('midtrans.is_sanitized');
    Config::$is3ds = config('midtrans.is_3ds');

    // Order ID wajib ada
    if (empty($transaction->midtrans_order_id)) {
        abort(400, 'Order ID tidak valid');
    }

    $qrUrl = null;
    $deeplinkUrl = null;
    $errorMessage = null;

    // Jika belum ada payment_url atau masih pending, buat request baru
    if (empty($transaction->midtrans_payment_url) && $transaction->payment_status === 'pending') {
        
        // Payload Core API khusus GoPay
        $params = [
            'payment_type' => 'gopay',
            'transaction_details' => [
                'order_id' => $transaction->midtrans_order_id,
                'gross_amount' => (int) $transaction->total,
            ],
            'gopay' => [
                'enable_callback' => true,
                'callback_url' => route('transactions.index'),
            ],
            'item_details' => [[
                'id' => (string) $transaction->id,
                'price' => (int) $transaction->total,
                'quantity' => 1,
                'name' => 'Cuci Motor - ' . ($transaction->motor->nama_motor ?? 'Tanpa Nama'),
            ]],
            'customer_details' => [
                'first_name' => 'Customer',
            ]
        ];

        try {
            $chargeResponse = CoreApi::charge($params);

            // Log response (convert ke JSON biar aman)
            \Log::info('Midtrans Response: ' . json_encode($chargeResponse));

            if (isset($chargeResponse->status_code) && $chargeResponse->status_code == '201') {
                // Ambil QR & Deeplink dari response
                if (!empty($chargeResponse->actions) && is_array($chargeResponse->actions)) {
                    foreach ($chargeResponse->actions as $action) {
                        if (($action->name ?? '') === 'generate-qr-code') {
                            $qrUrl = $action->url ?? null;
                        }
                        if (($action->name ?? '') === 'deeplink-redirect') {
                            $deeplinkUrl = $action->url ?? null;
                        }
                    }
                }

                // Simpan ke DB
                $transaction->update([
                    'midtrans_payment_url'   => $qrUrl,
                    'midtrans_payment_type'  => 'gopay',
                    'midtrans_transaction_id'=> $chargeResponse->transaction_id ?? null,
                ]);
            } else {
                $errorMessage = 'Gagal membuat pembayaran: ' . ($chargeResponse->status_message ?? 'Unknown error');
                \Log::error('Midtrans Error Response: ' . json_encode($chargeResponse));
            }

        } catch (\Exception $e) {
            $errorMessage = 'Midtrans Error: ' . $e->getMessage();
            \Log::error('Midtrans Core API Exception: ' . $e->getMessage());
        }
    } else {
        // Jika sudah ada payment_url, gunakan yang sudah ada
        $qrUrl = $transaction->midtrans_payment_url;
    }

    $isPaid = strtolower($transaction->payment_status) === 'paid';

    return view('payments.pay', compact('transaction', 'qrUrl', 'deeplinkUrl', 'isPaid', 'errorMessage'));
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
    Config::$isSanitized = config('midtrans.is_sanitized');
    Config::$is3ds = config('midtrans.is_3ds');

    // Validasi order_id
    if (
        $helm_transaction->midtrans_payment_type !== null &&
        empty($helm_transaction->midtrans_order_id)
    ) {
        abort(400, 'Order ID tidak valid');
    }

    // Hitung total harga dari semua helm item
    $total = $helm_transaction->helmitems->sum('harga');

    $params = [
        'enabled_payments' => ['qris', 'gopay', 'shopeepay', 'bank_transfer'],
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

    if (!$helm_transaction->midtrans_snap_token) {
        try {
            $snapToken = Snap::getSnapToken($params);
            $helm_transaction->update(['midtrans_snap_token' => $snapToken]);
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Token Error (Helm): ' . $e->getMessage());
            abort(500, 'Midtrans Error: ' . $e->getMessage());
        }
    } else {
        $snapToken = $helm_transaction->midtrans_snap_token;
    }

    $helm_transaction->load('helmitems');

    $isPaid = strtolower($helm_transaction->payment_status) === 'paid';

    return view('payments.pay_helm', [
        'helm_transaction' => $helm_transaction,
        'snapToken' => $snapToken,
        'isPaid' => $isPaid
    ]);
}
}