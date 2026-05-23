<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receipt_batches', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->string('source_file')->nullable();
            $table->string('start_proof')->default('BPU100');
            $table->string('number_mode')->default('renumber');
            $table->string('merge_mode')->default('by-proof');
            $table->unsignedBigInteger('stamp_limit')->default(5000000);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipt_batches');
    }
};
