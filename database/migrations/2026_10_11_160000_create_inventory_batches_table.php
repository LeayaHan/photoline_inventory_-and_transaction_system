<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->cascadeOnDelete();
            $table->string('batch_number', 80)->nullable();
            $table->unsignedInteger('quantity')->default(0);
            $table->date('received_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('notes', 500)->nullable();
            $table->timestamps();
            $table->index(['inventory_item_id', 'expiry_date']);
        });

        DB::table('inventory_items')->orderBy('id')->get()->each(function ($item) {
            if ((int) $item->quantity > 0) {
                DB::table('inventory_batches')->insert([
                    'inventory_item_id' => $item->id,
                    'batch_number' => 'OPENING-' . $item->item_code,
                    'quantity' => $item->quantity,
                    'received_date' => now()->toDateString(),
                    'expiry_date' => null,
                    'notes' => 'Opening stock carried over when batch tracking was enabled.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_batches');
    }
};
