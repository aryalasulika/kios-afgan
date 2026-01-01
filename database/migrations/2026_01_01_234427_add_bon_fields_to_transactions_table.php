<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Modify payment_method to support 'bon' (changing to string for flexibility)
            $table->string('payment_method')->change();

            // Add new columns
            $table->string('customer_name')->nullable()->after('change_amount');
            $table->enum('status', ['paid', 'unpaid'])->default('paid')->after('customer_name');
            $table->string('settlement_method')->nullable()->after('status'); // cash, qris
            $table->timestamp('settlement_at')->nullable()->after('settlement_method');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Revert payment_method to enum? (Might be risky if data exists, but for now just dropping columns)
            $table->dropColumn(['customer_name', 'status', 'settlement_method', 'settlement_at']);
            // We won't revert payment_method type to avoid data loss/issues
        });
    }
};
