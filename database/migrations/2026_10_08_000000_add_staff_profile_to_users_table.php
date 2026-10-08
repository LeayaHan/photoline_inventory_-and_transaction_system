<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columns = [
            'employee_id' => fn (Blueprint $table) => $table->string('employee_id')->nullable()->unique()->after('id'),
            'phone' => fn (Blueprint $table) => $table->string('phone', 30)->nullable()->after('email'),
            'address' => fn (Blueprint $table) => $table->string('address')->nullable()->after('phone'),
            'position' => fn (Blueprint $table) => $table->string('position')->nullable()->after('address'),
            'date_hired' => fn (Blueprint $table) => $table->date('date_hired')->nullable()->after('position'),
            'emergency_contact_name' => fn (Blueprint $table) => $table->string('emergency_contact_name')->nullable()->after('date_hired'),
            'emergency_contact_phone' => fn (Blueprint $table) => $table->string('emergency_contact_phone', 30)->nullable()->after('emergency_contact_name'),
            'is_active' => fn (Blueprint $table) => $table->boolean('is_active')->default(true)->after('role'),
        ];

        foreach ($columns as $column => $definition) {
            if (! Schema::hasColumn('users', $column)) {
                Schema::table('users', $definition);
            }
        }
    }

    public function down(): void
    {
        $columns = [
            'employee_id',
            'phone',
            'address',
            'position',
            'date_hired',
            'emergency_contact_name',
            'emergency_contact_phone',
            'is_active',
        ];

        foreach ($columns as $column) {
            if (Schema::hasColumn('users', $column)) {
                Schema::table('users', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
