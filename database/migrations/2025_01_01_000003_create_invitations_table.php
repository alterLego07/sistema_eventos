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
        Schema::create('invitations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->string('token', 16)->unique()->comment('Auto-generated unique token');
            $table->string('guest_name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->integer('table_number')->nullable();
            $table->integer('allowed_guests')->default(1)->comment('Max companions allowed');
            $table->boolean('confirmed')->default(false);
            $table->integer('confirmed_guests')->default(0);
            $table->timestamp('confirmed_at')->nullable();
            $table->text('dietary_restrictions')->nullable();
            $table->text('message')->nullable()->comment("Guest's personal message");
            $table->string('song_suggestion')->nullable();
            $table->timestamps();

            $table->index('event_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
