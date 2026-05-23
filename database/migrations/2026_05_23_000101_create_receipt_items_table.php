<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receipt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receipt_batch_id')->constrained()->cascadeOnDelete();
            $table->date('transaction_date')->nullable();
            $table->string('proof_number')->nullable();
            $table->string('activity_code')->nullable();
            $table->string('account_code')->nullable();
            $table->string('detail_code')->nullable();
            $table->text('description');
            $table->unsignedBigInteger('amount')->default(0);
            $table->string('receiver_name')->nullable();
            $table->string('warning')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipt_items');
    }
};
