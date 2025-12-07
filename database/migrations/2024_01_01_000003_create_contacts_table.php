<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecrm_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained('ecrm_clients')->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained('ecrm_projects')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('tipe', ['call', 'email', 'meeting', 'whatsapp', 'lainnya']);
            $table->string('subjek');
            $table->text('pesan');
            $table->dateTime('tanggal_kontak');
            $table->enum('arah', ['inbound', 'outbound'])->default('outbound');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecrm_contacts');
    }
};

