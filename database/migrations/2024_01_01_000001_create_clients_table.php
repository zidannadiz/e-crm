<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecrm_clients', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('email')->unique();
            $table->string('telepon')->nullable();
            $table->string('perusahaan')->nullable();
            $table->text('alamat')->nullable();
            $table->enum('tipe', ['individu', 'perusahaan'])->default('individu');
            $table->enum('status', ['prospek', 'aktif', 'nonaktif', 'blacklist'])->default('prospek');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecrm_clients');
    }
};

