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
        Schema::create('events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('template_id')->nullable()->constrained('templates')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->date('event_date');
            $table->time('event_time');
            $table->string('location')->nullable();
            $table->string('location_url')->nullable()->comment('Google Maps link');
            $table->string('cover_image')->nullable();
            $table->json('settings')->nullable()->comment('RSVP config, custom fields, messages');
            $table->json('template_config')->nullable()->comment('Per-event template overrides (colors, fonts, sections)');
            $table->string('status')->default('draft')->comment('Values: draft, published, cancelled, completed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
