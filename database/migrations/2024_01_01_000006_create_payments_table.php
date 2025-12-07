<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecrm_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('ecrm_invoices')->onDelete('cascade');
            $table->decimal('jumlah', 15, 2);
            $table->date('tanggal_pembayaran');
            $table->enum('metode_pembayaran', ['transfer', 'cash', 'kartu_kredit', 'e_wallet', 'lainnya'])->default('transfer');
            $table->string('bukti_pembayaran')->nullable();
            $table->text('catatan')->nullable();
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecrm_payments');
    }
};

