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
            $table->enum('payment_method', ['cash', 'qris'])->default('cash')->after('total_amount');
            $table->decimal('cash_received', 12, 2)->nullable()->after('payment_method');
            $table->decimal('change_amount', 12, 2)->default(0)->after('cash_received');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'cash_received', 'change_amount']);
        });
    }
};
