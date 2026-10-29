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
            $table->integer('old_stock')->nullable()->after('status');
            $table->decimal('old_selling_price', 10, 2)->nullable()->after('old_stock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batch_edit_requests', function (Blueprint $table) {
            $table->dropColumn(['old_stock', 'old_selling_price']);
        });
    }
};
