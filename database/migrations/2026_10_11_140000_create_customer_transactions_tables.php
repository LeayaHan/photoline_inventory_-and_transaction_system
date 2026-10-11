<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('control_number', 20)->unique();
            $table->foreignId('staff_id')->nullable()->constrained('staff')->nullOnDelete();
            $table->string('customer_name', 100);
            $table->date('transaction_date');
            $table->enum('status', ['Pending', 'Claimed', 'Voided'])->default('Pending');
            $table->string('void_reason', 255)->nullable();
            $table->timestamp('claimed_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'transaction_date']);
            $table->index('customer_name');
        });

        Schema::create('transaction_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('customer_transactions')->cascadeOnDelete();
            $table->string('service_type', 50);
            $table->string('description', 150)->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('amount', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_details');
        Schema::dropIfExists('customer_transactions');
    }
};
