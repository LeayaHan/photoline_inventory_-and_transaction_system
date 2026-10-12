<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('inventory_audits')) {
            Schema::create('inventory_audits', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users');
                $table->date('audit_date');
                $table->string('status')->default('Ongoing');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Intentionally do not drop this table: it may have existed before this migration was recorded.
    }
};
