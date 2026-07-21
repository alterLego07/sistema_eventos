<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Marca si la invitación ya fue enviada al invitado (WhatsApp/email/manual).
     */
    public function up(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->boolean('invited')->default(false)->after('allowed_guests');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->dropColumn('invited');
        });
    }
};
