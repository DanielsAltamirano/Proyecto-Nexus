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
            $table->string('type')->default('edit')->after('batch_id'); // edit o delete
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batch_edit_requests', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
