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
        Schema::table('receipt_batches', function (Blueprint $table) {
            $table->boolean('show_attachment')->default(true)->after('stamp_limit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipt_batches', function (Blueprint $table) {
            $table->dropColumn('show_attachment');
        });
    }
};
