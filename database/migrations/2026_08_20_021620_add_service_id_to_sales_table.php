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
            $table->foreignId('service_id')->nullable()->after('batch_id')->constrained()->nullOnDelete();
            // Make product_id and batch_id nullable if they are not already
            $table->unsignedBigInteger('product_id')->nullable()->change();
            $table->unsignedBigInteger('batch_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropColumn('service_id');
            // Can't reliably revert nullable changes without knowing original state, but we assume they were not nullable
            $table->unsignedBigInteger('product_id')->nullable(false)->change();
            $table->unsignedBigInteger('batch_id')->nullable(false)->change();
        });
    }
};
