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
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('cash_payment', 10, 2)->default(0)->after('amount_paid');
            $table->decimal('card_payment', 10, 2)->default(0)->after('cash_payment');
            $table->decimal('transfer_payment', 10, 2)->default(0)->after('card_payment');
        });

        // Backfill existing data
        DB::statement("UPDATE sales SET cash_payment = total_price WHERE payment_method = 'efectivo'");
        DB::statement("UPDATE sales SET card_payment = total_price WHERE payment_method = 'tarjeta'");
        DB::statement("UPDATE sales SET transfer_payment = total_price WHERE payment_method = 'transferencia'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['cash_payment', 'card_payment', 'transfer_payment']);
        });
    }
};
