<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecrm_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('ecrm_clients')->onDelete('cascade');
            $table->string('nama_proyek');
            $table->text('deskripsi')->nullable();
            $table->enum('jenis_desain', [
                'logo', 
                'branding', 
                'web_design', 
                'ui_ux', 
                'print_design', 
                'packaging', 
                'social_media',
                'lainnya'
            ]);
            $table->enum('status', [
                'quotation', 
                'approved', 
                'in_progress', 
                'review', 
                'revision', 
                'completed', 
                'cancelled'
            ])->default('quotation');
            $table->decimal('budget', 15, 2)->nullable();
            $table->date('deadline')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->integer('revision_count')->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecrm_projects');
    }
};

