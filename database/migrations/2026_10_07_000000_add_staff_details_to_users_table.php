<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columns = [
            'first_name' => fn (Blueprint $table) => $table->string('first_name')->after('name'),
            'middle_name' => fn (Blueprint $table) => $table->string('middle_name')->nullable()->after('first_name'),
            'last_name' => fn (Blueprint $table) => $table->string('last_name')->after('middle_name'),
        ];

        foreach ($columns as $column => $definition) {
            if (! Schema::hasColumn('users', $column)) {
                Schema::table('users', $definition);
            }
        }
    }

    public function down(): void
    {
        $columns = array_values(array_filter(
            ['first_name', 'middle_name', 'last_name'],
            fn ($column) => Schema::hasColumn('users', $column)
        ));

        if ($columns !== []) {
            Schema::table('users', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
