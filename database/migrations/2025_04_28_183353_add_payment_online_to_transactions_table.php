<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('payment_status')->nullable()->after('total');
            $table->string('payment_method')->nullable()->after('payment_status');
            $table->string('midtrans_order_id')->nullable()->after('payment_method');
            $table->string('midtrans_transaction_id')->nullable()->after('midtrans_order_id');
            $table->string('midtrans_payment_type')->nullable()->after('midtrans_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('payment_status');
            $table->dropColumn('payment_method');
            $table->dropColumn('midtrans_order_id');
            $table->dropColumn('midtrans_transaction_id');
            $table->dropColumn('midtrans_payment_type');
        });
    }
};
