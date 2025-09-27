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
        Schema::table('helm_transactions', function (Blueprint $table) {
            $table->text('qr_url')->nullable()->after('midtrans_payment_type');
            $table->text('qr_string')->nullable()->after('qr_url');
            $table->timestamp('expiry_time')->nullable()->after('qr_string');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('helm_transactions', function (Blueprint $table) {
            $table->dropColumn(['qr_url', 'qr_string', 'expiry_time']);
        });
    }
};
