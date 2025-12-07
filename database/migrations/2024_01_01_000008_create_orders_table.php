<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecrm_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('ecrm_clients')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Client yang memesan
            $table->string('nomor_order')->unique();
            $table->string('jenis_desain'); // logo, branding, web_design, dll
            $table->text('deskripsi');
            $table->text('kebutuhan')->nullable();
            $table->enum('status', ['pending', 'approved', 'in_progress', 'review', 'completed', 'cancelled'])->default('pending');
            $table->decimal('budget', 15, 2)->nullable();
            $table->date('deadline')->nullable();
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecrm_orders');
    }
};

