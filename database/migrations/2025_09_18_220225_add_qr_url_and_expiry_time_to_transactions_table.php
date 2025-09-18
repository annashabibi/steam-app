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
            $table->string('qr_url')->nullable()->after('midtrans_payment_type');
            $table->timestamp('expiry_time')->nullable()->after('qr_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['qr_url', 'expiry_time']);
        });
        });
    }
};
