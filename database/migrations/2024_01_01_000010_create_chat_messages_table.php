<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecrm_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('ecrm_orders')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Pengirim
            $table->text('pesan');
            $table->foreignId('quick_reply_id')->nullable()->constrained('ecrm_quick_replies')->onDelete('set null');
            $table->boolean('is_ai_generated')->default(false);
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecrm_chat_messages');
    }
};

