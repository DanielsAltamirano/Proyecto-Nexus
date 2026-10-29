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
        Schema::table('batch_edit_requests', function (Blueprint $table) {
            $table->date('old_expiration_date')->nullable()->after('old_selling_price');
            $table->date('requested_expiration_date')->nullable()->after('requested_selling_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batch_edit_requests', function (Blueprint $table) {
            $table->dropColumn(['old_expiration_date', 'requested_expiration_date']);
        });
    }
};
