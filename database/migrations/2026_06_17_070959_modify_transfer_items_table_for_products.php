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
        Schema::table('transfer_items', function (Blueprint $table) {
            // Drop foreign key before altering column if necessary
            $table->dropForeign(['batch_id']);
            
            $table->foreignId('batch_id')->nullable()->change();
            
            // Re-add foreign key constraint with onDelete('cascade') as before
            $table->foreign('batch_id')->references('id')->on('batches')->onDelete('cascade');
            
            $table->foreignId('product_id')->nullable()->after('transfer_id')->constrained('products')->onDelete('cascade');
            $table->integer('quantity_requested')->nullable()->after('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transfer_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn(['product_id', 'quantity_requested']);
            
            // Revert batch_id to not null (might fail if data has nulls)
            $table->dropForeign(['batch_id']);
            $table->foreignId('batch_id')->nullable(false)->change();
            $table->foreign('batch_id')->references('id')->on('batches')->onDelete('cascade');
        });
    }
};
