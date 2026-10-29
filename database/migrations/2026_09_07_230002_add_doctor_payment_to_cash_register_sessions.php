<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cash_register_sessions', function (Blueprint $table) {
            $table->timestamp('doctor_paid_at')->nullable()->after('notes');
            $table->decimal('doctor_payment_amount', 10, 2)->nullable()->after('doctor_paid_at');
        });
    }

    public function down(): void
    {
        Schema::table('cash_register_sessions', function (Blueprint $table) {
            $table->dropColumn(['doctor_paid_at', 'doctor_payment_amount']);
        });
    }
};
