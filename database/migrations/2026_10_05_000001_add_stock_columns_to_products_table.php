<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('products', 'quantity')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedInteger('quantity')->default(0)->after('unit');
            });
        }

        if (! Schema::hasColumn('products', 'reorder_level')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedInteger('reorder_level')->default(5)->after('quantity');
            });
        }
    }

    public function down(): void
    {
        $columns = array_values(array_filter(
            ['quantity', 'reorder_level'],
            fn ($column) => Schema::hasColumn('products', $column)
        ));

        if ($columns !== []) {
            Schema::table('products', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
