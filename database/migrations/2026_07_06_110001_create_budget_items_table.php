<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * A budget item is a single expense line for an event (estimated vs
     * actual amount, plus payment tracking). company_id is denormalised
     * from the event for direct tenant scoping.
     */
    public function up(): void
    {
        Schema::create('budget_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->string('category')->comment('Catering, Decoración, Música, etc.');
            $table->string('concept');
            $table->decimal('estimated_amount', 12, 2)->default(0);
            $table->decimal('actual_amount', 12, 2)->nullable();
            $table->boolean('paid')->default(false);
            $table->date('paid_at')->nullable();
            $table->string('vendor')->nullable()->comment('Proveedor');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['event_id', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_items');
    }
};
