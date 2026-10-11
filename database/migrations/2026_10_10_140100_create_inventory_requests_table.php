<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_requests', function (Blueprint $table) {
            $table->id();
            $table->string('type');                       // add | edit | stock | delete
            $table->foreignId('inventory_item_id')->nullable()->constrained('inventory_items')->nullOnDelete();
            $table->string('item_name');                  // snapshot so history survives item deletion
            $table->json('payload')->nullable();          // the proposed values
            $table->text('reason')->nullable();
            $table->string('status')->default('pending'); // pending | approved | declined
            $table->foreignId('staff_id')->nullable()->constrained('staff')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('manager_remarks')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_requests');
    }
};
