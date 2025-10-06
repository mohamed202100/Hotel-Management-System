<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Change columns to nullable.
        // We only change the column definition to nullable, which is enough to fix the Duplicate Entry error.
        Schema::table('customers', function (Blueprint $table) {
            $table->string('phone_number')->nullable()->change();
            $table->string('passport_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert changes (if you need to rollback the migration)
        Schema::table('customers', function (Blueprint $table) {
            // Note: If you rollback, these fields will no longer be nullable.
            $table->string('phone_number')->nullable(false)->change();
            $table->string('passport_id')->nullable(false)->change();
            // We omit re-adding unique constraints since they were causing errors.
        });
    }
};
