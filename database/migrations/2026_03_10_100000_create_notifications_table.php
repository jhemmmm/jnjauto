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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type');          // appointment_created, appointment_completed, appointment_cancelled, low_stock, status_change
            $table->string('title');
            $table->text('message');
            $table->string('icon')->nullable();        // FontAwesome icon class
            $table->string('icon_color')->nullable();   // Bootstrap color name (success, danger, etc.)
            $table->string('link')->nullable();         // Optional link to related page
            $table->json('data')->nullable();           // Extra payload (appointment_id, item_id, etc.)
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'read_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
