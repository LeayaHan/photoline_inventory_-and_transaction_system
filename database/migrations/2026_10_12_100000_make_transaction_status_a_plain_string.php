<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The status column was created as an ENUM of Pending/Claimed/Voided and later widened only to add
     * "Completed". The "In Progress" status used by Start Processing is not in that list, so on MySQL
     * starting an order (the step before payments can be recorded) is rejected. A plain string
     * accepts every status the app uses and keeps the data and the index as they are.
     */
    public function up(): void
    {
        Schema::table('customer_transactions', function (Blueprint $table) {
            $table->string('status', 20)->default('Pending')->change();
        });
    }

    public function down(): void
    {
        // Intentionally left as a string: turning it back into an enum could reject saved statuses.
    }
};
