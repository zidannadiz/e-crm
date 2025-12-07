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
        Schema::create('ecrm_chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('ecrm_orders')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade'); // Customer yang chat
            $table->foreignId('agent_id')->nullable()->constrained('users')->onDelete('set null'); // Admin/CS yang menangani
            $table->enum('status', ['waiting', 'active', 'ended'])->default('waiting');
            $table->timestamp('assigned_at')->nullable(); // Waktu ketika agent ditugaskan
            $table->timestamp('ended_at')->nullable(); // Waktu ketika chat selesai
            $table->timestamps();
            
            // Index untuk performa query
            $table->index('agent_id');
            $table->index('customer_id');
            $table->index('status');
            $table->index(['agent_id', 'status']); // Composite index untuk query load balancing
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ecrm_chat_sessions');
    }
};
