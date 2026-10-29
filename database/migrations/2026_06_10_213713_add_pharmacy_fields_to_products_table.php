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
        Schema::table('products', function (Blueprint $table) {
            $table->string('active_ingredient')->nullable()->after('description');
            $table->string('presentation')->nullable()->after('active_ingredient');
            $table->boolean('prescription_required')->default(false)->after('presentation');
            $table->boolean('is_controlled')->default(false)->after('prescription_required');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['active_ingredient', 'presentation', 'prescription_required', 'is_controlled']);
        });
    }
};
