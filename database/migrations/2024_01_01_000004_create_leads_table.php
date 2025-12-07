<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecrm_leads', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('email')->nullable();
            $table->string('telepon')->nullable();
            $table->string('sumber')->nullable(); // website, referral, social media, dll
            $table->text('kebutuhan')->nullable();
            $table->enum('status', ['new', 'contacted', 'qualified', 'quotation', 'converted', 'lost'])->default('new');
            $table->decimal('estimated_value', 15, 2)->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('tanggal_kontak_terakhir')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecrm_leads');
    }
};

