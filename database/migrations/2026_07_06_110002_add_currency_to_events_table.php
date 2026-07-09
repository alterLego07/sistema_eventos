<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Each event defines the currency used across its budget.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('currency', 3)->default('MXN')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('currency');
        });
    }
};
